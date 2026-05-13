<?php

namespace App;

use App\User;
use App\View;
use App\Image;
use App\Review;
use App\Business;
use App\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    protected $guarded = [];
    public $timestamps = true;

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function views()
    {
        return $this->morphMany(View::class, 'viewable');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->latest();
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable')->latest();
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function setEmailAttribute($value)
    {
        $this->attributes['email'] = $value ? strtolower($value) : $value;
    }

    public function addReview($body, $rating, $image = null, $userId = null)
    {
        $review = $this->reviews()
            ->create(
                [
                    'body' => $body,
                    'rating' => $rating,
                    'user_id' => $userId ? $userId : auth()->id(),
                    'showcased' => false,
                ]
            );

        if ($image) {
            $review->image()->create(['image_path' => $image->store('reviews')]);
        }

        return $review;
    }

    public function attachCategories($categories)
    {
        foreach ($categories as $categoryId) {
            $this->categories()->attach(['category_id' => $categoryId]);
        }
    }

    public function addCategory($categoryId)
    {
        return $this->categories()->attach(['category_id' => $categoryId]);
    }

    public function path()
    {
        return '/businesses/' . $this->slug;
    }

    public function image()
    {
        if (Str::startsWith($this->front_image, 'images/')) {
            return $this->front_image;
        }

        return  'storage/' . $this->front_image;
    }

    public function reviewedAlready()
    {
        return $this->reviews()->where(['user_id' => auth()->id()])->exists();
    }

    public function addImage($image)
    {
        return $this->images()->create([
            'image_path' =>  $image->store('guest_uploads')
        ]);
    }

    public function reviewCount()
    {
        return $this->reviews()->count();
    }

    public function incrementViewCount($user = null)
    {
        $this->views()->create();
    }

    public function viewCount()
    {
        return $this->views->count();
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}
