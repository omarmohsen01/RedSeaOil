<?php

namespace App\Http\Controllers\Services\Front;
use App\Http\Controllers\Interfaces\Front\SurveyWellDataServiceInterface;
use App\Models\Structure;
use App\Models\Structure_description;
use App\Models\SurveyStructure;
use App\Models\SurveyStructure_description;
use App\Models\SurveyWell;
use App\Models\SurveyWell_data;
use App\Models\Well;
use App\Models\Well_data;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SurveyWellDataService implements SurveyWellDataServiceInterface
{
    public $well,$well_data,$structure_description,$structure;
    public function __construct(SurveyWell $well,SurveyWell_data $well_data,SurveyStructure_description $structure_description,SurveyStructure $structure)
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
    public function publishNewSurveyWell($request,$published)
    {
        $well=Well::findOrFail($request->well_id)->first();
        if($well){
            $surveyWell=SurveyWell::create([
                'well_id'=>$request->well_id,
                'user_id'=>Auth::id(),
                'published'=>($published=='published')?'published':'as_draft',
            ]);
            $surveyDataInputs= $request->json('survey_data');
            foreach($surveyDataInputs as $surveyDataInput){
                if(isset($surveyDataInput['survey_structure_description_id'])){
                    $survey_structure_desc=SurveyStructure_description::findOrfail($surveyDataInput['survey_structure_description_id']);
                    //(string&Int&Boolean) or List or Multitext
                    $type=$survey_structure_desc->type;
                    if(($type == 'String') || ($type == 'Int') || ($type == 'Boolean') || ($type == 'List')){
                        SurveyWell_data::createStringIntBoolList($surveyWell,$survey_structure_desc->id,$surveyDataInput['data']);
                    }elseif($type=='MultiText'){
                        $data = [
                            'pi' => $surveyDataInput['data']['Pi'],
                            'Pd' => $surveyDataInput['data']['Pd'],
                            'Ti' => $surveyDataInput['data']['Ti'],
                            'Tm' => $surveyDataInput['data']['Tm'],
                            'Ct' => $surveyDataInput['data']['Ct'],
                        ];
                        SurveyWell_data::createStringIntBoolList($surveyWell,$surveyDataInput['survey_structure_description_id'],$data);
                    }
                }elseif(!isset($surveyDataInput['survey_structure_description_id'])
                        && isset($surveyDataInput['survey_structure_id'])
                        && isset($surveyDataInput['input']))
                {
                    //date && multitext
                    SurveyStructure::findOrfail($surveyDataInput['survey_structure_id']);
                    $validator = Validator::make(['input' => $surveyDataInput['input']], ['input' => 'date']);
                    if($validator->fails())
                    {
                        SurveyWell_data::createMultiTextWithInput($surveyDataInput,$surveyWell);
                    }else{
                        SurveyWell_data::createDateInputWithData($surveyDataInput,$surveyWell);
                    }
                }
            }
        }
    }

    //4-publish from dataWell already exist(edit)
    public function publishOldSurveyWell($request,$published)
    {
        $survey_well=SurveyWell::findOrFail($request->survey_well_id)->first();
        $survey_well_data=SurveyWell_data::with('Structure_description')->where('survey_well_id',$request->survey_well_id)->get();
        SurveyWell::updateSurveyWell($survey_well,$published);
        $surveyDataInputs= $request->json('survey_data');

        if(!empty($surveyDataInputs)){
            foreach($surveyDataInputs as $surveyDataInput){
                //for edit old data
                if(isset($surveyDataInput['survey_well_data_id'])){
                    $survey_well_data_id=$surveyDataInput['survey_well_data_id'];
                    $survey_well_data=SurveyWell_data::with('Structure_description')->where('id',$survey_well_data_id)->first();
                    $type=$survey_well_data->Structure_description->type;
                    if(!isset($surveyDataInput['input'])){
                        if(($type == 'String') || ($type == 'Int') || ($type == 'Boolean') || ($type=='List')){
                            $survey_well_data->update([
                                'data'=>json_encode($surveyDataInput['data'])
                            ]);
                        }elseif($type=='MultiText'){
                                $data = [
                                    'pi' => $surveyDataInput['data']['Pi'],
                                    'Pd' => $surveyDataInput['data']['Pd'],
                                    'Ti' => $surveyDataInput['data']['Ti'],
                                    'Tm' => $surveyDataInput['data']['Tm'],
                                    'Ct' => $surveyDataInput['data']['Ct'],
                                ];
                                $survey_well_data->update([
                                    'data'=>json_encode($data)
                                ]);
                        }
                    }else{
                        if($type=='MultiText'){
                            $data = [
                                'pi' => $surveyDataInput['data']['Pi'],
                                'Pd' => $surveyDataInput['data']['Pd'],
                                'Ti' => $surveyDataInput['data']['Ti'],
                                'Tm' => $surveyDataInput['data']['Tm'],
                                'Ct' => $surveyDataInput['data']['Ct'],
                            ];
                            $survey_well_data->update([
                                'input'=>json_encode($surveyDataInput['input']),
                                'data'=>json_encode($data)
                            ]);
                        }else{
                            $survey_well_data->update([
                                'input'=>json_encode($surveyDataInput['input']),
                                'data'=>json_encode($surveyDataInput['data'])
                            ]);
                        }
                    }
                }
                //for store new data
                elseif(!isset($surveyDataInput['survey_well_data_id'])){
                    if(isset($surveyDataInput['survey_structure_description_id'])){
                        $survey_structure_desc=SurveyStructure_description::findOrfail($surveyDataInput['survey_structure_description_id']);
                        //(string&Int&Boolean) or List or Multitext
                        $type=$survey_structure_desc->type;
                        if(($type == 'String') || ($type == 'Int') || ($type == 'Boolean') || ($type == 'List')){
                            SurveyWell_data::createStringIntBoolList($survey_well,$survey_structure_desc->id,$surveyDataInput['data']);
                        }elseif($type=='MultiText'){
                            $data = [
                                'pi' => $surveyDataInput['data']['Pi'],
                                'Pd' => $surveyDataInput['data']['Pd'],
                                'Ti' => $surveyDataInput['data']['Ti'],
                                'Tm' => $surveyDataInput['data']['Tm'],
                                'Ct' => $surveyDataInput['data']['Ct'],
                            ];
                            SurveyWell_data::createStringIntBoolList($survey_well,$surveyDataInput['survey_structure_description_id'],$data);
                        }
                    }elseif(!isset($surveyDataInput['survey_structure_description_id']) && isset($surveyDataInput['survey_structure_id']) && isset($surveyDataInput['input']))
                    {
                        //date && multitext
                        SurveyStructure::findOrfail($surveyDataInput['survey_structure_id']);
                        $validator = Validator::make(['input' => $surveyDataInput['input']], ['input' => 'date']);
                        if($validator->fails())
                        {
                            $this->well_data->createMultiTextWithInput($surveyDataInput,$survey_well);
                        }else{
                            $this->well_data->createDateInputWithData($surveyDataInput,$survey_well);
                        }
                    }
                }
            }
        }
    }


    public function requestToEdit($request,$published,$wellRequest)
    {
        // $well=$this->well->where('id',$wellRequest->well_id)->first();
        $well_data=$this->well_data->with('Structure_description')->where('survey_well_id',$wellRequest->survey_well_id)->get();
        // $this->well->updateWell($well,$request,$published);
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
