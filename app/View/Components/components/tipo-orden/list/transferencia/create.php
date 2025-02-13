<?php

namespace App\View\Components\components\tipo-orden\list\transferencia;

use Illuminate\View\Component;

class create extends Component
{
    public $proveedores;
    public $status;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($proveedores,$status)
    {
        $this-> $proveedores= $proveedores;
        $this-> $status= $status;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.components.tipo-orden.list.transferencia.create');
    }
}
