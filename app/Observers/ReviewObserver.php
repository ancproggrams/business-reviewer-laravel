<?php

namespace App\Observers;

use App\Review;
use App\Business;
use App\User;
use Illuminate\Support\Facades\DB;

class ReviewObserver
{
    /**
     * Handle the review "created" event.
     *
     * @param  \App\Review  $review
     * @return void
     */
    public function created(Review $review)
    {
        $this->refreshCounters($review->business_id, $review->user_id);
    }
    /**
     * Handle the review "updated" event.
     *
     * @param  \App\Review  $review
     * @return void
     */
    public function updated(Review $review)
    {
        $this->refreshCounters($review->business_id, $review->user_id);

        if ($review->getOriginal('business_id') && $review->getOriginal('business_id') != $review->business_id) {
            $this->refreshBusinessCounter($review->getOriginal('business_id'));
        }

        if ($review->getOriginal('user_id') && $review->getOriginal('user_id') != $review->user_id) {
            $this->refreshUserCounter($review->getOriginal('user_id'));
        }
    }

    /**
     * Handle the review "deleted" event.
     *
     * @param  \App\Review  $review
     * @return void
     */
    public function deleted(Review $review)
    {
        $this->refreshCounters($review->business_id, $review->user_id);
    }

    /**
     * Handle the review "restored" event.
     *
     * @param  \App\Review  $review
     * @return void
     */
    public function restored(Review $review)
    {
        $this->refreshCounters($review->business_id, $review->user_id);
    }

    /**
     * Handle the review "force deleted" event.
     *
     * @param  \App\Review  $review
     * @return void
     */
    public function forceDeleted(Review $review)
    {
        $this->refreshCounters($review->business_id, $review->user_id);
    }

    protected function refreshCounters($businessId, $userId)
    {
        DB::transaction(function () use ($businessId, $userId) {
            $this->refreshBusinessCounter($businessId);
            $this->refreshUserCounter($userId);
        });
    }

    protected function refreshBusinessCounter($businessId)
    {
        if (! $businessId) {
            return;
        }

        $stats = Review::where('business_id', $businessId)
            ->selectRaw('COUNT(*) AS review_count, AVG(rating) AS average_rating')
            ->first();

        Business::where('id', $businessId)->update([
            'average_review' => $stats && $stats->review_count ? (int) ceil($stats->average_rating) : 0,
        ]);
    }

    protected function refreshUserCounter($userId)
    {
        if (! $userId) {
            return;
        }

        $stats = Review::where('user_id', $userId)
            ->selectRaw('COUNT(*) AS review_count, AVG(rating) AS average_rating')
            ->first();

        User::where('id', $userId)->update([
            'review_count' => $stats ? (int) $stats->review_count : 0,
            'average_rating' => $stats && $stats->review_count ? round($stats->average_rating, 1) : 0,
        ]);
    }
}
