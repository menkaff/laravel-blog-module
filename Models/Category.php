<?php

namespace Modules\Blog\Models;

use DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Kalnoy\Nestedset\NodeTrait;

class Category extends Model
{
    use NodeTrait;
    use HasFactory;

    protected $table = 'blog_category';

    protected $fillable = ['name', 'image'];

    protected static function newFactory()
    {
        return \Modules\Blog\Database\factories\CategoryFactory::new();
    }

    public function userable()
    {
        return $this->morphTo();
    }

    public function posts()
    {
        return $this->belongsToMany('Modules\Blog\Models\Post', 'blog_post_category', 'category_id', 'post_id');
    }

    public static function render($categories = array(), $is_checkbox = 'checkbox', $is_leaf_only = false, $is_root = true, $is_category = true, $edit_id = 0)
    {

        function renderNodeCategory($node, $categories, $is_checkbox = 'checkbox', $is_leaf_only = false, $edit_id = 0)
        {

            echo "<li>";

            $checked = '';
            if (in_array($node->id, $categories)) {
                $checked = 'checked';
            }

            if ($node->children()->count() > 0) {
                // echo "<div class='btn btn-primary'>";
                echo "<div class='hl'></div> <span class='tree_node_title'>  {$node->name} </span> ";
                echo "<span class='tree_node_info'>";

                echo "</span>";
                if (!$is_leaf_only) {
                    if ($edit_id != $node->id) {

                        if ($is_checkbox == "checkbox") {
                            echo "<input  class='child tree_node_size'  type='{$is_checkbox}' name='categories[]' value='{$node->id}' $checked>";
                        } else {
                            echo "<input  class='child tree_node_size'  type='{$is_checkbox}' name='category' value='{$node->id}' $checked>";
                        }
                    }
                }

                // echo "<br /><div class='btn btn-success'> تعداد دسته بندی های زیر شاخه : <span class='badge'>{$node->children()->count()}</span></div>";
                // echo "<br /><div class='btn btn-info'> تعداد کالاهای عمومی : <span class='badge'>{$node->product_count()}</span></div>";

                echo "<ul class='tree'>";
                foreach ($node->children as $child) {
                    renderNodeCategory($child, $categories, $is_checkbox, $is_leaf_only, $edit_id);
                }
                echo "</ul>";
                // echo "</div>";
            } else {
                // echo "<div class='btn btn-danger'>";
                echo "<div class='hl'></div> <span class='tree_node_title'>  {$node->name} </span>";
                echo "<span class='tree_node_info'>";

                echo "</span>";

                if ($edit_id != $node->id) {

                    if ($is_checkbox == "checkbox") {
                        echo "<input  class='child tree_node_size'  type='{$is_checkbox}' name='categories[]' value='{$node->id}' $checked>";
                    } else {
                        echo "<input  class='child tree_node_size'  type='{$is_checkbox}' name='category' value='{$node->id}' $checked>";
                    }
                }

                // echo "<br /><div class='btn btn-info'> تعداد کالاهای عمومی : <span class='badge'>{$node->product_count()}</span></div>";
                // echo "</div>";
            }

            echo "</li>";
        }

        echo "<ul class='tree'>";
        $roots = Category::whereIsRoot()->get();

        $checked = '';
        if ($is_root) {
            $checked = 'checked';
        }

        if ($is_category) {
            echo "<div class='hl'></div> <span  class='tree_node_root'>انتخاب به عنوان ریشه </span>";
            if ($is_checkbox == "checkbox") {
                echo "<input  class='child tree_node_size'  type='{$is_checkbox}' name='categories[]' value='root' $checked>";
            } else {
                echo "<input  class='child tree_node_size'  type='{$is_checkbox}' name='category' value='root' $checked>";
            }
        }

        foreach ($roots as $root) {
            renderNodeCategory($root, $categories, $is_checkbox, $is_leaf_only, $edit_id);
        }
        echo "</ul>";
    }

    public static function renderlinks()
    {

        function renderNode($node)
        {
            echo "<li>";

            if ($node->children()->count() > 0) {

                echo "<a href='/blog/posts?category_id={$node->id}'><span>{$node->name}</span></a>";
                echo "<ul >";
                foreach ($node->children as $child) {
                    renderNode($child);
                }

                echo "</ul>";
            } else {
                echo "<a href='/blog/posts?category_id={$node->id}'><span>{$node->name} </span></a>";
            }

            echo "</li>";
        }

        echo "<ul  >";
        $roots = Category::whereIsRoot()->get();

        foreach ($roots as $root) {
            renderNode($root);
            echo "<hr />";
        }
        echo "</ul>";
    }
}
