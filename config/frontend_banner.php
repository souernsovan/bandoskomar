<?php

return [
    'defaults' => [
        'background_image' => 'https://images.unsplash.com/photo-1497486751825-1233686d5d80?q=80&w=1600&auto=format&fit=crop',
        'badge' => 'Impact since 1989',
        'title' => 'Empowering Communities',
        'subtitle' => 'for a Better Future',
        'description' => 'Bandos Komar is a local NGO dedicated to improving education in Cambodia...',
        'accent_color' => '#F68B1E',
        'navy_color' => '#1E2D53',
        'primary' => [
            'label' => 'Our Programs',
            'route' => 'frontend.product',
        ],
        'secondary' => [
            'label' => 'Learn More',
            'route' => 'frontend.about-us',
        ],
    ],

    'pages' => [
        'contact' => [
            'badge' => 'Get in touch',
            'title' => 'Contact Us',
            'subtitle' => 'We would love to hear from you',
            'description' => 'Whether you have questions about our programs, want to volunteer, or are interested in partnering with us, our team is here to help.',
            'primary' => [
                'label' => 'Support us',
                'route' => 'frontend.donate',
            ],
            'secondary' => [
                'label' => 'Learn more',
                'route' => 'frontend.about-us',
            ],
        ],

        'history' => [
            'badge' => 'Our story',
            'title' => 'History of Impact',
            'subtitle' => 'Building communities since 1989',
            'description' => 'For over three decades, we have worked hand-in-hand with communities to create lasting change through education, health, and relief programs.',
            'primary' => [
                'label' => 'Our Programs',
                'route' => 'frontend.product',
            ],
            'secondary' => [
                'label' => 'Contact us',
                'route' => 'frontend.contact',
            ],
        ],

        'donate' => [
            'badge' => 'Support our work',
            'title' => 'Make a Difference Today',
            'subtitle' => 'Your generosity creates lasting change',
            'description' => 'Every contribution, no matter the size, helps us deliver practical support to communities in need. Join us in building a better future for families across Southeast Asia.',
            'primary' => [
                'label' => 'Donate Now',
                'route' => 'frontend.product',
            ],
            'secondary' => [
                'label' => 'Our work',
                'route' => 'frontend.about-us',
            ],
        ],
    ],
];
