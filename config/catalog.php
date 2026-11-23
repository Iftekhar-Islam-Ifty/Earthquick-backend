<?php

return [
    'product_types' => [
        'general' => [
            'label' => 'General',
            'template' => ['Brand' => '', 'Model' => '', 'Color' => ''],
        ],
        'apparel' => [
            'label' => 'Apparel',
            'template' => ['Material' => '', 'Color' => '', 'Size' => '', 'Care' => ''],
        ],
        'accessories' => [
            'label' => 'Accessories',
            'template' => ['Material' => '', 'Color' => '', 'Dimensions' => '', 'Closure' => ''],
        ],
        'electronics' => [
            'label' => 'Electronics',
            'template' => ['Brand' => '', 'Model' => '', 'Power' => '', 'Voltage' => '', 'Connectivity' => ''],
        ],
        'home' => [
            'label' => 'Home & Living',
            'template' => ['Material' => '', 'Color' => '', 'Dimensions' => '', 'Care' => ''],
        ],
    ],

    'category_defaults' => [
        'men' => 'apparel',
        'women' => 'apparel',
        'kids' => 'apparel',
        'ornaments' => 'accessories',
        'bags' => 'accessories',
        'home-decor' => 'home',
        'electronics' => 'electronics',
    ],

    'category_groups' => [
        'fashion-accessories' => [
            'label' => 'Fashion & Accessories',
            'description' => 'Explore fashion, accessories and handcrafted collections from Rthquick stores.',
            'categories' => ['women', 'men', 'kids', 'ornaments', 'bags'],
        ],
    ],

    'delivery_classes' => [
        'standard' => 'Standard Courier',
        'fragile' => 'Fragile — Special Handling',
        'oversized' => 'Oversized / Heavy',
    ],

    'media_roles' => [
        'gallery' => 'Gallery',
        'lifestyle' => 'Lifestyle / In Use',
        'detail' => 'Detail / Close-up',
        'packaging' => 'Packaging',
        'size_chart' => 'Size Chart',
    ],
];
