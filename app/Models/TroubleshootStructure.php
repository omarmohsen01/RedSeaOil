<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TroubleshootStructure extends Model
{
    use HasFactory;
    protected $table="troubleshoot_structs";
    protected $fillable=[
        'name','troubleshoot_id','order'
    ];
    public function troubleshoot()
    {
        return $this->belongsTo(Troubleshoot::class);
    }

    public function structure_descriptions()
    {
        return $this->hasMany(TroubleshootStructure_description::class);
    }
    public static function createStruct(Request $request)
    {
        $str=TroubleshootStructure::create([
            'troubleshoot_id'=>$request->troubleshoot_id,
            'name'=>$request->structureName
        ]);
        return $str;
    }
    public static function createDesc_Menu(Request $request,$id)
    {
        if(isset($request->structuresDes)){
            foreach($request->structuresDes as $type=>$struc){
                TroubleshootStructure_description::updateOrcreate([
                    'troubleshoot_struct_id'=>$id,
                    'input'=>$struc['input'],
                    'type'=>$struc['type'],
                    'is_require'=>(!isset($struc['is_require'])|| !($struc['is_require'])=='Required')?'Optional':'Required',
                    'user_id'=>Auth::id()
                ]);
            }
        }
        if(isset($request->structuresDesMenu)){
            foreach($request->structuresDesMenu as $type=>$struc){
                TroubleshootStructure_description::updateOrcreate([
                    'troubleshoot_struct_id'=>$id,
                    'input'=>$struc['input'],
                    'type'=>$struc['type'],
                    'is_require'=>(!isset($struc['is_require'])|| !($struc['is_require'])=='Required')?'Optional':'Required',
                    'data'=>json_encode($struc['data']),
                    'user_id'=>Auth::id()
                ]);
            }
        }
    }
    public static function createStruct_Desc_menu(Request $request)
    {
        $structure=TroubleshootStructure::createStruct($request);
        TroubleshootStructure::createDesc_Menu($request, $structure->id);
    }

    public static function UpdateOptionStruc(Request $request,$id)
    {
        $structure=TroubleshootStructure::findOrfail($id);
        $structure->troubleshoot_id=$request->troubleshoot_id;
        $structure->name=$request->structureName;
        return $structure;
    }

    public static function UpdateStrucDesc(Request $request,$id)
    {
        $structure=TroubleshootStructure::UpdateOptionStruc($request,$id);
        foreach($request->structuresDesUpdate as $type=>$struc)
        {
            if(isset($struc['id'])){
                $struc_desc=TroubleshootStructure_description::findOrfail($struc['id']);
                $struc_desc->input=$struc['input'];
                $struc_desc->type=$struc['type'];
                $struc_desc->is_require=(!isset($struc['is_require'])|| !($struc['is_require'])=='Required')?'Optional':'Required';
                if(isset($struc['data'])){
                    $struc_desc->data = json_encode($struc['data']);
                }
                $struc_desc->save();
            }
            $structure->save();
        }
    }
   public static function booted()
    {
        static::creating(function(TroubleshootStructure $structure)
        {
            $structure->order=TroubleshootStructure::listOrderStructure();
        });
    }
    public static function listOrderStructure()
    {
        $order=TroubleshootStructure::max('order');
        if($order)
        {
            return $order+1;
        }
        return '1';
    }

}
