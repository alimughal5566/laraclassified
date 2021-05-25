<?php

namespace extras\plugins\reviews\app\Models;

class Post extends \App\Models\Post
{
    /*
    |--------------------------------------------------------------------------
    | FUNCTIONS
    |--------------------------------------------------------------------------
    */
    /**
     * The way average rating is calculated (and stored) is by getting an average of all ratings,
     * storing the calculated value in the rating_cache column (so that we don't have to do calculations later)
     * and incrementing the rating_count column by 1
     */
    public function recalculateRating()
    {
        $reviews = $this->reviews()->notSpam()->approved();
        $avgRating = $reviews->avg('rating');
        
        $avgRating = round($avgRating, 1);
        
        // Use valid data for MySQL
        $avgRating = str_replace(',', '.', $avgRating);
        $avgRating = preg_replace('/[^0-9\.]/', '', $avgRating);
        
        $this->rating_cache = $avgRating;
        $this->rating_count = $reviews->count();
        $this->save();
    }
	
	/**
	 * Get average rating (from all the ratings received) by User
	 *
	 * @param $userId
	 * @return float|mixed|null|string|string[]
	 */
	public function getRatingByUser($userId)
	{
		$reviews = Review::notSpam()->approved()->whereHas('post', function($q) use ($userId) {
			$q->where('user_id', $userId);
		});
		$avgRating = $reviews->avg('rating');
		
		$avgRating = round($avgRating, 1);
		
		// Use valid data for MySQL
		$avgRating = str_replace(',', '.', $avgRating);
		$avgRating = preg_replace('/[^0-9\.]/', '', $avgRating);
		
		return $avgRating;
	}
	
	/**
	 * Count the number of ratings received by the User's posts
	 *
	 * @param $userId
	 * @return mixed
	 */
	public function getCountRatingByUser($userId)
	{
		$ratingCount = self::where('user_id', $userId)->sum('rating_count');
		
		return $ratingCount;
	}
    
    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    public function reviews()
    {
        return $this->hasMany(Review::class, 'post_id');
    }
    
    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    
    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */
    
    /*
    |--------------------------------------------------------------------------
    | MUTATORS
    |--------------------------------------------------------------------------
    */
}
