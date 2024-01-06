<?php

namespace App\Livewire;

use App\Models\Troubleshoot;
use Livewire\Component;

class EditTroubleshootStructure extends Component
{
    public $structuresDesUpdate=[];
    public $structuresDes = [];
    public $structuresDesMenu = [];
    public $types;
    public $options;
    public $structure;
    public function mount($structure)
    {
        $this->structure = $structure;
        $this->options=Troubleshoot::all();
        $this->structuresDesUpdate=[];
        $this->structuresDes = [];
        $this->structuresDesMenu = [];
        $this->types = ['String', 'Int', 'Boolean', 'Date', 'MultiText','List'];
    }
    public function render()
    {
        return view('livewire.edit-troubleshootstructure');
    }
    public function addStruDesc()
    {
        $this->structuresDes[] = ['input', 'type', 'is_require'];
    }
    public function addStruMenu()
    {
        $this->structuresDesMenu[] = ['input' => '', 'data' => []];
    }

    public function addData($index)
    {
        $this->structuresDesMenu[$index]['data'][] = '';
    }
    public function removeStruDesc($index)
    {
        unset($this->structuresDes[$index]);
        $this->structuresDes = array_values($this->structuresDes);
    }

    public function removeStruMenu($index)
    {
        unset($this->structuresDesMenu[$index]);
        $this->structuresDesMenu = array_values($this->structuresDesMenu);
    }

    public function removeData($descIndex, $dataIndex)
    {
        unset($this->structuresDesMenu[$descIndex]['data'][$dataIndex]);
        $this->structuresDesMenu[$descIndex]['data'] = array_values($this->structuresDesMenu[$descIndex]['data']);
    }
}
