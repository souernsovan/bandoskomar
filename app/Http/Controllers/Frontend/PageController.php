<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Mail\ContactMessageMail;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

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
                ->take(5)
                ->get();
            $data = array_merge($data, [
                'description' => $c['description'] ?? 'Our programs are designed to support communities through education, health, relief, and long-term empowerment.',
                'productsTitle' => $c['products_title'] ?? 'Our Programs',
                'partnersTitle' => $c['partners_title'] ?? 'Our Supporters',
                'partnerImages' => $partnerImages,
                'products' => $products,
            ]);
        } elseif ($slug === 'contact') {
            $data = array_merge($data, $this->buildContactPageData($page));
            $data['hidePageBanner'] = true;
        } elseif ($slug === 'image-gallery') {
            $data = array_merge($data, $this->buildPublicImageGallery());
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
            ->take(5)
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

    /**
     * Display the public image gallery page.
     */
    public function gallery()
    {
        return $this->show('image-gallery');
    }

    /**
     * Handle the frontend contact form submission.
     */
    public function sendContactMessage(Request $request)
    {
        $page = Page::getBySlug('contact');
        $contactData = $this->buildContactPageData($page);
        $contactContent = $contactData['contactContent'] ?? [];
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        $recipient = $this->resolveContactRecipient($contactContent);
        if ($recipient === null) {
            return redirect()
                ->route('frontend.contact')
                ->withInput()
                ->with('error', 'We could not send your message right now because no contact email is configured.');
        }

        try {
            Mail::to($recipient)->send(new ContactMessageMail(
                siteName: SiteSetting::get('site_name', config('app.name')),
                pageTitle: (string) ($contactContent['page_title'] ?? 'Contact Us'),
                name: $validated['name'],
                email: $validated['email'],
                message: $validated['message'],
            ));
        } catch (\Throwable $e) {
            report($e);

            return redirect()
                ->route('frontend.contact')
                ->withInput()
                ->with('error', 'We could not send your message right now. Please try again later.');
        }

        return redirect()
            ->route('frontend.contact')
            ->with('success', (string) ($contactContent['success_message'] ?? 'Thank you! Your message has been sent and we will respond shortly.'));
    }

    /**
     * Collect the public images from the website image directory.
     */
    private function buildPublicImageGallery(): array
    {
        $imageRoot = public_path('images');

        if (! File::exists($imageRoot)) {
            return [
                'galleryGroups' => [],
                'gallerySummary' => [
                    'total' => 0,
                    'folders' => 0,
                    'formats' => [],
                ],
            ];
        }

        $allowedExtensions = ['avif', 'gif', 'ico', 'jpeg', 'jpg', 'png', 'svg', 'webp'];

        $images = collect(File::allFiles($imageRoot))
            ->filter(fn ($file) => in_array(strtolower($file->getExtension()), $allowedExtensions, true))
            ->sortBy(fn ($file) => str_replace('\\', '/', $file->getRelativePathname()))
            ->values()
            ->map(function ($file) {
                $relativePath = str_replace('\\', '/', $file->getRelativePathname());
                $folder = trim(str_replace('\\', '/', $file->getRelativePath()), '/');
                $publicPath = 'images/' . $relativePath;

                return [
                    'label' => Str::headline(pathinfo($file->getFilename(), PATHINFO_FILENAME)),
                    'filename' => $file->getFilename(),
                    'folder' => $folder !== '' ? $folder : 'root',
                    'folder_label' => $folder !== '' ? Str::headline(str_replace('/', ' / ', $folder)) : 'Images',
                    'extension' => strtolower($file->getExtension()),
                    'path' => $publicPath,
                    'url' => asset($publicPath),
                ];
            });

        $galleryGroups = $images
            ->groupBy('folder')
            ->map(function ($items, $folder) {
                $first = $items->first();

                return [
                    'folder' => $folder,
                    'label' => $first['folder_label'] ?? Str::headline($folder),
                    'count' => $items->count(),
                    'images' => $items->values()->all(),
                ];
            })
            ->values()
            ->all();

        return [
            'galleryGroups' => $galleryGroups,
            'gallerySummary' => [
                'total' => $images->count(),
                'folders' => count($galleryGroups),
                'formats' => $images->pluck('extension')->unique()->sort()->values()->all(),
            ],
        ];
    }

    /**
     * Build the data needed to render the contact page.
     */
    private function buildContactPageData(?Page $page): array
    {
        $content = $page?->getPageContentForLocale() ?? [];

        $defaults = [
            'page_title' => 'Contact Us',
            'page_intro' => "We're here to help. Feel free to reach out through any of the channels below or send us a message directly.",
            'contact_info_title' => 'Contact Information',
            'confirm_open' => 'Open this contact method?',
            'address' => '123 Main Street, Phnom Penh, Cambodia',
            'phone' => '+855 23 123 456',
            'email' => 'info@bandoskomar.org',
            'office_hours' => 'Monday-Friday, 8:00-17:00 (ICT)',
            'form_title' => 'Send Us a Message',
            'form_subtitle' => 'Fill out the form below and we will get back to you as soon as possible.',
            'success_message' => 'Thank you! Your message has been sent and we will respond shortly.',
            'target_email' => 'info@bandoskomar.org',
            'labels' => [
                'full_name' => 'Full Name',
                'email_address' => 'Email Address',
                'message' => 'Message',
                'send_message' => 'Send Message',
            ],
            'placeholders' => [
                'full_name' => 'Your full name',
                'email_address' => 'you@example.com',
                'message' => 'How can we help you?',
            ],
            'messages' => [
                'no_methods' => 'No contact methods are available yet.',
            ],
            'contact_methods' => [],
        ];

        $contactContent = array_replace($defaults, $content);
        $contactContent['labels'] = array_replace($defaults['labels'], is_array($content['labels'] ?? null) ? $content['labels'] : []);
        $contactContent['placeholders'] = array_replace($defaults['placeholders'], is_array($content['placeholders'] ?? null) ? $content['placeholders'] : []);
        $contactContent['messages'] = array_replace($defaults['messages'], is_array($content['messages'] ?? null) ? $content['messages'] : []);

        $contactMethods = $this->normalizeContactMethods(
            is_array($contactContent['contact_methods'] ?? null) ? $contactContent['contact_methods'] : [],
            $contactContent
        );

        return [
            'contactContent' => $contactContent,
            'contactMethods' => $contactMethods,
            'contactLabels' => $contactContent['labels'],
            'contactPlaceholders' => $contactContent['placeholders'],
            'contactMessages' => $contactContent['messages'],
            'hidePageBanner' => true,
        ];
    }

    /**
     * Build a safe contact method list for display.
     *
     * @param  array<int, mixed>  $methods
     * @return array<int, array<string, mixed>>
     */
    private function normalizeContactMethods(array $methods, array $contactContent = []): array
    {
        $normalized = [];
        $byKey = [];

        foreach ($methods as $method) {
            if (! is_array($method)) {
                continue;
            }

            $key = strtolower(trim((string) ($method['key'] ?? '')));
            if ($key === '') {
                continue;
            }

            $byKey[$key] = $method;
        }

        $orderedKeys = ['email', 'whatsapp', 'telegram', 'signal', 'teams', 'wechat'];
        foreach ($orderedKeys as $key) {
            $method = $byKey[$key] ?? null;
            if (! is_array($method)) {
                continue;
            }

            $enabled = filter_var($method['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            $enabled = $enabled ?? false;
            $value = trim((string) ($method['value'] ?? ''));
            if (! $enabled || $value === '') {
                continue;
            }

            $url = $this->normalizeContactMethodUrl($key, $value, trim((string) ($method['url'] ?? '')));
            if ($url === '') {
                continue;
            }

            $label = trim((string) ($method['label'] ?? ''));
            $normalized[] = [
                'key' => $key,
                'label' => $label !== '' ? $label : Str::headline($key),
                'value' => $value,
                'url' => $url,
                'enabled' => true,
            ];
        }

        return $normalized;
    }

    private function normalizeContactMethodUrl(string $key, string $value, string $url): string
    {
        $key = strtolower(trim($key));
        $value = trim($value);
        $url = trim($url);

        if ($url !== '') {
            return $url;
        }

        if ($value === '') {
            return '';
        }

        return match ($key) {
            'email' => 'mailto:'.$value,
            'phone' => 'tel:'.preg_replace('/[^0-9+]/', '', $value),
            'whatsapp' => 'https://wa.me/'.preg_replace('/\D+/', '', $value),
            'telegram' => 'https://t.me/'.ltrim(preg_replace('#^https?://t\.me/#i', '', $value), '@'),
            'signal' => Str::startsWith($value, ['http://', 'https://']) ? $value : 'https://signal.me/#p/'.preg_replace('/\s+/', '', $value),
            'teams' => Str::startsWith($value, ['http://', 'https://']) ? $value : 'https://teams.microsoft.com/l/chat/0/0?users='.urlencode($value),
            'wechat' => Str::startsWith($value, ['http://', 'https://']) ? $value : 'weixin://dl/chat?'.urlencode($value),
            'website', 'link', 'custom' => Str::startsWith($value, ['http://', 'https://']) ? $value : '',
            default => Str::startsWith($value, ['http://', 'https://']) ? $value : '',
        };
    }

    private function resolveContactRecipient(array $contactContent): ?string
    {
        $candidates = [
            trim((string) ($contactContent['target_email'] ?? '')),
            trim((string) ($contactContent['email'] ?? '')),
            trim((string) SiteSetting::get('contact_email', '')),
            trim((string) config('mail.from.address', '')),
            'info@bandoskomar.org',
        ];

        foreach ($candidates as $candidate) {
            if ($candidate !== '' && filter_var($candidate, FILTER_VALIDATE_EMAIL)) {
                return $candidate;
            }
        }

        return null;
    }
}
