<?php

namespace App;

use App\Image;
use App\Review;
use App\Business;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



    public function avatar()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function business()
    {
        return $this->hasOne(Business::class, 'owner_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value ? strtolower($value) : $value;
    }

    public function reviewAverage()
    {
        return $this->fresh()->reviews()->sum('rating') / $this->review_count;
    }

    public function ownerOf($business)
    {
        return $business->owner_id == $this->id;
    }

    public function addAvatar($image)
    {
        return $this->avatar()->create([
            'image_path' =>  $image->store('avatars')
        ]);
    }

    public function displayAvatar()
    {
        return $this->avatar ? asset('/storage/' . $this->avatar->image_path) : 'https://ui-avatars.com/api/?name=' . $this->name . '&color=7F9CF5&background=EBF4FF';
    }

    public function removeAvatar()
    {
        Storage::delete($this->avatar->image_path);
        $this->avatar()->delete();
    }
}
