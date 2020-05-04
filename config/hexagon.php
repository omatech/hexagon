<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    /**
     * Defines the location of files and to save and templates
     */
    'directories' => [
        'templates' => 'vendor/omatech/hexagon/templates/',
        'templates_default' => 'vendor/omatech/hexagon/resources/templates/',
        'application' => 'app/Application/',
        'domain' => 'app/Domain',
        'infrastructure' => 'app/Infrastructure/',
        'input-adapter' => '',
        'output-adapter' => ''
    ],

    /**
     * Menu configuration
     */
    'menu' => [
        'main' => [
            'title' => 'Welcome to Hexagon For Laravel, Please select an option'
        ]
    ],

    /**
     *  Defines the availability of commands depending on the depth of the structure to reach
     *  1 -> (default) Aplication: Generates Controllers, Use Case and Adapters
     *  2 -> Domain: Generates Domain Objects
     *  3 -> Infrastructure: Generates Actions and Repositories
     */
    'depth' => 1
];
