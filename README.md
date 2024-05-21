# This is my package plugin-skeleton

[![Latest Version on Packagist](https://img.shields.io/packagist/v/filamentphp/plugin-plugin-skeleton.svg?style=flat-square)](https://packagist.org/packages/filamentphp/plugin-plugin-skeleton)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/filamentphp/plugin-plugin-skeleton/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/filamentphp/plugin-plugin-skeleton/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/filamentphp/plugin-plugin-skeleton/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/filamentphp/plugin-plugin-skeleton/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/filamentphp/plugin-plugin-skeleton.svg?style=flat-square)](https://packagist.org/packages/filamentphp/plugin-plugin-skeleton)



This is where your description should go. Limit it to a paragraph or two. Consider adding a small example.

## Installation

You can install the package via composer:

```bash
composer require filamentphp/plugin-plugin-skeleton
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --tag="plugin-plugin-skeleton-migrations"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="plugin-plugin-skeleton-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="plugin-plugin-skeleton-views"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
$pluginSkeleton = new Filamentphp\PluginSkeleton();
echo $pluginSkeleton->echoPhrase('Hello, Filamentphp!');
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [tsetisStelios](https://github.com/filamentphp)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
