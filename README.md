<!--suppress HtmlDeprecatedAttribute -->

# HtmlMin: PHP package for HTML minification

Very simple (and very fast) html compression. See [benchmark and comparison](https://github.com/abordage/html-min-benchmark)

<p style="text-align: center;" align="center">
    <img alt="HtmlMin" src="https://github.com/abordage/html-min/blob/master/docs/images/abordage-html-min-cover.png?raw=true">
</p>

<p style="text-align: center;" align="center">


<a href="https://packagist.org/packages/abordage/html-min" title="Packagist version">
    <img alt="Packagist Version" src="https://img.shields.io/packagist/v/abordage/html-min">
</a>


<a href="https://github.com/abordage/html-min/actions/workflows/tests.yml" title="GitHub Tests Status">
    <img alt="GitHub Tests Status" src="https://img.shields.io/github/actions/workflow/status/abordage/html-min/tests.yml?label=tests">
</a>

<a href="https://github.com/abordage/html-min/actions/workflows/tests.yml" title="GitHub Code Style Status">
    <img alt="GitHub Code Style Status" src="https://img.shields.io/github/actions/workflow/status/abordage/html-min/php-cs-fixer.yml?label=code%20style">
</a>

<a href="https://www.php.net/" title="PHP version">
    <img alt="PHP Version Support" src="https://img.shields.io/packagist/php-v/abordage/html-min">
</a>

<a href="https://github.com/abordage/html-min/blob/master/README.md" title="License">
    <img alt="License" src="https://img.shields.io/github/license/abordage/html-min">
</a>

</p>

## Features:
- Removing extra whitespaces
- Removing html comments
- Skip `textarea`, `pre` and `script` elements
- Compresses in microseconds. See benchmark and comparison

## Requirements
- PHP 7.4 - 8.2

## Installation

You can install the package via composer:

```bash
composer require abordage/html-min
```

## Quick start

```php
<?php

require __DIR__ . '/vendor/autoload.php';

$htmlMin = new Abordage\HtmlMin\HtmlMin();
$result = $htmlMin->minify("<!DOCTYPE html><html>   ...  </html>");
```
## Options

```php
$htmlMin->findDoctypeInDocument(); // default: true
$htmlMin->removeWhitespaceBetweenTags(); // default: true
$htmlMin->removeBlankLinesInScriptElements(); // default: false
```

## Benchmark

See [abordage/html-min-benchmark](https://github.com/abordage/html-min-benchmark)

## Testing

```bash
composer test:all
```

or

```bash
composer test:phpunit
composer test:phpstan
composer test:phpcsf
```

or see https://github.com/abordage/html-min/actions/workflows/tests.yml

## Feedback

If you have any feedback, comments or suggestions, please feel free to open an issue within this repository.

## Contributing

Please see [CONTRIBUTING](https://github.com/abordage/.github/blob/master/CONTRIBUTING.md) for details.

## Credits

- [Pavel Bychko](https://github.com/abordage)
- [All Contributors](https://github.com/abordage/html-min/graphs/contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
