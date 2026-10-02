<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Product;
use App\Support\ContentSchema;
use App\Support\PageContent;

class PageController extends Controller
{
    /**
     * Display a page by slug.
     */
    public function show(string $slug)
    {
        if ($slug === ContentSchema::SITE) {
            abort(404);
        }

        $page = Page::getBySlug($slug);

        if (!$page && $slug === 'product') {
            $page = $this->makeFallbackProductPage();
        }

        if (!$page) {
            abort(404, 'Page not found');
        }

        $data = [
            'page' => $page,
            'c' => PageContent::forSlug($page->slug, $page),
        ];

        if ($page->slug === 'product') {
            $data['products'] = Product::where('status', 'active')->latest()->get();
        }

        $view = "frontend.pages.{$page->slug}";

        return view(view()->exists($view) ? $view : 'frontend.pages.default', $data);
    }

    /**
     * Keep /programs working even if the seeded page row is missing.
     */
    private function makeFallbackProductPage(): Page
    {
        $page = new Page();
        $page->slug = 'product';
        $page->title = 'Our Program';
        $page->route_name = 'frontend.product';

        return $page;
    }

    /**
     * Old category URLs now point to the programs page.
     */
    public function showProduct(string $slug)
    {
        return redirect()->route('frontend.product');
    }

    /**
     * Display a single program (project) detail page.
     */
    public function showProductDetail(Product $product)
    {
        if ($product->status !== 'active') {
            abort(404, 'Program not found');
        }

        $page = new class($product) {
            public $slug = 'product';
            public $meta_title;
            public $meta_description;
            public $canonical_url;
            public $og_tags = [];
            public $structured_data = null;

            public function __construct($product)
            {
                $this->meta_title = $product->title;
                $this->meta_description = $product->description ? \Illuminate\Support\Str::limit(strip_tags($product->description), 160) : null;
                $this->canonical_url = route('frontend.product.detail', $product->slug);
            }

            public function getTitleForLocale()
            {
                return $this->meta_title;
            }
        };

        return view('frontend.pages.product-detail', compact('product', 'page'));
    }

    /**
     * Display the public image gallery page.
     */
    public function gallery()
    {
        return $this->show('image-gallery');
    }
}
