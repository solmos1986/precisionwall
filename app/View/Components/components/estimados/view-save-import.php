<?php

namespace App\View\Components\components\estimados;

use Illuminate\View\Component;

class view-save-import extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public $userId;
    public $userName;
    public function __construct($userId,$userName)
    {
        $this->user_id= $userId;
        $this->user_name= $userName;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.components.estimados.view-save-import');
    }
}
