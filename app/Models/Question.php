<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $guarded = ['id'];
    
    public function form(){
        return $this->belongsTo(Form::class);
    }
    public function answer(){
        return $this->hasMany(Answer::class);
    }
    
}
