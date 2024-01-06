<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TroubleshootStructure_description extends Model
{
    use HasFactory;use HasFactory;
    protected $table='troubleshoot_struct_desc';
    protected $fillable=[
        'troubleshoot_struct_id','input','type','is_require','data','user_id'
    ];

    public function structure()
    {
        return $this->belongsTo(TroubleshootStructure::class);
    }

    public function wells()
    {
        return $this->belongsToMany(TroubleshootWell::class,'troubleshoot_well_data','troubleshoot_struct_desc_id','troubleshoot_well_id','id','id')
            ->withPivot(['data'])
            ->using(TroubleshootWell_data::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
