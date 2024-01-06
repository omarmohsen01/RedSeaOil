<?php

namespace App\Http\Controllers\Services\Front;
use App\Http\Controllers\Interfaces\Front\TestWellDataServiceInterface;
use App\Http\Controllers\Interfaces\Front\WellDataServiceInterface;
use App\Models\Structure;
use App\Models\Structure_description;
use App\Models\TestStructure;
use App\Models\TestStructure_description;
use App\Models\TestWell;
use App\Models\TestWell_data;
use App\Models\Well;
use App\Models\Well_data;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TestWellDataService implements TestWellDataServiceInterface
{
    public $well_data,$structure_description,$structure;
    public function __construct(TestWell_data $well_data,TestStructure_description $structure_description,TestStructure $structure)
    {
        $this->well_data = $well_data;
        $this->structure_description = $structure_description;
        $this->structure = $structure;
    }
    //1-save a draft from first time
    //2-save a draft from dataWell already exist(edit)
    //3-publish from first time
    //4-publish from dataWell already exist(edit)

    //3-publish from first time
    public function publishNewTestWell($request,$published)
    {
        $well=Well::findOrFail($request->well_id)->first();
        if($well){
            $testWell=TestWell::create([
                'well_id'=>$request->well_id,
                'user_id'=>Auth::id(),
                'published'=>($published=='published')?'published':'as_draft',
            ]);
            $testDataInputs= $request->json('test_data');
            foreach($testDataInputs as $testDataInput){
                if(isset($testDataInput['test_structure_description_id'])){
                    $test_structure_desc=TestStructure_description::findOrfail($testDataInput['test_structure_description_id']);
                    //(string&Int&Boolean) or List or Multitext
                    $type=$test_structure_desc->type;
                    if(($type == 'String') || ($type == 'Int') || ($type == 'Boolean') || ($type == 'List')){
                        TestWell_data::createStringIntBoolList($testWell,$test_structure_desc->id,$testDataInput['data']);
                    }elseif($type=='MultiText'){
                        $data = [
                            'pi' => $testDataInput['data']['Pi'],
                            'Pd' => $testDataInput['data']['Pd'],
                            'Ti' => $testDataInput['data']['Ti'],
                            'Tm' => $testDataInput['data']['Tm'],
                            'Ct' => $testDataInput['data']['Ct'],
                        ];
                        TestWell_data::createStringIntBoolList($testWell,$testDataInput['test_structure_description_id'],$data);
                    }
                }elseif(!isset($testDataInput['test_structure_description_id'])
                        && isset($testDataInput['test_structure_id'])
                        && isset($testDataInput['input']))
                {
                    //date && multitext
                    TestStructure::findOrfail($testDataInput['test_structure_id']);
                    $validator = Validator::make(['input' => $testDataInput['input']], ['input' => 'date']);
                    if($validator->fails())
                    {
                        TestWell_data::createMultiTextWithInput($testDataInput,$testWell);
                    }else{
                        TestWell_data::createDateInputWithData($testDataInput,$testWell);
                    }
                }
            }
        }
    }

    //4-publish from dataWell already exist(edit)
    public function publishOldTestWell($request,$published)
    {
        $test_well=TestWell::findOrFail($request->test_well_id)->first();
        $test_well_data=TestWell_data::with('Structure_description')->where('test_well_id',$request->test_well_id)->get();
        TestWell::updateWell($test_well,$published);
        $testDataInputs= $request->json('test_data');

        if(!empty($testDataInputs)){
            foreach($testDataInputs as $testDataInput){
                //for edit old data
                if(isset($testDataInput['test_well_data_id'])){
                    $test_well_data_id=$testDataInput['test_well_data_id'];
                    $test_well_data=TestWell_data::with('Structure_description')->where('id',$test_well_data_id)->first();
                    $type=$test_well_data->Structure_description->type;
                    if(!isset($testDataInput['input'])){
                        if(($type == 'String') || ($type == 'Int') || ($type == 'Boolean') || ($type=='List')){
                            $test_well_data->update([
                                'data'=>json_encode($testDataInput['data'])
                            ]);
                        }elseif($type=='MultiText'){
                                $data = [
                                    'pi' => $testDataInput['data']['Pi'],
                                    'Pd' => $testDataInput['data']['Pd'],
                                    'Ti' => $testDataInput['data']['Ti'],
                                    'Tm' => $testDataInput['data']['Tm'],
                                    'Ct' => $testDataInput['data']['Ct'],
                                ];
                                $test_well_data->update([
                                    'data'=>json_encode($data)
                                ]);
                        }
                    }else{
                        if($type=='MultiText'){
                            $data = [
                                'pi' => $testDataInput['data']['Pi'],
                                'Pd' => $testDataInput['data']['Pd'],
                                'Ti' => $testDataInput['data']['Ti'],
                                'Tm' => $testDataInput['data']['Tm'],
                                'Ct' => $testDataInput['data']['Ct'],
                            ];
                            $test_well_data->update([
                                'input'=>json_encode($testDataInput['input']),
                                'data'=>json_encode($data)
                            ]);
                        }else{
                            $test_well_data->update([
                                'input'=>json_encode($testDataInput['input']),
                                'data'=>json_encode($testDataInput['data'])
                            ]);
                        }
                    }
                }
                //for store new data
                elseif(!isset($testDataInput['test_well_data_id'])){
                    if(isset($testDataInput['test_structure_description_id'])){
                        $test_structure_desc=TestStructure_description::findOrfail($testDataInput['test_structure_description_id']);
                        //(string&Int&Boolean) or List or Multitext
                        $type=$test_structure_desc->type;
                        if(($type == 'String') || ($type == 'Int') || ($type == 'Boolean') || ($type == 'List')){
                            TestWell_data::createStringIntBoolList($test_well,$test_structure_desc->id,$testDataInput['data']);
                        }elseif($type=='MultiText'){
                            $data = [
                                'pi' => $testDataInput['data']['Pi'],
                                'Pd' => $testDataInput['data']['Pd'],
                                'Ti' => $testDataInput['data']['Ti'],
                                'Tm' => $testDataInput['data']['Tm'],
                                'Ct' => $testDataInput['data']['Ct'],
                            ];
                            TestWell_data::createStringIntBoolList($test_well,$testDataInput['test_structure_description_id'],$data);
                        }
                    }elseif(!isset($testDataInput['test_structure_description_id'])
                        && isset($testDataInput['test_structure_id'])
                        && isset($testDataInput['input']))
                    {
                        //date && multitext
                        TestStructure::findOrfail($testDataInput['test_structure_id']);
                        $validator = Validator::make(['input' => $testDataInput['input']], ['input' => 'date']);
                        if($validator->fails())
                        {
                            $this->well_data->createMultiTextWithInput($testDataInput,$test_well);
                        }else{
                            $this->well_data->createDateInputWithData($testDataInput,$test_well);
                        }
                    }
                }
            }
        }
    }


    public function requestToEdit($request,$published,$wellRequest)
    {
        // $well=TestWell::where('id',$wellRequest->well_id)->first();
        $well_data=$this->well_data->with('Structure_description')
                        ->where('test_well_id',$wellRequest->test_well_id)->get();
        // TestWell::updateWell($well,$published);
        $wellDataInputs= $request->json('well_data');

        if(!empty($wellDataInputs)){
            foreach($wellDataInputs as $wellDataInput){
                //for edit old data
                if(isset($wellDataInput['well_data_id'])){
                    $well_data_id=$wellDataInput['well_data_id'];
                    $well_data=$this->well_data->with('Structure_description')->where('id',$well_data_id)->first();
                    $type=$well_data->Structure_description->type;
                    if(!isset($wellDataInput['input'])){
                        if(($type == 'String') || ($type == 'Int') || ($type == 'Boolean') || ($type=='List')){
                            $well_data->update([
                                'data'=>json_encode($wellDataInput['data'])
                            ]);
                        }elseif($type=='MultiText'){
                                $data = [
                                    'pi' => $wellDataInput['data']['Pi'],
                                    'Pd' => $wellDataInput['data']['Pd'],
                                    'Ti' => $wellDataInput['data']['Ti'],
                                    'Tm' => $wellDataInput['data']['Tm'],
                                    'Ct' => $wellDataInput['data']['Ct'],
                                ];
                                $well_data->update([
                                    'data'=>json_encode($data)
                                ]);
                        }
                    }else{
                        if($type=='MultiText'){
                            $data = [
                                'pi' => $wellDataInput['data']['Pi'],
                                'Pd' => $wellDataInput['data']['Pd'],
                                'Ti' => $wellDataInput['data']['Ti'],
                                'Tm' => $wellDataInput['data']['Tm'],
                                'Ct' => $wellDataInput['data']['Ct'],
                            ];
                            $well_data->update([
                                'input'=>json_encode($wellDataInput['input']),
                                'data'=>json_encode($data)
                            ]);
                        }else{
                            $well_data->update([
                                'input'=>json_encode($wellDataInput['input']),
                                'data'=>json_encode($wellDataInput['data'])
                            ]);
                        }
                    }
                }
            }
        }
    }
}
