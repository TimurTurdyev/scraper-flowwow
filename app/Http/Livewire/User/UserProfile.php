<?php

namespace App\Http\Livewire\User;

use App\Models\User;
use Livewire\Component;

class UserProfile extends Component
{
    public User $user;
    public $showSuccesNotification = false;

    public $showDemoNotification = false;

    protected $rules = [
        'user.name' => 'max:40|min:3',
        'user.email' => 'email:rfc,dns',
    ];

    public function mount()
    {
        $this->user = auth()->user();
    }

    public function save()
    {
        $this->validate();
        $this->user->save();
        $this->showSuccesNotification = true;
    }

    public function render()
    {
        return view('livewire.user.user-profile');
    }
}
