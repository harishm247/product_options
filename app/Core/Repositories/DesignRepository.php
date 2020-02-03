<?php

namespace App\Core\Repositories;

use App\Core\Interfaces\DesignInterface;
use App\Core\Repositories\BaseRepository;
use App\Core\Models\EZT2\User\Project\CustomizableDesign;
use App\Core\Models\OrderCore\DesignFile;
use App\Core\Models\OrderCore\Invoice\Item\DesignFile as InoviceItemDesignFile;
use App\Core\Models\OrderCore\DesignFile\UserProject as UserProjectDesignFile;
use App\Core\Models\EZT2\Design\Customizable\Design as EZT2CustomizableDesign;
use App\Core\Models\EZT2\User\Project;
use DB;
class DesignRepository extends BaseRepository implements DesignInterface
{
  protected $userProjectCustomizableModel;
  protected $designFileModel;
  protected $inoviceItemDesignFileModel;
  protected $userProjectDesignFileModel;
  protected $eZT2CustomizableDesignModel;
  protected $userProjectModel;
  
  public function __construct(
      CustomizableDesign $userProjectCustomizableModel,
      DesignFile $designFileModel,
      InoviceItemDesignFile $inoviceItemDesignFileModel,
      UserProjectDesignFile $userProjectDesignFileModel,
      EZT2CustomizableDesign $eZT2CustomizableDesignModel,
      Project $userProjectModel
      )
  {
    $this->userProjectCustomizableModel = $userProjectCustomizableModel;
    $this->designFileModel              = $designFileModel;
    $this->inoviceItemDesignFileModel   = $inoviceItemDesignFileModel;
    $this->userProjectDesignFileModel   = $userProjectDesignFileModel;
    $this->eZT2CustomizableDesignModel  = $eZT2CustomizableDesignModel;
    $this->userProjectModel             = $userProjectModel;
    
  }
  /*
    function is for set static data values for design into session 
    @author harish meshram
  */
  public function setStaticSessionData()
  {
    session()->flush();
    $session = [
                'useSessionCurrentDesigns'=>true,
                'frontId' => '2316632',
                'frontType' => 'customizable',
                'frontThumb' => 'http://upload.expresscopy.com/static/img/user/2019/11/25/00/df-5ddb8ace03f35/small-thumb.jpg',
                'frontProductName' => 'Jumbo Postcard (4.25" x 5.6")',
                'frontProductId' => '2',
                'frontOrientation' => '',
                'frontBackRequired' => '',
                'backId' => '2316631',
                'backType' => 'customizable',
                'backThumb' => 'http://upload.expresscopy.com/static/img/user/2019/11/25/00/df-5ddb8acbe6de5/small-thumb.jpg',
                'backProductId' => '2',
                'backOrientation' => ''  
                //'invoiceItemId'=>4
            ];      
    session($session);
  }
  /**
     * getSelectedDesigns
     *
     * Logic wrapper to retrieve selected designs first from the session and then from the
     * design files attached to the current invoice item if session values are not present.
     *
     * @static
     * @access public
     * @return array $selectedDesigns Array of selected designs.
     */
    public function getSelectedDesigns()
    {
        $this->setStaticSessionData();
        $selectedDesigns = array();
        /* code for fectch data from laravel session and set this on zend code start here */
        $frontId = 0;
        $backId  = 0;
        $useSessionCurrentDesigns = false;

        $frontId = session('frontId');
        $backId  = session('backId');
        $useSessionCurrentDesigns =session('useSessionCurrentDesigns');

        if (($frontId || $backId) && $useSessionCurrentDesigns) {
            if ($frontId) {
                $selectedDesigns['front'] = array(
                    'id'           => session('frontId'),
                    'type'         => session('frontType'),
                    'thumb'        => session('frontThumb'),
                    'productName'  => session('frontProductName'),
                    'productId'    => session('frontProductId'),
                    'orientation'  => session('frontOrientation'),
                    'side'         => 'front',
                    'backRequired' => session('frontBackRequired')
                );
            }
            if ($backId) {
                $selectedDesigns['back'] = array(
                    'id'          => session('backId'),
                    'type'        => session('backType'),
                    'thumb'       => session('backThumb'),
                    'productId'   => session('backProductId'),
                    'orientation' => session('backOrientation'),
                    'side'        => 'back',
                    'displayText' => session('backProductId') == 11 ? 'inside' : 'back'
                );
            }
        } else {
            // prior to accept within editor it is possible to not have an invoice item.
            if (session()->has('invoiceItemId')) {
                $inoviceItemDesignFile = $this->inoviceItemDesignFileModel
                                ->with('designFile')->where([
                                    'invoice_item_id'=>session('invoiceItemId')
                                ])->get();
                foreach ($inoviceItemDesignFile as $key => $itemDesignFile) {
                    $designData  = $this->loadDesign($itemDesignFile);
                    $selectedDesigns[$designData['side']] = $designData;
                }
                return $selectedDesigns;
            }else{
                return $selectedDesigns;
            }
        }
        krsort($selectedDesigns);
        return $selectedDesigns;
    }
    public function loadDesign($itemDesignFile){
        $selectedData = [];
        $page = $itemDesignFile->designFile->page;
        $selectedData['type'] = $itemDesignFile->designFile->type;
        $selectedData['productId'] = $itemDesignFile->designFile->product_print_id;
        $selectedData['id'] = $itemDesignFile->designFile->id;
        
        if($page == 1){
            $selectedData['side'] = 'front';
        }else{
            $selectedData['side'] = 'back';
        }

         switch ($selectedData['type']) {
            case 'customizable':
                $design = $this->eZT2CustomizableDesignModel->where([
                    'id'=>$selectedData['id']
                ])->first();
                /* for local setup here is a static url for preview need to change it
                    $thumbPrefix = config("app.server_config")['designFile']['thumbRootURL']
                */
                $selectedData['thumb'] = "https://upload.expresscopy.com/static/img/user/path/".$design->small_thumb_path;
                $selectedData['large_thumb'] = "https://upload.expresscopy.com/static/img/user/path/".$design->large_thumb_path;
                $selectedData['orientation'] = $design->orientation;
                $selectedData['productId'] = $design->product_print_id;
                break;
            case 'unfinished':
                $id =$selectedData['id'];
                 $customizableDesignRow = $this->eZT2CustomizableDesignModel->whereHas('customizableDesigns.project',function($query)  use ($id, $page){
                    $query->where('user_project.id',$id);
                })
                ->where([
                    'page'=>$page
                ])->first();  
                
                /* for local setup here is a static url for preview need to change it
                    $thumbPrefix = config("app.server_config")['designFile']['thumbRootURL']
                */
                if (!is_null($customizableDesignRow->small_thumb_path)) {
                    $selectedData['thumb'] = $customizableDesignRow->small_thumb_path;
                } else {
                    /* url replace by this variable $thumbPrefix */
                    $selectedData['thumb'] = "https://upload.expresscopy.com/static/img/user/path/".$customizableDesignRow->small_thumb_path;
                }
                $selectedData['large_thumb'] = "https://upload.expresscopy.com/static/img/user/path/".$customizableDesignRow->large_thumb_path;
                
                $selectedData['orientation'] = $customizableDesignRow->orientation;
                $selectedData['productId'] = $customizableDesignRow->productPrintId;
                break;
            case 'customized':
                $designFile = $this->userProjectDesignFileModel->with(['designFile' => function($query) use ($productIds) {
                        $query->where('design_file.page', $page);
                    }])->where([
                        'user_project_id' => $selectedData['id']
                    ])->first();
                /* for local setup here is a static url for preview need to change it
                    $large_thumb = config("app.server_config")['imageServer']['userBaseURL']
                */
                $selectedData['thumb'] = "https://upload.expresscopy.com/static/img/user/path/".$designFile->path.'/'.$designFile->small_thumb;
                break;
            default:
                /* for local setup here is a static url for preview need to change it
                    $large_thumb = config("app.server_config")['imageServer']['userBaseURL']
                */
                $selectedData['thumb'] = "https://upload.expresscopy.com/static/img/user/path/".$itemDesignFile->designFile->path.'/'.$itemDesignFile->designFile->small_thumb;
                break;
        }

        return $selectedData;
    }
    
    public function getLargeThumbDesign($designFileId, $side, $type)
    {   
        if ('customized' == $type) {
            $customizedDesign = $this->userProjectCustomizableModel->where([
                    'user_project_id' => $designFileId,
                    'page'=>$side,
                ])->first();
            return $customizedDesign->large_thumb;
        } else {
            if ('uploaded' == $type) {
                
                $file = $this->designFileModel->where([
                    'id' => $designFileId,
                ])->first();
                
                if ($file) {
                    /* for local setup here is a static url for preview need to change it
                        $large_thumb = config("app.server_config")['imageServer']['userBaseURL']
                    */
                    $large_thumb = "https://upload.expresscopy.com/static/img/user/" .
                        '/' . $file->path .
                        '/' . $file->large_thumb;
                        // '/' . $file->file; old condito
                    return $large_thumb;
                }
            } else {
                /*if (session('userProjectId')) {
                  need to code for load function
                     $designManager = new Expresscopy_Design_Manager;
                     $designManager->load(session('userProjectId'), FALSE);

                    if ($pdfFile = $designManager->getPdfPreview()) {
                        $this->view->file = Zend_Registry::get('config')->uri->mpowertmp .
                            '/' . $pdfFile['previewPath'];
                        if ('uploaded' != $type) {
                            $this->view->file .= '#page=' . $side;
                        }
                    }
                } else {
                    $this->view->file = '/ec/ajax/design/preview-pdf/designId/' . $id;
                } */
                /*Need To implement it*/
                return "https://images-eu.ssl-images-amazon.com/images/G/31/img19/Sports/GW_Desktop/1199101_379x304_Compressed._SY304_CB448278349_.jpg";
            }
        }
    }
}
