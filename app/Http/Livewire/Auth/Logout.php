<?php

namespace App\Http\Livewire\Auth;

use Illuminate\Contracts\View\View;
use Livewire\Component;

class Logout extends Component
{
    public function logout(): \Illuminate\Foundation\Application|\Illuminate\Routing\Redirector|\Illuminate\Http\RedirectResponse|\Illuminate\Contracts\Foundation\Application
    {
        auth()->logout();
        return redirect('/login');
    }

    public function render(): View
    {
        return view('livewire.auth.logout');
    }
}
