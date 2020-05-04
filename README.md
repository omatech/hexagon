# Very short description of the package

[![Latest Version on Packagist](https://img.shields.io/packagist/v/omatech/hexagon.svg?style=flat-square)](https://packagist.org/packages/omatech/hexagon)
[![Build Status](https://img.shields.io/travis/omatech/hexagon/master.svg?style=flat-square)](https://travis-ci.org/omatech/hexagon)
[![Quality Score](https://img.shields.io/scrutinizer/g/omatech/hexagon.svg?style=flat-square)](https://scrutinizer-ci.com/g/omatech/hexagon)
[![Total Downloads](https://img.shields.io/packagist/dt/omatech/hexagon.svg?style=flat-square)](https://packagist.org/packages/omatech/hexagon)

File structure generator for hexagonal architecture adapted to Laravel

## Installation

You can install the package via composer:

```bash
composer require omatech/hexagon
```

## Usage

``` php
Configuration
php artisan vendor:publish --tag=hexagon-config

Set-up
- create Application, Domain and Infrastructure folders into app folder
- Move Http and Console folders into app\Infrastructure and modify Kernels namespace
- Modify RouteServiceProvider $namespace attribute to 'App\Infrastructure\Http\Controllers
- Modify bootstrap/app.php: Adjust Http and Console Kernels namespaces
- Move Exception folder into app\Domain and modify Handler namespace
- Modify bootstrap/app.php: Adjust Handler namespaces
- Move app/User.php Model to app\Infrastructure\User folder and modify namespace
- Modify config/auth.php adapting User class to the new namespace
- Create RepositoryServiceProvider.php into app/Providers

```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email epuig@omatech.com instead of using the issue tracker.

## Credits

- [Edgar Puig](https://github.com/omatech)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
