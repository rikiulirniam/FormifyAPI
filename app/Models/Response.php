<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    use HasFactory;
    protected $guarded =['id'];
    public $timestamps = false;

    public function form(){
        return $this->belongsTo(Form::class);
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function answer(){
        return $this->hasMany(Answer::class);
    }
}
