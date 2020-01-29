<?php

namespace App\Core\Repositories;

use App\Core\Interfaces\DesignInterface;
use App\Core\Repositories\BaseRepository;
use App\Core\Models\EZT2\User\Project\CustomizableDesign;
use App\Core\Models\OrderCore\DesignFile;
use App\Core\Models\OrderCore\Invoice\Item\DesignFile as InoviceItemDesignFile;
use DB;
class DesignRepository extends BaseRepository implements DesignInterface
{
  protected $userProjectCustomizableModel;
  protected $designFileModel;
  protected $inoviceItemDesignFileModel;
  public function __construct(
      CustomizableDesign $userProjectCustomizableModel,
      DesignFile $designFileModel,
      InoviceItemDesignFile $inoviceItemDesignFileModel
      )
  {
        $this->userProjectCustomizableModel = $userProjectCustomizableModel;
        $this->designFileModel              = $designFileModel;
        $this->inoviceItemDesignFileModel   = $inoviceItemDesignFileModel;
  }
  /*
    function is for set static data values for design into session 
    @author harish meshram
  */
  public function setStaticSessionData()
  {
    session()->flush();
    $session = [
                /* 'useSessionCurrentDesigns'=>true,
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
                'backOrientation' => ''  */
                'invoiceItemId'=>4
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
                    $this->load($itemDesignFile->designFile);
                }
                /* foreach ($item->designFiles as $designFile) {
                    $design = new Expresscopy_Design();
                    $design->load($designFile);
                    $selectedDesigns[$designFile->side] = $design->getInfo();
                }    */
            }else{
                return $selectedDesigns;
            }
        }
        krsort($selectedDesigns);
        return $selectedDesigns;
    }

    public function load($values = null){

        if (!$values) {
            throw new Expresscopy_Exception('Attempt to load with no values.');
        }

        // load and early return for objects
        if ($values instanceof Excopy_Model_Design_File) {
            $this->loadDesignFile($values);
            if ($values->type == 'uploaded') {
                $this->_type = 'uploaded';
            }
            return $this;

        }
        // defaults
        if (!isset($values['side'])) {
            $values['side'] = 'front';
        }
        $this->_side = $values['side'];
        if ($values['side'] == 'front') {
            $page = 1;
        } else {
            if ($values['side'] == 'back') {
                $page = 2;
            }
        }
        if (isset($values['frontDesignType'])) {
            $this->_type = $values['frontDesignType'];
        } else {
            $this->_type = $values['backDesignType'];
        }

        if (isset($values['categoryId'])) {
            $this->_categoryId = $values['categoryId'];
        }
        if (isset($values['backRequired'])) {
            $this->_backRequired = $values['backRequired'];
        }
        if (isset($values['intention'])) {
            $this->_intention = $values['intention'];
        }
        $this->_id = $values['designId'];
        switch ($this->_type) {
            case 'customizable':
                $design = Excopy_Db_Table::instance('Excopy_Model_EZT2_Design_Customizable_Design_Table')
                    ->fetchRow($this->_id);
                $thumbPrefix = Zend_Registry::get('serverConfig')->designFile->thumbRootURL;
                $this->_smallThumbPath = $thumbPrefix . '/' . $design->smallThumbPath;
                $this->_largeThumbPath = $thumbPrefix . '/' . $design->largeThumbPath;
                $this->_orientation = $design->orientation;
                $this->_productId = $design->productPrintId;
                break;
            case 'unfinished':
                $customizableDesignRow =
                    Excopy_Db_Table::instance('Excopy_Model_EZT2_Design_Customizable_Design_Table')
                        ->select()
                        ->setIntegrityCheck(FALSE)
                        ->from(array ('a' => 'ezt2.customizable_design'))
                        ->join(
                            array ('b' => 'ezt2.user_project_customizable_design'),
                            'a.id = b.customizable_design_id',
                            array ('b.small_thumb')
                        )
                        ->join(
                            array ('c' => 'ezt2.user_project'),
                            'b.user_project_id = c.id',
                            array ()
                        )
                        ->where('page=?', $page)
                        ->where('c.id=?', $this->_id)
                        ->fetchRow();
                $thumbPrefix = Zend_Registry::get('serverConfig')->designFile->thumbRootURL;
                if (!is_null($customizableDesignRow->smallThumb)) {
                    $this->_smallThumbPath = $customizableDesignRow->smallThumb;
                } else {
                    $this->_smallThumbPath = $thumbPrefix . '/' . $customizableDesignRow->smallThumbPath;
                }

                $this->_largeThumbPath = $thumbPrefix . '/' . $customizableDesignRow->largeThumbPath;
                $this->_orientation = $customizableDesignRow->orientation;
                $this->_productId = $customizableDesignRow->productPrintId;
                break;
            case 'customized':
                $designFile = Excopy_Db_Table::instance('Excopy_Model_Design_File_Table')
                    ->select()
                    ->from(array ('df' => 'design_file'))
                    ->join(
                        array ('dfup' => 'design_file_user_project'), 'df.id = dfup.design_file_id', array ()
                    )
                    ->where('dfup.user_project_id = ?', $this->_id)
                    ->where('df.page = ?', $page)
                    ->fetchRow();
                if ($designFile) {
                    $this->loadDesignFile($designFile);
                }
                break;
            default:
                if (!is_null($this->_id)) {
                    $designFile = Excopy_Db_Table::instance('Excopy_Model_Design_File_Table')
                        ->fetchRow($this->_id);
                    $this->loadDesignFile($designFile);
                }
                break;
        }
        return $this;
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
