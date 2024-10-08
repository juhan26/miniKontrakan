<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facility extends Model
{
    use HasFactory;

    protected $table = 'facilities';
    protected $fillable = [
        'photo',
        'name',
        'description'
    ];

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'property_facilities', 'facility_id', 'property_id');
    }

    public function facility_images()
    {
        return $this->hasMany(FacilityImage::class);
    }
}
