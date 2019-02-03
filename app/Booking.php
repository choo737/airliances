<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{   
    protected $fillable = ['user_id', 'area_id', 'headcount', 'roomcount', 'stars', 'date_from', 'date_to'];
    //
    
    public function user()
    {
        return $this->belongsTo('App\User')->withDefault();
    }

    public function area()
    {
        return $this->belongsTo('App\Area')->withDefault();
    }
}
