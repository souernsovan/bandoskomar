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

        $pageContent = $page?->getPageContentForLocale() ?? [];
        $partnerImages = $pageContent['partner_images'] ?? [];
        $partnerImages = is_array($partnerImages) ? $partnerImages : [$partnerImages];

        $heroHeadline = $pageContent['hero_headline'] ?? 'Building stronger communities together';
        $heroDescription = $pageContent['hero_description'] ?? 'We are a non-profit organization dedicated to education, health, and community support. Together with volunteers and donors, we turn compassion into action.';
        $heroImage = $pageContent['hero_image'] ?? '';

        $companyTitle = $pageContent['company_title'] ?? 'Our Mission';
        $companyDescription = $pageContent['company_description'] ?? 'We mobilize people, resources, and partnerships to create practical support for communities that need it most.';
        $companyLogo = $pageContent['company_logo'] ?? '';

        $valueProp1 = [
            'title' => $pageContent['value_prop_1_title'] ?? 'Education',
            'desc' => $pageContent['value_prop_1_desc'] ?? 'Scholarships, tutoring, school supplies, and learning spaces that help children and young people thrive.',
        ];
        $valueProp2 = [
            'title' => $pageContent['value_prop_2_title'] ?? 'Health & Care',
            'desc' => $pageContent['value_prop_2_desc'] ?? 'Community health outreach, wellness education, and compassionate care for families.',
        ];
        $valueProp3 = [
            'title' => $pageContent['value_prop_3_title'] ?? 'Emergency Relief',
            'desc' => $pageContent['value_prop_3_desc'] ?? 'Fast response support for families facing crisis, displacement, or urgent hardship.',
        ];
        $capabilitiesImage = $pageContent['capabilities_image'] ?? '';

        $marketingTitle = $pageContent['marketing_title'] ?? 'Get involved';
        $marketingDescription = $pageContent['marketing_description'] ?? 'Donate, volunteer, or partner with us to help expand our impact across more communities.';
        $marketingImage = $pageContent['marketing_image'] ?? '';

        $mobileTitle = $pageContent['mobile_title'] ?? 'Impact in action';
        $mobileImage = $pageContent['mobile_image'] ?? '';
        $mobileBg = $pageContent['mobile_bg'] ?? '';
        $impactFeature1 = [
            'title' => $pageContent['impact_feature_1_title'] ?? 'Community updates',
            'desc' => $pageContent['impact_feature_1_desc'] ?? 'Short, clear updates that show what is happening on the ground.',
        ];
        $impactFeature2 = [
            'title' => $pageContent['impact_feature_2_title'] ?? 'Transparent reporting',
            'desc' => $pageContent['impact_feature_2_desc'] ?? 'Simple reporting that helps supporters understand the results.',
        ];
        $impactFeature3 = [
            'title' => $pageContent['impact_feature_3_title'] ?? 'Direct response',
            'desc' => $pageContent['impact_feature_3_desc'] ?? 'Fast action when families need practical help the most.',
        ];
        $impactFeatureLabel = $pageContent['impact_feature_label'] ?? 'What we focus on';

        $trustSubtitle = $pageContent['trust_subtitle'] ?? 'Built on trust';
        $trustTitle = $pageContent['trust_title'] ?? 'Support that feels local, practical, and accountable.';
        $trustDescription = $pageContent['trust_description'] ?? 'NGO work is strongest when it stays close to the people it serves. We listen first, respond with simple action, and keep donors and partners informed along the way.';
        $trustStep1 = [
            'title' => $pageContent['trust_step_1_title'] ?? 'Listen to the community',
            'desc' => $pageContent['trust_step_1_desc'] ?? 'We work with local families, schools, and leaders to understand what matters most.',
        ];
        $trustStep2 = [
            'title' => $pageContent['trust_step_2_title'] ?? 'Act with purpose',
            'desc' => $pageContent['trust_step_2_desc'] ?? 'Every program is designed to be useful, visible, and easy to support.',
        ];
        $trustStep3 = [
            'title' => $pageContent['trust_step_3_title'] ?? 'Show the outcome',
            'desc' => $pageContent['trust_step_3_desc'] ?? 'We keep the story transparent so people can see the impact of their help.',
        ];
        $trustImage = $pageContent['trust_image'] ?? '';
        $trustQuoteLabel = $pageContent['trust_quote_label'] ?? 'Why it matters';
        $trustQuoteText = $pageContent['trust_quote_text'] ?? 'Small, clear actions build trust, and trust makes long-term community support possible.';

        $styleTitle = $pageContent['style_title'] ?? 'Featured programs';
        $colorChoiceTitle = $pageContent['color_choice_title'] ?? 'PROGRAM AREAS';
        $styles = is_array($pageContent['styles'] ?? null) ? $pageContent['styles'] : [];

        $partnersTitle = $pageContent['partners_title'] ?? 'Our supporters';

           return view('frontend.index', compact(
               'page', 'categories', 'products',
               'pageContent',
               'heroHeadline', 'heroDescription', 'heroImage',
               'companyTitle', 'companyDescription', 'companyLogo',
               'valueProp1', 'valueProp2', 'valueProp3', 'capabilitiesImage',
               'marketingTitle', 'marketingDescription', 'marketingImage',
               'mobileTitle', 'mobileImage', 'mobileBg',
               'impactFeature1', 'impactFeature2', 'impactFeature3', 'impactFeatureLabel',
               'trustSubtitle', 'trustTitle', 'trustDescription',
               'trustStep1', 'trustStep2', 'trustStep3', 'trustImage',
               'trustQuoteLabel', 'trustQuoteText',
               'styleTitle', 'colorChoiceTitle', 'styles',
               'partnersTitle', 'partnerImages'
           ));
    }
}
