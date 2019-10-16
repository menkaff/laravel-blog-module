<?php
namespace Modules\Blog\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Auth\Models\User;

class Post extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'blog_post';

    public function user()
    {
        return $this->belongsTo('Modules\Auth\Models\User', 'user_id')->withDefault(
            ['id' => 0, 'name' => 'نامشخص']);

    }

    public function categories()
    {
        return $this->belongsToMany('Modules\Blog\Models\Category', 'blog_post_category', 'post_id', 'category_id');

    }

    public function comments()
    {
        return $this->hasMany('Modules\Blog\Models\Comment');
    }
}
