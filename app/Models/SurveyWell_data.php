<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Auth;

class SurveyWell_data extends Pivot
{
    use HasFactory;

    protected $table="survey_well_data";
    public $incrementing = true;
    protected $fillable=[
        'survey_well_id','survey_structure_description_id','data'
    ];

    public function Structure_description()
    {
        return $this->belongsTo(SurveyStructure_description::class,'survey_structure_description_id');
    }
    public function well()
    {
        return $this->belongsTo(SurveyWell::class,'survey_well_id');
    }

    public static function createStringIntBoolList($surveyWell,$surveyDataInput,$data)
    {
        SurveyWell_data::create([
            'survey_well_id'=>$surveyWell->id,
            'survey_structure_description_id'=> $surveyDataInput,
            'data'=> json_encode($data)
        ]);
    }

    public static function createMultiTextWithInput($surveyDataInput,$surveyWell)
    {
        $data = [
            'pi' => $surveyDataInput['data']['Pi'],
            'Pd' => $surveyDataInput['data']['Pd'],
            'Ti' => $surveyDataInput['data']['Ti'],
            'Tm' => $surveyDataInput['data']['Tm'],
            'Ct' => $surveyDataInput['data']['Ct'],
        ];
        $struct_desc=SurveyStructure_description::create([
            'structure_id'=>$surveyDataInput['survey_structure_id'],
            'input'=>$surveyDataInput['input'],
            'type'=>'MultiText',
            'user_id'=> Auth::guard('sanctum')->id()
        ]);
        SurveyWell_data::create([
            'survey_well_id'=>$surveyWell->id,
            'survey_structure_description_id'=>$struct_desc->id,
            'data' => json_encode($data)
        ]);
    }

    public static function createDateInputWithData($surveyDataInput, $surveyWell)
    {
        $struct_desc=SurveyStructure_description::create([
            'survey_structure_id'=>$surveyDataInput['survey_structure_id'],
            'input'=>$surveyDataInput['input'],
            'type'=>'String',
            'user_id'=> Auth::guard('sanctum')->id()
        ]);
        SurveyWell_data::create([
            'survey_well_id'=>$surveyWell->id,
            'survey_structure_description_id'=>$struct_desc->id,
            'data' => json_encode($surveyDataInput['data'])
        ]);
    }

    public static function updateDataStringIntBoolList($request,$well_data){
        $well_data->update([
            'data'=>json_encode($request->data)
        ]);
    }

}
