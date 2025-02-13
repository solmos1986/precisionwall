<?php

namespace App\View\Components\components\estimados\labor_cost;

use Illuminate\View\Component;

class labor_cost extends Component
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
        return view('components.components.estimados.labor_cost.labor_cost');
    }
}
