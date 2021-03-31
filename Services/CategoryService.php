<?php

namespace Modules\Blog\Services;

use Modules\Blog\Models\Category;
use phpDocumentor\Reflection\Types\Null_;
use Validator;

class CategoryService
{

    public function index($params = [])
    {
        $categories = Category::when(isset($params["parent_id"]), function ($query) use ($params) {
            $query->where("parent_id", $params['parent_id']);
        })
            ->when(isset($params["order_by"]), function ($query) use ($params) {
                $query->orderBy($params["order_by"], "desc");
            })
            ->when(isset($params["userable_id"]), function ($query) use ($params) {
                $query->where("userable_id", $params['userable_id']);
            })
            ->when(isset($params["userable_type"]), function ($query) use ($params) {
                $query->where("userable_type", $params['userable_type']);
            })
            ->get();

        return serviceOk($categories);
    }

    public function store($params)
    {

        $validator = Validator::make($params, [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return responseError($validator->errors(), 400);
        }

        $category = new Category;

        $category->name = $params['name'];
        $category->userable_id = $params['userable_id'];
        $category->userable_type = $params['userable_type'];

        $category->save();


        if (!isset($params['parent_id']) || (isset($params['parent_id']) && $params['parent_id'] == Null)) {
            $category->makeRoot();
        } elseif (isset($params['parent_id'])) {
            $category->parent_id = $params['parent_id'];
            $category->save();
        }

        return serviceOk(trans('blog::messages.done'));
    }

    public function update($params)
    {

        $validator = Validator::make($params, [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return responseError($validator->errors(), 400);
        }

        $category = Category::findorFail($params['id']);
        $category->name = $params['name'];


        if (!isset($params['parent_id']) || (isset($params['parent_id']) && $params['parent_id'] == Null)) {
            $category->save();
            $category->makeRoot();
        } elseif (isset($params['parent_id'])) {
            $parent = Category::findOrFail($params['parent_id']);
            if ($parent->parent_id == $params['id']) {
                return serviceError("wrong parent", 400);
            }
            $category->parent_id = $params['parent_id'];
            $category->save();
        }

        return serviceOk(trans('blog::messages.done'));
    }

    public function Show($params)
    {
        $id = $params['id'];
        $category = Category::findorFail($id);
        $categories = Category::where('parent_id', $id)->get();
        return serviceOk([
            'title' => trans('blog::messages.showchild') . ' ' . trans('blog::messages.category') . ' : ' . $category->name,
            'categories' => $categories,
        ]);
    }

    public function delete($params)
    {

        $id = $params['id'];
        $category = Category::findorFail($id);
        $category->delete();
        return serviceOk(trans('blog::messages.done'));
    }
}
