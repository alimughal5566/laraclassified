<?php

namespace extras\plugins\reviews\app\Models;

use App\Models\BaseModel;
use App\Models\Scopes\LocalizedScope;
use App\Models\Scopes\ReviewedScope;
use App\Models\Scopes\VerifiedScope;
use App\Models\User;
use Jenssegers\Date\Date;
use Larapen\Admin\app\Models\Traits\Crud;

class Review extends BaseModel
{
	use Crud;
	
	/**
	 * The table associated with the model.
	 *
	 * @var string
	 */
	protected $table = 'reviews';
	
	/**
	 * The primary key for the model.
	 *
	 * @var string
	 */
	protected $primaryKey = 'id';
	public $incrementing = false;
	protected $appends = ['timeago'];
	
	/**
	 * Indicates if the model should be timestamped.
	 *
	 * @var boolean
	 */
	//public $timestamps = false;
	
	/**
	 * The attributes that aren't mass assignable.
	 *
	 * @var array
	 */
	protected $guarded = ['id'];
	
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'post_id',
		'user_id',
		'rating',
		'comment',
		'approved',
		'spam',
	];
	
	/**
	 * The attributes that should be hidden for arrays
	 *
	 * @var array
	 */
	// protected $hidden = [];
	
	/**
	 * The attributes that should be mutated to dates.
	 *
	 * @var array
	 */
	protected $dates = ['created_at', 'created_at'];
	
	/*
	|--------------------------------------------------------------------------
	| FUNCTIONS
	|--------------------------------------------------------------------------
	*/
	protected static function boot()
	{
		parent::boot();
		
		static::addGlobalScope(new LocalizedScope());
	}
	
	/**
	 * This function takes in Item ID, comment and the rating and attaches the review to the Item by its ID,
	 * then the average rating for the product is recalculated.
	 *
	 * @param $postId
	 * @param $comment
	 * @param $rating
	 */
	public function storeReviewForItem($postId, $comment, $rating)
	{
		$post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->find($postId);
		if (auth()->check()) {
			$this->user_id = auth()->user()->id;
		}
		$this->comment = $comment;
		$this->rating = $rating;
		$post->reviews()->save($this);
		
		// Recalculate ratings for the specified product
		$post->recalculateRating();
	}
	
	/**
	 * @param $reviewId
	 * @return bool
	 */
	public static function deleteReviewForItem($reviewId)
	{
		$review = self::find($reviewId);
		if (empty($review)) {
			return null;
		}
		$postId = $review->post_id;
		
		// Delete the review
		$review->delete();
		
		// Recalculate ratings for the specified product
		$post = Post::withoutGlobalScopes([VerifiedScope::class, ReviewedScope::class])->find($postId);
		if (empty($post)) {
			return null;
		}
		$post->recalculateRating();
		
		return $post;
	}
	
	/**
	 * @return string
	 */
	public function getPostTitleHtml()
	{
		$post = Post::find($this->post_id);
		
		return getPostUrl($post);
	}
	
	/**
	 * @return \Illuminate\Contracts\Translation\Translator|string
	 */
	public function getUserHtml()
	{
		if (!empty($this->user_id) && $this->user_id != 0) {
			$user = User::find($this->user_id);
			if (!empty($user)) {
				return $user->name;
			}
		}
		
		return trans('reviews::messages.Anonymous');
	}
	
	/**
	 * @return string
	 */
	public function getApprovedHtml()
	{
		if ($this->approved == 1) {
			return '<i class="admin-single-icon fa fa-toggle-on" aria-hidden="true"></i>';
		} else {
			return '<i class="admin-single-icon fa fa-toggle-off" aria-hidden="true"></i>';
		}
	}
	
	/*
	|--------------------------------------------------------------------------
	| RELATIONS
	|--------------------------------------------------------------------------
	*/
	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}
	
	public function post()
	{
		return $this->belongsTo(Post::class, 'post_id');
	}
	
	/*
	|--------------------------------------------------------------------------
	| SCOPES
	|--------------------------------------------------------------------------
	*/
	public function scopeApproved($query)
	{
		return $query->where('approved', 1);
	}
	
	public function scopeSpam($query)
	{
		return $query->where('spam', 1);
	}
	
	public function scopeNotSpam($query)
	{
		return $query->where('spam', 0);
	}
	
	/*
	|--------------------------------------------------------------------------
	| ACCESORS
	|--------------------------------------------------------------------------
	*/
	public function getTimeagoAttribute()
	{
		$date = Date::createFromTimeStamp(strtotime($this->created_at))->diffForHumans();
		
		return $date;
	}
	
	/*
	|--------------------------------------------------------------------------
	| MUTATORS
	|--------------------------------------------------------------------------
	*/
}
