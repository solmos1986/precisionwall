<?php

namespace App\View\Components\Components\registerActividad;

use Illuminate\View\Component;

class modalActividad extends Component
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
        return view('components.components.register-actividad.modal-actividad');
    }
}
