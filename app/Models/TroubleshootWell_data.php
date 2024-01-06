<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\Auth;

class TroubleshootWell_data extends Pivot
{
    use HasFactory;
    protected $table="troubleshoot_well_data";
    public $incrementing = true;
    protected $fillable=[
        'troubleshoot_well_id','troubleshot_struct_desc_id','data'
    ];

    public function Structure_description()
    {
        return $this->belongsTo(TroubleshootStructure_description::class,'troubleshoot_struct_desc_id');
    }
    public function well()
    {
        return $this->belongsTo(TroubleshootWell::class,'troubleshoot_well_id');
    }


    public static function createStringIntBoolList($well,$wellDataInput,$data)
    {
        TroubleshootWell_data::create([
            'troubleshoot_well_id'=>$well->id,
            'troubleshoot_struct_desc_id'=> $wellDataInput,
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
        $struct_desc=TroubleshootStructure_description::create([
            'troubleshoot_struct_id'=>$wellDataInput['structure_id'],
            'input'=>$wellDataInput['input'],
            'type'=>'MultiText',
            'user_id'=> Auth::guard('sanctum')->id()
        ]);
        TroubleshootWell_data::create([
            'troubleshoot_well_id'=>$well->id,
            'troubleshoot_struct_desc_id'=>$struct_desc->id,
            'data' => json_encode($data)
        ]);
    }

    public static function createDateInputWithData($wellDataInput, $well)
    {
        $struct_desc=TroubleshootStructure_description::create([
            'troubleshoot_struct_id'=>$wellDataInput['structure_id'],
            'input'=>$wellDataInput['input'],
            'type'=>'String',
            'user_id'=> Auth::guard('sanctum')->id()
        ]);
        TroubleshootWell_data::create([
            'troubleshoot_well_id'=>$well->id,
            'troubleshoot_struct_desc_id'=>$struct_desc->id,
            'data' => json_encode($wellDataInput['data'])
        ]);
    }

    public static function updateDataStringIntBoolList($request,$well_data){
        $well_data->update([
            'data'=>json_encode($request->data)
        ]);
    }

}
