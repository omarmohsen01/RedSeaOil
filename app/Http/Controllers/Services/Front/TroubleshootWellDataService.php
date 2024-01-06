<?php

namespace App\Http\Controllers\Services\Front;

use App\Http\Controllers\Interfaces\Front\TroubleshootWellDataServiceInterface;
use App\Http\Controllers\Interfaces\Front\WellDataServiceInterface;
use App\Models\Structure;
use App\Models\Structure_description;
use App\Models\TroubleshootStructure;
use App\Models\TroubleshootStructure_description;
use App\Models\TroubleshootWell;
use App\Models\TroubleshootWell_data;
use App\Models\Well;
use App\Models\Well_data;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TroubleshootWellDataService implements TroubleshootWellDataServiceInterface
{
    public $well,$well_data,$structure_description,$structure;
    public function __construct(TroubleshootWell $well,TroubleshootWell_data $well_data,TroubleshootStructure_description $structure_description,TroubleshootStructure $structure)
    {
        $this->well = $well;
        $this->well_data = $well_data;
        $this->structure_description = $structure_description;
        $this->structure = $structure;
    }
    //1-save a draft from first time
    //2-save a draft from dataWell already exist(edit)
    //3-publish from first time
    //4-publish from dataWell already exist(edit)

    //3-publish from first time
    public function publishNewTroubleshootWell($request,$published)
    {
        $well=Well::findOrFail($request->well_id)->first();
        if($well){
            $troubleshootWell=TroubleshootWell::create([
                'well_id'=>$request->well_id,
                'user_id'=>Auth::id(),
                'published'=>($published=='published')?'published':'as_draft',
            ]);
            $troubleshootDataInputs= $request->json('troubleshoot_data');
            foreach($troubleshootDataInputs as $troubleshootDataInput){
                if(isset($troubleshootDataInput['troubleshoot_structure_description_id'])){
                    $troubleshoot_structure_desc=TroubleshootStructure_description::findOrfail($troubleshootDataInput['troubleshoot_structure_description_id']);
                    //(string&Int&Boolean) or List or Multitext
                    $type=$troubleshoot_structure_desc->type;
                    if(($type == 'String') || ($type == 'Int') || ($type == 'Boolean') || ($type == 'List')){
                        TroubleshootWell_data::createStringIntBoolList($troubleshootWell,$troubleshoot_structure_desc->id,$troubleshootDataInput['data']);
                    }elseif($type=='MultiText'){
                        $data = [
                            'pi' => $troubleshootDataInput['data']['Pi'],
                            'Pd' => $troubleshootDataInput['data']['Pd'],
                            'Ti' => $troubleshootDataInput['data']['Ti'],
                            'Tm' => $troubleshootDataInput['data']['Tm'],
                            'Ct' => $troubleshootDataInput['data']['Ct'],
                        ];
                        TroubleshootWell_data::createStringIntBoolList($troubleshootWell,$troubleshootDataInput['test_structure_description_id'],$data);
                    }
                }elseif(!isset($troubleshootDataInput['troubleshoot_structure_description_id'])
                        && isset($troubleshootDataInput['troubleshoot_structure_id'])
                        && isset($troubleshootDataInput['input']))
                {
                    //date && multitext
                    TroubleshootStructure::findOrfail($troubleshootDataInput['troubleshoot_structure_id']);
                    $validator = Validator::make(['input' => $troubleshootDataInput['input']], ['input' => 'date']);
                    if($validator->fails())
                    {
                        TroubleshootWell_data::createMultiTextWithInput($troubleshootDataInput,$troubleshootWell);
                    }else{
                        TroubleshootWell_data::createDateInputWithData($troubleshootDataInput,$troubleshootWell);
                    }
                }
            }
        }
    }

    //4-publish from dataWell already exist(edit)
    public function publishOldTroubleshootWell($request,$published)
    {
        $troubleshoot_well=TroubleshootWell::findOrFail($request->test_well_id)->first();
        $troubleshoot_well_data=TroubleshootWell_data::with('Structure_description')->where('test_well_id',$request->test_well_id)->get();
        TroubleshootWell::updateWell($troubleshoot_well,$published);
        $troubleshootDataInputs= $request->json('troubleshoot_data');

        if(!empty($troubleshootDataInputs)){
            foreach($troubleshootDataInputs as $troubleshootDataInput){
                //for edit old data
                if(isset($troubleshootDataInput['troubleshoot_well_data_id'])){
                    $troubleshoot_well_data_id=$troubleshootDataInput['troubleshoot_well_data_id'];
                    $troubleshoot_well_data=TroubleshootWell_data::with('Structure_description')->where('id',$troubleshoot_well_data_id)->first();
                    $type=$troubleshoot_well_data->Structure_description->type;
                    if(!isset($troubleshootDataInput['input'])){
                        if(($type == 'String') || ($type == 'Int') || ($type == 'Boolean') || ($type=='List')){
                            $troubleshoot_well_data->update([
                                'data'=>json_encode($troubleshootDataInput['data'])
                            ]);
                        }elseif($type=='MultiText'){
                                $data = [
                                    'pi' => $troubleshootDataInput['data']['Pi'],
                                    'Pd' => $troubleshootDataInput['data']['Pd'],
                                    'Ti' => $troubleshootDataInput['data']['Ti'],
                                    'Tm' => $troubleshootDataInput['data']['Tm'],
                                    'Ct' => $troubleshootDataInput['data']['Ct'],
                                ];
                                $troubleshoot_well_data->update([
                                    'data'=>json_encode($data)
                                ]);
                        }
                    }else{
                        if($type=='MultiText'){
                            $data = [
                                'pi' => $troubleshootDataInput['data']['Pi'],
                                'Pd' => $troubleshootDataInput['data']['Pd'],
                                'Ti' => $troubleshootDataInput['data']['Ti'],
                                'Tm' => $troubleshootDataInput['data']['Tm'],
                                'Ct' => $troubleshootDataInput['data']['Ct'],
                            ];
                            $troubleshoot_well_data->update([
                                'input'=>json_encode($troubleshootDataInput['input']),
                                'data'=>json_encode($data)
                            ]);
                        }else{
                            $troubleshoot_well_data->update([
                                'input'=>json_encode($troubleshootDataInput['input']),
                                'data'=>json_encode($troubleshootDataInput['data'])
                            ]);
                        }
                    }
                }
                //for store new data
                elseif(!isset($troubleshootDataInput['troubleshoot_well_data_id'])){
                    if(isset($troubleshootDataInput['troubleshoot_structure_description_id'])){
                        $troubleshoot_structure_desc=TroubleshootStructure_description::findOrfail($troubleshootDataInput['troubleshoot_structure_description_id']);
                        //(string&Int&Boolean) or List or Multitext
                        $type=$troubleshoot_structure_desc->type;
                        if(($type == 'String') || ($type == 'Int') || ($type == 'Boolean') || ($type == 'List')){
                            TroubleshootWell_data::createStringIntBoolList($troubleshoot_well,$troubleshoot_structure_desc->id,$troubleshootDataInput['data']);
                        }elseif($type=='MultiText'){
                            $data = [
                                'pi' => $troubleshootDataInput['data']['Pi'],
                                'Pd' => $troubleshootDataInput['data']['Pd'],
                                'Ti' => $troubleshootDataInput['data']['Ti'],
                                'Tm' => $troubleshootDataInput['data']['Tm'],
                                'Ct' => $troubleshootDataInput['data']['Ct'],
                            ];
                            TroubleshootWell_data::createStringIntBoolList($troubleshoot_well,$troubleshootDataInput['troubleshoot_structure_description_id'],$data);
                        }
                    }elseif(!isset($troubleshootDataInput['troubleshoot_structure_description_id'])
                        && isset($troubleshootDataInput['troubleshoot_structure_id'])
                        && isset($troubleshootDataInput['input']))
                    {
                        //date && multitext
                        TroubleshootStructure::findOrfail($troubleshootDataInput['troubleshoot_structure_id']);
                        $validator = Validator::make(['input' => $troubleshootDataInput['input']], ['input' => 'date']);
                        if($validator->fails())
                        {
                            $this->well_data->createMultiTextWithInput($troubleshootDataInput,$troubleshoot_well);
                        }else{
                            $this->well_data->createDateInputWithData($troubleshootDataInput,$troubleshoot_well);
                        }
                    }
                }
            }
        }
    }


    public function requestToEdit($request,$published,$wellRequest)
    {
        // $well=$this->well->where('id',$wellRequest->well_id)->first();
        $well_data=$this->well_data->with('Structure_description')->where('troubleshoot_well_id',$wellRequest->troubleshoot_well_id)->get();
        // $this->well->updateWell($well,$published);
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
                //for store new data
                // elseif(!isset($wellDataInput['well_data_id'])){
                //     if(isset($wellDataInput['structure_description_id'])){
                //         $structure_desc=$this->structure_description->findOrfail($wellDataInput['structure_description_id']);
                //         //(string&Int&Boolean) or List or Multitext
                //         $type=$structure_desc->type;
                //         if(($type == 'String') || ($type == 'Int') || ($type == 'Boolean') || ($type == 'List')){
                //             $this->well_data->createStringIntBoolList($well,$structure_desc->id,$wellDataInput['data']);
                //         }elseif($type=='MultiText'){
                //             $data = [
                //                 'pi' => $wellDataInput['data']['Pi'],
                //                 'Pd' => $wellDataInput['data']['Pd'],
                //                 'Ti' => $wellDataInput['data']['Ti'],
                //                 'Tm' => $wellDataInput['data']['Tm'],
                //                 'Ct' => $wellDataInput['data']['Ct'],
                //             ];
                //             $this->well_data->createStringIntBoolList($well,$wellDataInput['structure_description_id'],$data);
                //         }
                //     }elseif(!isset($wellDataInput['structure_description_id']) && isset($wellDataInput['structure_id']) && isset($wellDataInput['input']))
                //     {
                //         //date && multitext
                //         $this->structure->findOrfail($wellDataInput['structure_id']);
                //         $validator = Validator::make(['input' => $wellDataInput['input']], ['input' => 'date']);
                //         if($validator->fails())
                //         {
                //             $this->well_data->createMultiTextWithInput($wellDataInput,$well);
                //         }else{
                //             $this->well_data->createDateInputWithData($wellDataInput,$well);
                //         }
                //     }
                // }
            }
        }
    }
}
