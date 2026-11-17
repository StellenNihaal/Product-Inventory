<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandingContent;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        $contents = LandingContent::orderBy('section')->get()->groupBy('section');
        return view('admin.landing.index', compact('contents'));
    }

    public function update(Request $request)
    {
        foreach ($request->except('_token', '_method') as $key => $value) {
            LandingContent::where('key', $key)->update(['value' => $value]);
        }

        return back()->with('success', 'Landing page updated successfully!');
    }
}
