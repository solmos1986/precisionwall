<?php

namespace App\View\Components\pdf;

use Illuminate\View\Component;

class firma extends Component
{
    public $firma_installer;
    public $firma_foreman;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($firma_installer,$firma_foreman)
    {
        $this->firma_installer=$firma_installer;
        $this->firma_foreman=$firma_foreman;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.pdf.firma');
    }
}
