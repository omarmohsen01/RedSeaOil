<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Troubleshoot extends Model
{
    use HasFactory;
    protected $table="troubleshoots";
    protected $fillable=[
        'name'
    ];

    public static function createTroubleshootWithStruct_StructDesc(Request $request)
    {
        $troubleshoot = Troubleshoot::create([
            'name'=>$request->troubleshootName
        ]);
        $structure=TroubleshootStructure::create([
            'troubleshoot_id'=>$troubleshoot->id,
            'name'=>$request->structureName
        ]);
        if(isset($request->structuresDes)){
            foreach($request->structuresDes as $type=>$struc){
                TroubleshootStructure_description::create([
                    'troubleshoot_struct_id'=>$structure->id,
                    'input'=>$struc['input'],
                    'type'=>$struc['type'],
                    'is_require'=>(!isset($struc['is_require'])|| !($struc['is_require'])=='Required')?'Optional':'Required',
                    'user_id'=>Auth::id()
                ]);
            }
        }
        if(isset($request->structuresDesMenu)){
            foreach($request->structuresDesMenu as $type=>$struc){
                TroubleshootStructure_description::create([
                    'troubleshoot_struct_id'=>$structure->id,
                    'input'=>$struc['input'],
                    'type'=>$struc['type'],
                    'is_require'=>(!isset($struc['is_require'])|| !($struc['is_require'])=='Required')?'Optional':'Required',
                    'data'=>json_encode($struc['data']),
                    'user_id'=>Auth::id()
                ]);
            }
        }
    }

    public static function createTroubleshootWithStruct(Request $request)
    {
        $troubleshoot=Troubleshoot::create([
            'name'=>$request->troubleshootName
        ]);
        TroubleshootStructure::create([
            'troubleshoot_id'=>$troubleshoot->id,
            'name'=>$request->structureName
        ]);
    }

    public static function createTroubleshoot(Request $request)
    {
        Troubleshoot::create([
            'name'=>$request->troubleshootName
        ]);
    }
    public function structures()
    {
        return $this->hasMany(TroubleshootStructure::class);
    }

}
