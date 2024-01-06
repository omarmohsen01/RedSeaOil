<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class TestWell extends Model
{
    use HasFactory;
    protected $table='test_wells';
    protected $fillable=[
        'well_id','user_id','published'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function requests()
    {
        return $this->hasMany(Request::class);
    }

    public function Structure_descriptions()
    {
        return $this->belongsToMany(TestStructure_description::class
            ,'test_well_data','test_well_id','test_structure_description_id')
            ->withPivot(['data'])
            ->using(TestWell_data::class);
    }

    public function well_data()
    {
        return $this->hasMany(TestWell_data::class);
    }
    // public static function createWell($request,$published)
    // {
    //     $well=TestWell::create([
    //         'name' => $request->post('name'),
    //         'from' => $request->post('from'),
    //         'to' => $request->post('to'),
    //         'user_id' => Auth::guard('sanctum')->id(),
    //         'published'=>($published=='published')?'published':'as_draft',
    //     ]);
    //     return $well;
    // }

    public static function updateWell($testWell,$published)
    {
        $testWell->update([
            'published'=>($published=='published')?'published':'last_draft',
        ]);
    }

    // public function scopeFilter(Builder $builder, $filter)
    // {
    //     $options=array_merge([
    //         'name'=>null,
    //         'user_id'=>null,
    //         'from'=>null,
    //         'to'=>null,
    //     ],$filter);


    //     $builder->when($options['name'],function($query,$name){
    //         return $query->where('name',$name);
    //     });

    //     $builder->when($options['user_id'],function($query,$user){
    //         return $query->where('user_id',$user);
    //     });

    //     $builder->when($options['from'],function($query,$from){
    //         return $query->where('from',$from);
    //     });

    //     $builder->when($options['to'],function($query,$to){
    //         return $query->where('to',$to);
    //     });

    // }
}
