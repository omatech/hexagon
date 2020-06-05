<?php

/*
 * You can place your custom package configuration in here.
 */
return [
    /**
     * Defines the location of files and to save and templates
     * type:string
     */
    'directories' => [
        'app' => 'app',
        'action' => 'Repositories',
        'application' => 'Application',
        'domain' => 'Domain',
        'infrastructure' => 'Infrastructure',
        'input-adapter' => '',
        'service-providers' => 'Providers',
        'templates' => 'vendor/omatech/hexagon/templates/',
        'templates_default' => 'vendor/omatech/hexagon/resources/templates/',
        'output-adapter' => '',
        'api-controller' => 'Api/Controllers',
        'http-controller' => 'Http/Controllers'
    ],

    /**
     * Defines names' prefixes and suffixes for files
     * type:string
     */
    'names' => [
        'domain-object' => ['suffix' => 'DO'],
        'action' => [],
        'action-repository' => ['suffix' => 'Repository'],
        'input-adapter' => ['suffix' => 'InputAdapter'],
        'output-adapter' => ['suffix' => 'OutputAdapter'],
        'api-controller' => ['suffix' => 'Controller'],
        'http-controller' => ['suffix' => 'Controller']
    ],

    /**
     * Defines if path for a file type has a domain folder
     * type: bool
     * default: true
     */
    'domain-paths' => [
        'api-controller' => false,
        'http-controller' => false
    ],

    /**
     * Defines if path for a file type has a use case folder
     * type: bool
     * default: false
     */
    'use-case-paths' => [
        'use-case' => true,
        'input-adapter' => true,
        'output-adapter' => true,
    ],

    /**
     * Menu configuration
     * type:string
     */
    'menu' => [
        'main' => [
            'title' => 'Welcome to Hexagon For Laravel, '
        ]
    ],

    /**
     *  Defines the availability of commands depending on the depth of the structure to reach
     *  1 -> (default) Aplication: Generates Controllers, Use Case and Adapters
     *  2 -> Domain: Generates Domain Objects
     *  3 -> Infrastructure: Generates Actions and Repositories
     */
    'depth' => 1,

    /**
     * Bounded context
     * If code is split in boundaries, specify them in here
     */
    'boundaries' => [],

    /**
     * Adds availability of dependencies for a template
     * example: if we want to use of a class in our template of type input-adapter, with an array like
     * ['type' => 'input-adapter'] we will be able to replace ${InputAdapterNamespace} and ${InputAdapterName}
     *
     * available keys:
     * 'type' => Type of the file we want to use. Defaults: same type as dependant class
     * 'layer' => Layer where the dependency resides. Defaults: same layer as dependant class
     * 'domain' => Domain where the dependency belongs. Defaults: same domain as dependant class
     */
    'dependencies' => [
        'api-controller' => [
            [
                'type' => 'use-case',
                'layer' => 'application'
            ],
            [
                'type' => 'input-adapter',
                'layer' => 'application'
            ],
            [
                'type' => 'output-adapter',
                'layer' => 'application'
            ]
        ],
        'http-controller' => [
            [
                'type' => 'use-case',
                'layer' => 'application'
            ],
            [
                'type' => 'input-adapter',
                'layer' => 'application'
            ],
            [
                'type' => 'output-adapter',
                'layer' => 'application'
            ]
        ],
        'use-case' => [['type' => 'input-adapter'], ['type' => 'output-adapter']],
    ],

];
