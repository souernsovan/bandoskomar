<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Support\PageContent;

class HomeController extends Controller
{
    /**
     * Show the frontend home page.
     */
    public function index()
    {
        $page = Page::getHomePage();
        $c = PageContent::forSlug('home', $page);

        return view('frontend.index', compact('page', 'c'));
    }
}
