<?php

namespace App\Livewire;

use App\Models\TestStructure;
use Livewire\Component;
use App\Models\Structure;


class DeleteTestStructure extends Component
{
    public $StructureId;
    public $password;
    public function mount($StructureId)
    {
        $this->StructureId = $StructureId;
    }
    public function render()
    {
        return view('livewire.delete-teststructure');
    }
    public function deleteStructure()
    {
        if (! password_verify($this->password, auth()->user()->password)) {
            session()->flash('fail', 'Incorrect password. Please try again.');
            return redirect()->route('testStructure.delete',$this->StructureId);
        }

        $structure = TestStructure::find($this->StructureId);
        $structure->delete();

        session()->flash('success', 'Structure deleted successfully.');

        return redirect()->route('tests.index');
    }
}
