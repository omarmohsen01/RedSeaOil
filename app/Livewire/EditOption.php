<?php

namespace App\Livewire;

use App\Models\Option;
use App\Models\Test;
use Livewire\Component;

class EditOption extends Component
{
    public $optionId;
    public $newOptionName;

    public function mount($optionId)
    {
        $this->optionId = $optionId;
        $option=Option::find($optionId);
        $this->newOptionName = $option->name;
    }
    public function render()
    {
        return view('livewire.edit-option');
    }
    public function updateTest()
    {
        $existingOption=Option::where('name',$this->newOptionName)
                                ->where('id','!=',$this->optionId)
                                ->first();
        if($existingOption)
        {
            session()->flash('fail', 'The Option Name Is Already In Use.');
            return redirect()->route('structures.index');
        }elseif($this->newOptionName==''){
            session()->flash('fail', 'The Option Name Can\'t Be Empty');
            return redirect()->route('optionStructures.index');
        }
        $test = Test::find($this->testId);
        $test->name = $this->newTestName;
        $test->save();

        session()->flash('success', 'Test updated successfully.');

        return redirect()->route('tests.index');
    }
}
