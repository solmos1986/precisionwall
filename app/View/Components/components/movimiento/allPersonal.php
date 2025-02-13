<?php

namespace App\View\Components\components\movimiento;

use Illuminate\View\Component;

class allPersonal extends Component
{
    public $eventos;
    public $company;
    public $cargos;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($eventos, $company, $cargos)
    {
        $this->eventos = $eventos;
        $this->company = $company;
        $this->cargos = $cargos;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.components.movimiento.all-personal');
    }
}
