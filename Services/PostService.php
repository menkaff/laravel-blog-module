<?php
namespace Modules\Blog\Services;

use Modules\Blog\Models\Category;
use Modules\Blog\Models\Post;

class PostService
{

    public function recent_posts($count = 3)
    {
        return Post::orderBy('created_at', 'DESC')->limit($count)->get();
    }

    public function slides($count = 3)
    {

        $slider_cats = Category::where('name', 'LIKE', '%اسلایدر%')->pluck('id')->toArray();

        $slides = Post::whereHas('categories', function ($query) use ($slider_cats) {
            $query->whereIn('blog_post_category.category_id', $slider_cats);
        })->get();
        return $slides;

    }
}
