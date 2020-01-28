<?php namespace App\Http\Composers;

use App\Core\Interfaces\DesignInterface;
use Illuminate\Contracts\View\View;

class DesignWidgetComposer
{
    protected $designInterface;

    public function __construct(  
        DesignInterface $designInterface
    ) 
    {    
        $this->designInterface  = $designInterface;
    }

    /**
     * Compose the selected design widget views
     *
     * @param View $view
     * @return View
     */
    public function compose(View $view)
    {   
        //Get Selected Design Files
        $designs = $this->designInterface->getSelectedDesigns();
        return $view->with(
            [
              'designs' => $designs,
            ]
        );
    }
}