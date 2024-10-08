<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instance extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $table = 'instances';

    public function users(){
        return $this->hasMany(User::class);
    }
}
