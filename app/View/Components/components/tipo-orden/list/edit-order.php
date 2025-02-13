<?php

namespace App\View\Components\components\tipo-orden\list;

use Illuminate\View\Component;

class edit-order extends Component
{
    public $status;
  
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($status)
    {
        $this-> $status= $status;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.components.tipo-orden.list.edit-order');
    }
}
