<?php

namespace App\View\Components\components\estimados\final;

use Illuminate\View\Component;

class view_filter extends Component
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
        return view('components.components.estimados.final.view_filter');
    }
}
