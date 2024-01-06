<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Auth;

class TestWell_data extends Pivot
{
    use HasFactory;

    protected $table="test_well_data";
    public $incrementing = true;
    protected $fillable=[
        'test_well_id','test_structure_description_id','data'
    ];

    public function Structure_description()
    {
        return $this->belongsTo(TestStructure_description::class,'test_structure_description_id');
    }
    public function well()
    {
        return $this->belongsTo(TestWell::class,'test_well_id');
    }

    public static function createStringIntBoolList($testWell,$testDataInput,$data)
    {
        TestWell_data::create([
            'test_well_id'=>$testWell->id,
            'test_structure_description_id'=> $testDataInput,
            'data'=> json_encode($data)
        ]);
    }

    public static function createMultiTextWithInput($testDataInput,$well)
    {
        $data = [
            'pi' => $testDataInput['data']['Pi'],
            'Pd' => $testDataInput['data']['Pd'],
            'Ti' => $testDataInput['data']['Ti'],
            'Tm' => $testDataInput['data']['Tm'],
            'Ct' => $testDataInput['data']['Ct'],
        ];
        $struct_desc=TestStructure_description::create([
            'test_structure_id'=>$testDataInput['structure_id'],
            'input'=>$testDataInput['input'],
            'type'=>'MultiText',
            'user_id'=> Auth::guard('sanctum')->id()
        ]);
        TestWell_data::create([
            'test_well_id'=>$well->id,
            'test_structure_description_id'=>$struct_desc->id,
            'data' => json_encode($data)
        ]);
    }

    public static function createDateInputWithData($testDataInput, $well)
    {
        $struct_desc=TestStructure_description::create([
            'test_structure_id'=>$testDataInput['test_structure_id'],
            'input'=>$testDataInput['input'],
            'type'=>'String',
            'user_id'=> Auth::guard('sanctum')->id()
        ]);
        TestWell_data::create([
            'test_well_id'=>$well->id,
            'test_structure_description_id'=>$struct_desc->id,
            'data' => json_encode($testDataInput['data'])
        ]);
    }

    public static function updateDataStringIntBoolList($request,$well_data){
        $well_data->update([
            'data'=>json_encode($request->data)
        ]);
    }

}
