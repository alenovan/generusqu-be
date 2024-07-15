<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
class EventDetails extends Model
{
    protected $table = 'event_details';

    public function event()
    {
        return $this->hasOne('App\Models\Events', 'id', 'event_id');
    }

    public function users()
    {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }


    public function getPhotoUrlAttribute()
    {
        if (!empty($this->photo)) {
            return Storage::url($this->photo);
        }

        return null;
    }
}