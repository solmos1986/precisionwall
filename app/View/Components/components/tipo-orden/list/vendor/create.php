<?php

namespace App\View\Components\components\tipo-orden\list\vendor;

use Illuminate\View\Component;

class create extends Component
{
    public $proveedores;
    public $vendor;
    public $status;
    public function __construct( $proveedores,$status,$vendor)
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
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
        return view('components.components.tipo-orden.list.vendor.create');
    }
}
