<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestRequest extends Model
{
    use HasFactory;
    protected $fillable = [
        "user_id","test_well_id","description","status"
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function test_well()
    {
        return $this->belongsTo(TestWell::class);
    }
}