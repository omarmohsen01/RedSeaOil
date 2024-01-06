<?php

namespace App\Http\Controllers\Services\Front;
use App\Http\Controllers\Interfaces\Front\WellDataServiceInterface;
use App\Models\Structure;
use App\Models\Structure_description;
use App\Models\Well;
use App\Models\Well_data;
use Illuminate\Support\Facades\Validator;

class WellDataService implements WellDataServiceInterface
{
    public $well,$well_data,$structure_description,$structure;
    public function __construct(Well $well,Well_data $well_data,Structure_description $structure_description,Structure $structure)
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
    public function publishNewWell($request,$published)
    {
        $well=$this->well->createWell($request,$published);
        $wellDataInputs= $request->json('well_data');
        foreach($wellDataInputs as $wellDataInput){
            if(isset($wellDataInput['structure_description_id'])){
                $structure_desc=$this->structure_description->findOrfail($wellDataInput['structure_description_id']);
                //(string&Int&Boolean) or List or Multitext
                $type=$structure_desc->type;
                if(($type == 'String') || ($type == 'Int') || ($type == 'Boolean') || ($type == 'List')){
                    $this->well_data->createStringIntBoolList($well,$structure_desc->id,$wellDataInput['data']);
                }elseif($type=='MultiText'){
                    $data = [
                        'pi' => $wellDataInput['data']['Pi'],
                        'Pd' => $wellDataInput['data']['Pd'],
                        'Ti' => $wellDataInput['data']['Ti'],
                        'Tm' => $wellDataInput['data']['Tm'],
                        'Ct' => $wellDataInput['data']['Ct'],
                    ];
                    $this->well_data->createStringIntBoolList($well,$wellDataInput['structure_description_id'],$data);
                }
            }elseif(!isset($wellDataInput['structure_description_id']) && isset($wellDataInput['structure_id']) && isset($wellDataInput['input']))
            {
                //date && multitext
                $this->structure->findOrfail($wellDataInput['structure_id']);
                $validator = Validator::make(['input' => $wellDataInput['input']], ['input' => 'date']);
                if($validator->fails())
                {
                    $this->well_data->createMultiTextWithInput($wellDataInput,$well);
                }else{
                    $this->well_data->createDateInputWithData($wellDataInput,$well);
                }
            }
        }
    }

    //4-publish from dataWell already exist(edit)
    public function publishOldWell($request,$published)
    {
        $well=$this->well->where('id',$request->well_id)->findOrfail($request->well_id);
        $well_data=$this->well_data->with('Structure_description')->where('well_id',$request->well_id)->get();
        $this->well->updateWell($well,$request,$published);
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
                elseif(!isset($wellDataInput['well_data_id'])){
                    if(isset($wellDataInput['structure_description_id'])){
                        $structure_desc=$this->structure_description->findOrfail($wellDataInput['structure_description_id']);
                        //(string&Int&Boolean) or List or Multitext
                        $type=$structure_desc->type;
                        if(($type == 'String') || ($type == 'Int') || ($type == 'Boolean') || ($type == 'List')){
                            $this->well_data->createStringIntBoolList($well,$structure_desc->id,$wellDataInput['data']);
                        }elseif($type=='MultiText'){
                            $data = [
                                'pi' => $wellDataInput['data']['Pi'],
                                'Pd' => $wellDataInput['data']['Pd'],
                                'Ti' => $wellDataInput['data']['Ti'],
                                'Tm' => $wellDataInput['data']['Tm'],
                                'Ct' => $wellDataInput['data']['Ct'],
                            ];
                            $this->well_data->createStringIntBoolList($well,$wellDataInput['structure_description_id'],$data);
                        }
                    }elseif(!isset($wellDataInput['structure_description_id']) && isset($wellDataInput['structure_id']) && isset($wellDataInput['input']))
                    {
                        //date && multitext
                        $this->structure->findOrfail($wellDataInput['structure_id']);
                        $validator = Validator::make(['input' => $wellDataInput['input']], ['input' => 'date']);
                        if($validator->fails())
                        {
                            $this->well_data->createMultiTextWithInput($wellDataInput,$well);
                        }else{
                            $this->well_data->createDateInputWithData($wellDataInput,$well);
                        }
                    }
                }
            }
        }
    }


    public function requestToEdit($request,$published,$wellRequest)
    {
        $well=$this->well->where('id',$wellRequest->well_id)->first();
        $well_data=$this->well_data->with('Structure_description')->where('well_id',$wellRequest->well_id)->get();
        $this->well->updateWell($well,$request,$published);
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
