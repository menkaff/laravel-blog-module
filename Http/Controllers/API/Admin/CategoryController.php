<?php

namespace Modules\Blog\Http\Controllers\API\Admin;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Services\CategoryService;

class CategoryController extends Controller
{

    public function index(Request $request)
    {
        $category_service = new CategoryService;
        $result = $category_service->index($request->all());
        if ($result['is_successful']) {
            return responseOk($result['data']);
        } else {
            return responseError($result['message']);

        }
    }

    public function store(Request $request)
    {

        $category_service = new CategoryService;
        $result = $category_service->store($request->all(), $request);
        if ($result['is_successful']) {
            return responseOk(trans('veclu::messages.done'));
        } else {
            return responseError($result['message']);

        }

    }

    public function update(Request $request)
    {
        $category_service = new CategoryService;
        $result = $category_service->update($request->all(), $request);
        if ($result['is_successful']) {
            return responseOk(trans('veclu::messages.done'));
        } else {
            return responseError($result['message']);

        }

    }

    public function show(Request $request)
    {
        $category_service = new CategoryService;
        $result = $category_service->update($request->all(), $request);
        if ($result['is_successful']) {
            return responseOk(trans('veclu::messages.done'));
        } else {
            return responseError($result['message']);

        }

    }

    public function delete(Request $request)
    {
        $category_service = new CategoryService;
        $result = $category_service->delete($request->all());
        if ($result['is_successful']) {
            return responseOk(trans('veclu::messages.done'));
        } else {
            return responseError($result['message']);

        }

    }

}
