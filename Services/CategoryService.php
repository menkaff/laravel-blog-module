<?php
namespace Modules\Blog\Services;

use Modules\Blog\Models\Category;

class CategoryService
{

    public function root_categories_posts()
    {
        return Category::where("parent_id", null)->with('posts')->get();

    }

}
