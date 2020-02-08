<?php
namespace Modules\Blog\Http\Controllers\WEB\EndUser;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Models\Page;

class PageController extends Controller
{

    public function show(Request $request)
    {
        $page = Page::where(['id' => $request->id, 'status' => 1])->first();

        return view('theme::blog.page.show')->with(['page' => $page]);
    }

}
