<?php

namespace Modules\Blog\Http\Controllers\API\EndUser;

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

    public function show(Request $request)
    {
        $ad_service = new PostService;
        $result = $ad_service->show($request->all());
        if ($result['is_successful']) {
            return responseOk($result['data']);
        } else {
            return responseError($result['message']);

        }

    }

}
