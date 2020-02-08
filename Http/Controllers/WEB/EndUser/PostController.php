<?php

namespace Modules\Blog\Http\Controllers\WEB\EndUser;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Services\PostService;

class PostController extends Controller
{

    public function index(Request $request)
    {
        $ad_service = new PostService;
        $result = $ad_service->index($request->all());
        if ($result['is_successful']) {
            return responseOk($result['data']);
        } else {
            return responseError($result['message']);

        }
    }

    public function store(Request $request)
    {

        $ad_service = new PostService;
        $result = $ad_service->store($request->all(), $request);
        if ($result['is_successful']) {
            return responseOk(trans('veclu::messages.done'));
        } else {
            return responseError($result['message']);

        }

    }

    public function update(Request $request)
    {
        $ad_service = new PostService;
        $result = $ad_service->update($request->all(), $request);
        if ($result['is_successful']) {
            return responseOk(trans('veclu::messages.done'));
        } else {
            return responseError($result['message']);

        }

    }

    public function delete(Request $request)
    {
        $ad_service = new PostService;
        $result = $ad_service->delete($request->all());
        if ($result['is_successful']) {
            return responseOk(trans('veclu::messages.done'));
        } else {
            return responseError($result['message']);

        }

    }

}
