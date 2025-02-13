<?php

namespace App\View\Components\components\evento;

use Illuminate\View\Component;

class editEvento extends Component
{
    public $typeEventos;
    public $company;
    public $cargo;
    /**
    /**
     * Create a new component instance.
     *
     * @return void
     */

    public function __construct($typeEventos, $company, $cargo)
    {
        $this->typeEventos = $typeEventos;
        $this->company = $company;
        $this->cargo = $cargo;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.components.evento.edit-evento');
    }
}
