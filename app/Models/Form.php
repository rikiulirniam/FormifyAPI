<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $guarded = ['id'];
    
    public function creator(){
        return $this->belongsTo(User::class);
    }
    public function allowed_domains(){
        return $this->hasOne(AllowedDomain::class);
    }
    public function response(){
        return $this->hasMany(Response::class);
    }
    public function question(){
        return $this->hasMany(Question::class);
    }
}
