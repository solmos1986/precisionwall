<?php

namespace App\View\Components\components\tipo-orden\list\vendor;

use Illuminate\View\Component;

class edit extends Component
{
    public $proveedores;
    public $vendor;
    public $status;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($proveedores,$status,$vendor)
    {
        $this-> $proveedores= $proveedores;
        $this-> $status= $status;
        $this-> $vendor= $vendor;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.components.tipo-orden.list.vendor.edit');
    }
}
