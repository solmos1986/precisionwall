<?php

namespace App\View\Components\components\goal\ProjectReportVisit;

use Illuminate\View\Component;

class viewCheckMaterial extends Component
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
        return view('components.components.goal.project-report-visit.view-check-material');
    }
}
