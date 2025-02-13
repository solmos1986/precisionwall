<?php

namespace App\View\Components\components\informacion-project;

use Illuminate\View\Component;

class view-info extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $status_info;
    public function __construct($status_info)
    {
        $this->status_info=$status_info;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.components.informacion-project.view-info');
    }
}
