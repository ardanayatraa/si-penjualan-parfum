<?php

namespace App\Livewire\Table;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\JurnalUmum;

class DetailJurnalTable extends Component
{
    use WithPagination;

    public $search = '';
    public $sortField = 'tanggal';
    public $sortDirection = 'desc';

    protected $listeners = ['refreshDatatable' => '$refresh'];

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function delete($id)
    {
        JurnalUmum::find($id)->delete();
        $this->dispatch('refreshDatatable');
    }

    public function edit($id)
    {
        $this->dispatch('edit', $id);
    }

    public function render()
    {
        $jurnals = JurnalUmum::query()
            ->when($this->search, function ($query) {
                $query->where('no_bukti', 'like', '%' . $this->search . '%')
                      ->orWhere('keterangan', 'like', '%' . $this->search . '%');
            })
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        return view('livewire.table.detail-jurnal-table', [
            'jurnals' => $jurnals
        ]);
    }
} 