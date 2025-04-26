<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class UserForm extends Component
{
    public string $submitMethod;
    public ?string $id;
    public string $name;
    public string $email;
    public string $emailstaffPosition;
    public string $password;

    /**
     * Create a new component instance.
     */
    public function __construct(string $submitMethod, string $id = null, string $name, string $email, string $staffPosition, string $password)
    {
        $this->submitMethod = $submitMethod;
        $this->name = $name;
        $this->id = $id;
        $this->email = $email;
        $this->staffPosition = $staffPosition;
        $this->password = $password;

    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.user-form', [
            'name' => $this->name,
            'email' => $this->email,
            'staffPosition' => $this->staffPosition,
            'password' => $this->password,
        ]);
    }
}
