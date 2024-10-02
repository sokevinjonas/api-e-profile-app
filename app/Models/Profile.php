<?php

namespace App\Models;

use App\Models\User;
use App\Models\Service;
use App\Models\SocialLink;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Profile extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function socialLinks()
    {
        return $this->hasMany(SocialLink::class);
    }

    public function services()
    {
        return $this->hasMany(Service::class);
    }

}
