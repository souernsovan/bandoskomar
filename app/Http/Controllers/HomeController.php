<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    private function page(string $slug, string $view, string $fallbackTitle)
    {
        $page = Schema::hasTable('pages') ? Page::where('slug', $slug)->first() : null;
        $page ??= new Page([
            'slug' => $slug,
            'title' => $fallbackTitle,
            'content' => [],
        ]);

        return view($view, [
            'page' => $page,
            'title' => $page->title,
        ]);
    }

    public function index()
    {
        return $this->page('home', 'pages.home', 'Home - Bandos Komar');
    }

    public function about()
    {
        return $this->page('about-us', 'pages.about', 'About Us - Bandos Komar');
    }

    public function history()
    {
        return $this->page('history', 'pages.history', 'History - Bandos Komar');
    }

    public function programs()
    {
        return $this->page('our-program', 'pages.programs', 'Our Program - Bandos Komar');
    }

    public function annualReport()
    {
        return $this->page('annual-report', 'pages.annual-report', 'Annual Report - Bandos Komar');
    }

    public function publication()
    {
        return $this->page('publication', 'pages.publication', 'Publication - Bandos Komar');
    }

    public function photoGallery()
    {
        return $this->page('photo-gallery', 'pages.photo-gallery', 'Photo Gallery - Bandos Komar');
    }

    public function videoCenter()
    {
        return $this->page('video-center', 'pages.video-center', 'Video Center - Bandos Komar');
    }

    public function supportUs()
    {
        return $this->page('support-us', 'pages.support-us', 'Support Us - Bandos Komar');
    }

    public function sponsorChild()
    {
        return $this->page('sponsor-child', 'pages.sponsor-child', 'Sponsor a Child - Bandos Komar');
    }

    public function waysToGive()
    {
        return $this->page('ways-to-give', 'pages.ways-to-give', 'Ways to Give - Bandos Komar');
    }

    public function career()
    {
        return $this->page('career', 'pages.career', 'Career - Bandos Komar');
    }

    public function donate()
    {
        return $this->page('donate', 'pages.donate', 'Donate - Bandos Komar');
    }

    public function contact()
    {
        return $this->page('contact', 'pages.contact', 'Contact - Bandos Komar');
    }

    public function customPage(Page $page)
    {
        return view('pages.custom', [
            'page' => $page,
            'title' => $page->title,
        ]);
    }
}
