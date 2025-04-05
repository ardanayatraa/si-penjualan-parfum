<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class Create extends Component
{
    public $open = false;
    public $level, $username, $password, $no_telp, $alamat;

    protected $rules = [
        'level' => 'required|string',
        'username' => 'required|string|max:255|unique:users,username',
        'password' => 'required|string|min:6',
        'no_telp' => 'nullable|string|max:20',
        'alamat' => 'nullable|string|max:255',
    ];

    public function store()
    {
        $this->validate();

        User::create([
            'level' => $this->level,
            'username' => $this->username,
            'password' => Hash::make($this->password),
            'no_telp' => $this->no_telp,
            'alamat' => $this->alamat,
        ]);

        $this->reset();
        $this->dispatch('refreshDatatable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.user.create');
    }
}
