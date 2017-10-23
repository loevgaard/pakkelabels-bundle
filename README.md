# Pakkelabels Bundle

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]
[![Total Downloads][ico-downloads]][link-downloads]

Symfony bundle for integrating Pakkelabels into your Symfony application

## Install

Via Composer

```bash
$ composer require loevgaard/pakkelabels-bundle
```

## Usage

Then, enable the bundle by adding it to the list of registered bundles
in the `app/AppKernel.php` file of your project:

```php
<?php
// app/AppKernel.php

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...

            new Loevgaard\PakkelabelsBundle\LoevgaardPakkelabelsBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
        );

        // ...
    }

    // ...
}
```

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

```bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email `joachim@loevgaard.dk` instead of using the issue tracker.

## Credits

- [Joachim Løvgaard][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/loevgaard/pakkelabels-bundle.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/loevgaard/pakkelabels-bundle/master.svg?style=flat-square
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/loevgaard/pakkelabels-bundle.svg?style=flat-square
[ico-code-quality]: https://img.shields.io/scrutinizer/g/loevgaard/pakkelabels-bundle.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/loevgaard/pakkelabels-bundle.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/loevgaard/pakkelabels-bundle
[link-travis]: https://travis-ci.org/loevgaard/pakkelabels-bundle
[link-scrutinizer]: https://scrutinizer-ci.com/g/loevgaard/pakkelabels-bundle/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/loevgaard/pakkelabels-bundle
[link-downloads]: https://packagist.org/packages/loevgaard/pakkelabels-bundle
[link-author]: https://github.com/loevgaard
[link-contributors]: ../../contributors
