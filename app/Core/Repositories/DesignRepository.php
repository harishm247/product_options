<?php

namespace App\Core\Repositories;

use App\Core\Interfaces\DesignInterface;
use App\Core\Repositories\BaseRepository;

class DesignRepository extends BaseRepository implements DesignInterface
{
  public function __construct()
  {

  }
  /*
    function is for set static data values for design into session 
    @author harish meshram
  */
  public function setStaticSessionData()
  {
    $session = [
                'useSessionCurrentDesigns'=>true,
                'frontId' => '2326644',
                'frontType' => 'uploaded',
                'frontThumb' => 'http://upload.expresscopy.com/static/img/user/2020/01/27/22/df-5e2fd39d432b4/small-thumb.jpg',
                'frontProductName' => 'Regular Postcard (4.25" x 5.6")',
                'frontProductId' => '1',
                'frontOrientation' => '',
                'frontBackRequired' => '',
                'backId' => '2326645',
                'backType' => 'uploaded',
                'backThumb' => 'http://upload.expresscopy.com/static/img/user/2020/01/27/22/df-5e2fd39e1ec03/small-thumb.jpg',
                'backProductId' => '1',
                'backOrientation' => ''
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
            /* if (!$item = Expresscopy_Invoice_Item::current()) {
                return $selectedDesigns;
            }
            foreach ($item->designFiles as $designFile) {
                $design = new Expresscopy_Design();
                $design->load($designFile);
                $selectedDesigns[$designFile->side] = $design->getInfo();
            } */
        }
        krsort($selectedDesigns);
        return $selectedDesigns;
    }

    /**
     * startOver()
     *
     * Clear selectd deisgnd
     * @author : Harish Meshram
     * 
     */
    /* public function startOver()
    { */
        /** @author: Sonu B*/
     /*    $payload = Expresscopy_Session::getLaravelSession();
        // check and unset if related session data was set using laravel
        if (!empty($payload)) {
            if (isset($payload['designs'])) {
                unset($payload['designs']);
            }
            if (isset($payload["frontId"])) {
                unset($payload['frontId']);
            }
            if (isset($payload["userProjectId"])) {
                unset($payload['userProjectId']);
            }
            if (isset($payload["frontType"])) {
                unset($payload['frontType']);
            }
            if (isset($payload["frontThumb"])) {
                unset($payload['frontThumb']);
            }
            if (isset($payload["frontProductName"])) {
                unset($payload['frontProductName']);
            }
            if (isset($payload["frontProductId"])) {
                unset($payload['frontProductId']);
            }
            if (isset($payload["frontOrientation"])) {
                unset($payload['frontOrientation']);
            }
            if (isset($payload["frontBackRequired"])) {
                unset($payload['frontBackRequired']);
            }
            if (isset($payload["backId"])) {
                unset($payload['backId']);
            }
            if (isset($payload["backType"])) {
                unset($payload['backType']);
            }
            if (isset($payload["backThumb"])) {
                unset($payload['backThumb']);
            }
            if (isset($payload["backProductId"])) {
                unset($payload['backProductId']);
            }
            if (isset($payload["backOrientation"])) {
                unset($payload['backOrientation']);
            }
            if (isset($payload["useSessionCurrentDesigns"])) {
                unset($payload['useSessionCurrentDesigns']);
            }
            if (isset($payload["userSelectedStock"])) {
                $payload['userSelectedStock'] = FALSE;
            }
            if (isset($payload["designIds"])) {
                unset($payload['designIds']);
            }
        }
        // update the database session for laravel
        Expresscopy_Session::updateLaravelSession($payload);

        // OVERKILL?
        unset($this->session->userProjectId);
        $this->session->userProjectId = null;
        unset($this->session->frontId);
        $this->session->frontId = null;
        unset($this->session->frontType);
        $this->session->frontType = null;
        unset($this->session->backId);
        $this->session->backId = null;
        unset($this->session->backType);
        $this->session->backType = null;
        unset($this->session->useSessionCurrentDesigns);
        $this->session->useSessionCurrentDesigns = null;
        $this->session->userSelectedStock = FALSE;

        if ($this->invoiceItem) {
            $this->invoiceItem->removeDesignFiles();
            //push address and eddm selections back to session
            if (count($addressfiles = $this->invoiceItem->getAddressFiles()) > 0) {
                foreach ($addressfiles as $addresFile) {
                    $this->session->addressFileIds[] = $addresFile->id;
                }
            }
            if (count($eddmSelections = $this->invoiceItem->getEddmSelections()) > 0) {
                foreach ($eddmSelections as $eddmSelection) {
                    $this->session->eddmSelectionIds[] = $eddmSelection->id;
                }
            }
        }
        $this->_redirect('design');
    } */
}
