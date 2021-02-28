<?php
namespace Modules\Blog\Services;

use Modules\Blog\Models\Category;
use Validator;

class CategoryService
{

    public function index()
    {
        $categories = Category::whereIsRoot()->orderBy('name')->get();

        return serviceOk($categories);

    }

    public function store($params)
    {

        $validator = Validator::make($params, [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return responseError($validator->errors(),400);
        }

        $category = new Category;

        $category->name = $params['name'];

        $category->save();

        if ($params['category'] == "root") {
            $category->makeRoot();
        } else {
            $category->parent_id = $params['category'];
            $category->save();
        }

        return serviceOk(trans('blog::messages.done'));
    }

    public function update($params)
    {

        $data = [
            'name' => $params['name'],
            'categories' => $params['categories'],
            'image' => $params['f_image'],
        ];
        $rules = ['name' => 'required'];

        $valid = Validator::make($data, $rules);
        if ($valid->fails()) {
            return Redirect::back()->withErrors($valid)->withInput();
        }

        $choice = "child";
        if (isset($params['is_root'])) {
            $choice = $params['radio'];
        }
        $id = $params['id'];
        $category = Category::findorFail($id);
        $category->name = $data['name'];
        $category->image = $data['image'];
        $category->save();
        if ($params['parent_id'] == "root") {
            $category->makeRoot();
        } else {
            $category->parent_id = $params['category'];
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
