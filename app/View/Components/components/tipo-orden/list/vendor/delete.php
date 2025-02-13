<?php

namespace App\View\Components\components\tipo-orden\list\vendor;

use Illuminate\View\Component;

class delete extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.components.tipo-orden.list.vendor.delete');
    }
}
