<?php

namespace App\Livewire\User;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Hash;

class Update extends Component
{
    public $id_user;
    public $level, $username, $password, $no_telp, $alamat;
    public $showModal = false;

    protected $listeners = ['edit' => 'edit'];

    protected function rules()
    {
        return [
            'level' => 'required|string',
            'username' => 'required|string|max:255|unique:users,username,' . $this->id_user,
            'password' => 'nullable|string|min:6',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:255',
        ];
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);

        $this->id_user = $user->id;
        $this->level = $user->level;
        $this->username = $user->username;
        $this->no_telp = $user->no_telp;
        $this->alamat = $user->alamat;
        $this->password = null;

        $this->showModal = true;
    }

    public function update()
    {
        $this->validate();

        $user = User::findOrFail($this->id_user);
        $user->update([
            'level' => $this->level,
            'username' => $this->username,
            'password' => $this->password ? Hash::make($this->password) : $user->password,
            'no_telp' => $this->no_telp,
            'alamat' => $this->alamat,
        ]);

        $this->reset(['showModal', 'password']);
        $this->dispatch('refreshDatatable');
        session()->flash('message', 'User berhasil diupdate.');
    }

    public function render()
    {
        return view('livewire.user.update');
    }
}
