<?php

namespace App\View\Components\components\informacion-project;

use Illuminate\View\Component;

class info extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $statusInfo;
    public $statusProyecto;
    public $tipoProyecto; 

    public function __construct($statusInfo,$statusProyecto,$tipoProyecto)
    {
        $this->statusInfo=$statusInfo;
        $this->statusProyecto=$statusProyecto;
        $this->tipoProyecto=$tipoProyecto;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.components.informacion-project.info');
    }
}
