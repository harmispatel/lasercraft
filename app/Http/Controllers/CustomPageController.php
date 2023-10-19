<?php

namespace App\Http\Controllers;

use App\Models\CustomPage;
use Illuminate\Http\Request;

class CustomPageController extends Controller
{
    public function index()
    {
        $custom_pages = CustomPage::get();
        return view('client.custom_pages.custom_page', compact(['custom_pages']));
    }

    public function create()
    {
        return view('client.custom_pages.create_custom_page');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:custom_pages,name',
        ]);

        try
        {
            $name = $request->name;
            $page_slug = str_replace(' ', '_', $name)."_page";
            $page_slug = strtolower($page_slug);
            $content = $request->content;

            $custom_page =  new CustomPage();
            $custom_page->name = $name;
            $custom_page->page_slug = $page_slug;
            $custom_page->content = $content;
            $custom_page->save();

            return redirect()->route('custom.pages')->with('success', 'Page has been Created SuccessFully..');

        }
        catch (\Throwable $th)
        {
            return redirect()->back()->with('error', 'Internal Server Error!');
        }

    }

    function edit($id)
    {
        try
        {
            $page_id = decrypt($id);
            $custom_page = CustomPage::find($page_id);

            return view('client.custom_pages.edit_custom_page', compact(['custom_page']));
        }
        catch (\Throwable $th)
        {
            return redirect()->back()->with('error', 'Internal Server Error!');
        }
    }

    function update(Request $request)
    {
        $page_id = decrypt($request->page_id);

        $request->validate([
            'name' => 'required|unique:custom_pages,name,'.$page_id,
        ]);

        try
        {
            $name = $request->name;
            $page_slug = str_replace(' ', '_', $name)."_page";
            $page_slug = strtolower($page_slug);
            $content = $request->content;

            $custom_page = CustomPage::find($page_id);
            $custom_page->name = $name;
            $custom_page->page_slug = $page_slug;
            $custom_page->content = $content;
            $custom_page->update();

            return redirect()->route('custom.pages')->with('success', 'Page has been Updated SuccessFully..');
        }
        catch (\Throwable $th)
        {
            return redirect()->back()->with('error', 'Internal Server Error!');
        }
    }

    function status(Request $request)
    {
        try
        {
            $id = $request->id;
            $status = $request->status;

            $custom_page = CustomPage::find($id);
            $custom_page->status = $status;
            $custom_page->update();

            return response()->json([
                'success' => 1,
                'message' => "Status has been Changed Successfully..",
            ]);

        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => "Internal Server Error!",
            ]);
        }
    }

    function destroy(Request $request)
    {
        try
        {
            CustomPage::where('id',$request->id)->delete();

            return response()->json([
                'success' => 1,
                'message' => 'Page has been Deleted SuccessFully..',
            ]);

        }
        catch (\Throwable $th)
        {
            return response()->json([
                'success' => 0,
                'message' => 'Internal Server Error!',
            ]);
        }
    }
}
