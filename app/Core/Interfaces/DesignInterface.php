<?php

namespace App\Core\Interfaces;

interface DesignInterface
{
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
    public function getSelectedDesigns();
    
    /*
        Function is for set static data values for design into session 
        @Author harish meshram
    */
    public function setStaticSessionData();
}
