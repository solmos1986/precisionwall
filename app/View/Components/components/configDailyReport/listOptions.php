<?php

namespace App\View\Components\components\configDailyReport;

use Illuminate\View\Component;

class listOptions extends Component
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
        return view('components.components.config-daily-report.list-options');
    }
}
