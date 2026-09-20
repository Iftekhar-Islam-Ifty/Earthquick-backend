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
];
