<?php

namespace App\Livewire;

use App\Models\Option;
use App\Models\Survey;
use Livewire\Component;

class AddSurveyStructureDesc extends Component
{
    public $structuresDes=[];
    public $structuresDesMenu=[];
    public $types;
    public $surveys;
    public $selectedSurvey;

    public function mount($survey)
    {
        $this->selectedSurvey=$survey;
        $this->surveys=Survey::all();
        $this->structuresDes=[];
        $this->structuresDesMenu=[];
        $this->types=['String','Int','Boolean','Date','MultiText'];
    }
    public function render()
    {
        return view('livewire.add-surveystructure-desc');
    }
    public function addStruDesc()
    {
        $this->structuresDes[]=['input','type','is_require'];
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
