<?php

namespace App;

use App\User;
use App\Business;
use DateTimeInterface;
use App\Traits\Reactionable;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{

    use Reactionable;

    protected $guarded = [];
    protected $appends = ['isReactedFunny', 'isReactedUseful'];
    public $timestamps = true;
    public $casts = [
        'showcased' => 'boolean',

    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function image()
    {
        return $this->morphOne(Image::class, 'imageable');
    }

    public function business()
    {
        return $this->belongsTo(Business::class, 'business_id');
    }


    public function reply()
    {
        return $this->hasOne(Reply::class);
    }

    public function addReply($body, $user = null)
    {
        return $this->reply()->create(['body' => $body, 'owner_id' => $user ? $user->id : auth()->id()]);
    }

    public function funnyCount()
    {
        return $this->reactions()->where(['type' => 'funny'])->count();
    }
    public function usefulCount()
    {
        return $this->reactions()->where(['type' => 'useful'])->count();
    }

    public function getIsReactedFunnyAttribute()
    {
      if (auth()->user()) {
       return $this->reactionExists('funny');
      }

      return false;
    }

     public function getIsReactedUsefulAttribute()
    {
      if (auth()->user()) {
        return $this->reactionExists('useful');
      }

      return false;
    }
}
