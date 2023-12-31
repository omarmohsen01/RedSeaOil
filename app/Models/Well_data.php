<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Auth;

class Well_data extends Pivot
{
    use HasFactory;

    protected $table="well_data";
    public $incrementing = true;
    protected $fillable=[
        'well_id','structure_description_id','data'
    ];

    public function Structure_description()
    {
        return $this->belongsTo(Structure_description::class,'structure_description_id');
    }
    public function well()
    {
        return $this->belongsTo(Well::class,'well_id');
    }

    public static function createStringIntBoolList($well,$wellDataInput,$data)
    {
        Well_data::create([
            'well_id'=>$well->id,
            'structure_description_id'=> $wellDataInput,
            'data'=> json_encode($data)
        ]);
    }

    public static function createMultiTextWithInput($wellDataInput,$well)
    {
        $data = [
            'pi' => $wellDataInput['data']['Pi'],
            'Pd' => $wellDataInput['data']['Pd'],
            'Ti' => $wellDataInput['data']['Ti'],
            'Tm' => $wellDataInput['data']['Tm'],
            'Ct' => $wellDataInput['data']['Ct'],
        ];
        $struct_desc=Structure_description::create([
            'structure_id'=>$wellDataInput['structure_id'],
            'input'=>$wellDataInput['input'],
            'type'=>'MultiText',
            'user_id'=> Auth::guard('sanctum')->id()
        ]);
        Well_data::create([
            'well_id'=>$well->id,
            'structure_description_id'=>$struct_desc->id,
            'data' => json_encode($data)
        ]);
    }

    public static function createDateInputWithData($wellDataInput, $well)
    {
        $struct_desc=Structure_description::create([
            'structure_id'=>$wellDataInput['structure_id'],
            'input'=>$wellDataInput['input'],
            'type'=>'String',
            'user_id'=> Auth::guard('sanctum')->id()
        ]);
        Well_data::create([
            'well_id'=>$well->id,
            'structure_description_id'=>$struct_desc->id,
            'data' => json_encode($wellDataInput['data'])
        ]);
    }

    public static function updateDataStringIntBoolList($request,$well_data){
        $well_data->update([
            'data'=>json_encode($request->data)
        ]);
    }

}
