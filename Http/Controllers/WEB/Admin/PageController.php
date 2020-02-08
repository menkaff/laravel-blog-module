<?php
namespace Modules\Blog\Http\Controllers\WEB\Admin;

use Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Auth\Models\User;
use Modules\Blog\Models\Page;
use Redirect;
use Validator;

class PageController extends Controller
{

    public function index()
    {
        $pages = Page::get();

        return view('blog::page.index')->with('pages', $pages);

    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create(Request $request)
    {

        return view('blog::page.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $page = new Page;

        $data = [
            'title' => $request->input('title'),
            'content' => $request->input('content')];

        $rules = ['title' => 'required', 'content' => 'required'];

        $valid = Validator::make($data, $rules);
        if ($valid->fails()) {
            return Redirect::back()->withErrors($valid)->withInput();
        }

        $page->status = $request->input('status');
        $page->title = $request->input('title');
        $page->url = str_replace(' ', '-', $request->input('title'));
        $page->f_image = parse_url($request->input('filepath'), PHP_URL_PATH);
        $page->user_id = Auth::user()->id;
        $page->content = $request->input('content');
        $page->excerpt = str_limit(strip_tags($request->input('content'), 50));

        $page->save();

        return Redirect::back()->withErrors(trans('blog::messages.done'));
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function report(Request $request)
    {
        $page = Page::find($request->input('id'));

        return view('blog::page.report')->with('page', $page);
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit(Request $request)
    {
        $id = $request->input('id');
        $page = page::find($id);

        return view('blog::page.edit')->with(
            'page', $page);

    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
        $id = $request->input('id');

        $data = [
            'title' => $request->input('title'),
            'content' => $request->input('content')];

        $rules = ['title' => 'required', 'content' => 'required'];

        $valid = Validator::make($data, $rules);
        if ($valid->fails()) {
            return Redirect::back()->withErrors($valid)->withInput();
        }

        $page = Page::find($id);

        $page->status = $request->input('status');
        $page->title = $request->input('title');
        $page->url = str_replace(' ', '-', $request->input('title'));
        $page->f_image = parse_url($request->input('filepath'), PHP_URL_PATH);

        $page->content = $request->input('content');
        $page->excerpt = str_limit(strip_tags($page->content, 50));
        if ($request->input('is_comment') == -1) {
            $page->is_comment = -1;
        }

        $page->save();

        return Redirect::back()->withErrors(trans('blog::messages.done'));
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function delete(Request $request)
    {
        if ($request->has('ids')) {
            $ids = $request->input('ids');
        } else {
            $ids = array($request->input('id'));
        }

        foreach ($ids as $id) {

            $delete = page::where('id', $id)->delete();

        }
        if (!$request->ajax()) {
            return Redirect::back()->withErrors(trans('blog::messages.done'));
        }

    }

}
