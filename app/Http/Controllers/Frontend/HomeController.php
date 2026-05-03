<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Show the frontend home page.
     */
    public function index()
    {
        $page = Page::getHomePage();
        $categories = Category::where('status', 'active')->orderBy('name')->get();
        $products = Product::where('status', 'active')->latest()->get();

        $c = $page?->getPageContentForLocale() ?? [];
        $partnerImages = $c['partner_images'] ?? [];
        $partnerImages = is_array($partnerImages) ? $partnerImages : [$partnerImages];

        $heroHeadline = $c['hero_headline'] ?? 'Building stronger communities together';
        $heroDescription = $c['hero_description'] ?? 'We are a non-profit organization dedicated to education, health, and community support. Together with volunteers and donors, we turn compassion into action.';
        $heroImage = $c['hero_image'] ?? '';

        $companyTitle = $c['company_title'] ?? 'Our Mission';
        $companyDescription = $c['company_description'] ?? 'We mobilize people, resources, and partnerships to create practical support for communities that need it most.';
        $companyLogo = $c['company_logo'] ?? '';

        $valueProp1 = [
            'title' => $c['value_prop_1_title'] ?? 'Education',
            'desc' => $c['value_prop_1_desc'] ?? 'Scholarships, tutoring, school supplies, and learning spaces that help children and young people thrive.',
        ];
        $valueProp2 = [
            'title' => $c['value_prop_2_title'] ?? 'Health & Care',
            'desc' => $c['value_prop_2_desc'] ?? 'Community health outreach, wellness education, and compassionate care for families.',
        ];
        $valueProp3 = [
            'title' => $c['value_prop_3_title'] ?? 'Emergency Relief',
            'desc' => $c['value_prop_3_desc'] ?? 'Fast response support for families facing crisis, displacement, or urgent hardship.',
        ];
        $capabilitiesImage = $c['capabilities_image'] ?? '';

        $marketingTitle = $c['marketing_title'] ?? 'Get involved';
        $marketingDescription = $c['marketing_description'] ?? 'Donate, volunteer, or partner with us to help expand our impact across more communities.';
        $marketingImage = $c['marketing_image'] ?? '';

        $mobileTitle = $c['mobile_title'] ?? 'Impact in action';
        $mobileImage = $c['mobile_image'] ?? '';
        $mobileBg = $c['mobile_bg'] ?? '';

        $styleTitle = $c['style_title'] ?? 'Featured programs';
        $colorChoiceTitle = $c['color_choice_title'] ?? 'PROGRAM AREAS';
        $styles = is_array($c['styles'] ?? null) ? $c['styles'] : [];

        $partnersTitle = $c['partners_title'] ?? 'Our supporters';

        return view('frontend.index', compact(
            'page', 'categories', 'products',
            'heroHeadline', 'heroDescription', 'heroImage',
            'companyTitle', 'companyDescription', 'companyLogo',
            'valueProp1', 'valueProp2', 'valueProp3', 'capabilitiesImage',
            'marketingTitle', 'marketingDescription', 'marketingImage',
            'mobileTitle', 'mobileImage', 'mobileBg',
            'styleTitle', 'colorChoiceTitle', 'styles',
            'partnersTitle', 'partnerImages'
        ));
    }
}
