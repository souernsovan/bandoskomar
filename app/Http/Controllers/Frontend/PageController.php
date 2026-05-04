<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;

class PageController extends Controller
{
    /**
     * Display a page by slug.
     */
    public function show(string $slug)
    {
        $page = Page::getBySlug($slug);

        if (!$page) {
            abort(404, 'Page not found');
        }

        $view = "frontend.pages.{$slug}";

        $data = compact('page');

        if ($slug === 'product') {
            $c = $page->getPageContentForLocale();
            $partnerImages = $c['partner_images'] ?? [];
            $partnerImages = is_array($partnerImages) ? $partnerImages : [$partnerImages];
            $products = Product::where('status', 'active')
                ->orderBy('created_at', 'desc')
                ->get();
            $data = array_merge($data, [
                'description' => $c['description'] ?? 'Our programs are designed to support communities through education, health, relief, and long-term empowerment.',
                'productsTitle' => $c['products_title'] ?? 'Our Programs',
                'partnersTitle' => $c['partners_title'] ?? 'Our Supporters',
                'partnerImages' => $partnerImages,
                'products' => $products,
            ]);
        }

        return view(
            view()->exists($view) ? $view : 'frontend.pages.default',
            $data
        );
    }

    /**
     * Display a program category page grouped by impact area.
     */
    public function showProduct(string $slug)
    {
        $category = Category::where('slug', $slug)->where('status', 'active')->first();

        if (!$category) {
            abort(404, 'Program category not found');
        }

        $productPage = Page::getBySlug('product');
        $c = $productPage?->getPageContentForLocale() ?? [];
        $partnerImages = $c['partner_images'] ?? [];
        $partnerImages = is_array($partnerImages) ? $partnerImages : [$partnerImages];

        $products = Product::where('status', 'active')
            ->where('category_id', $category->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $page = new class($category, $productPage) {
            public $slug;
            public $meta_title;
            public $meta_description;
            public $og_tags;
            public $canonical_url;
            public $structured_data;
            public $show_banner = true;

            public function __construct($category, $productPage) {
                $this->slug = $category->slug;
                $this->meta_title = $category->name;
                $this->meta_description = $productPage?->meta_description;
                $this->og_tags = $productPage?->og_tags;
                $this->canonical_url = route('frontend.product.show', ['slug' => $category->slug]);
                $this->structured_data = null;
            }
            public function getTitleForLocale() { return $this->meta_title; }
        };

        return view('frontend.pages.product', [
            'page' => $page,
            'description' => $category->description ?: ($c['description'] ?? 'Community programs grouped by area of focus.'),
            'productsTitle' => $c['products_title'] ?? 'Our Programs',
            'partnersTitle' => $c['partners_title'] ?? 'Our Supporters',
            'partnerImages' => $partnerImages,
            'products' => $products,
            'currentProductCategory' => $category->slug,
        ]);
    }

    /**
     * Display a single program detail page.
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
            public $og_tags;
            public $structured_data;
            public $show_banner = false;

            public function __construct($product) {
                $this->meta_title = $product->title;
                $this->meta_description = $product->description ? \Illuminate\Support\Str::limit(strip_tags($product->description), 160) : null;
                $this->canonical_url = route('frontend.product.detail', $product->slug);
                $this->og_tags = [];
                $this->structured_data = null;
            }

            public function getTitleForLocale() { return $this->meta_title; }
        };

        $currentProductCategory = $product->category?->slug;

        return view('frontend.pages.product-detail', compact('product', 'page', 'currentProductCategory'));
    }
}
