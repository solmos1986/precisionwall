<?php

namespace App\View\Components\pdf;

use Illuminate\View\Component;

class images extends Component
{
    public $images;
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct($images)
    {
        $this->images=$images;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.pdf.images');
    }
}
