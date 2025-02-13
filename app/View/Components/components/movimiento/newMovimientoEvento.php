<?php

namespace App\View\Components\components\movimiento;

use Illuminate\View\Component;

class newMovimientoEvento extends Component
{
    public $personal;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($personal)
    {
        $this->personal=$personal;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.components.movimiento.new-movimiento-evento');
    }
}
