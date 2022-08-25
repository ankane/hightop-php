# Hightop PHP

A nice shortcut for group count queries with Eloquent / Laravel

```php
Visit::top('browser');
// [
//   'Chrome' => 63,
//   'Safari' => 50,
//   'Firefox' => 34
// ]
```

[![Build Status](https://github.com/ankane/hightop-php/workflows/build/badge.svg?branch=master)](https://github.com/ankane/hightop-php/actions)

## Installation

Run:

```sh
composer require ankane/hightop
```

## Options

Limit the results

```php
Visit::top('referring_domain', 10);
```

Include null values

```php
Visit::top('search_keyword', null: true);
```

Works with expressions

```php
use Illuminate\Database\Query\Expression;

Visit::top(new Expression('lower(referring_domain)'));
```

And distinct

```php
Visit::top('city', distinct: 'user_id');
```

And min count

```php
Visit::top('city', min: 10);
```

And `where` clauses

```php
Visit::where('browser', 'Firefox')->top('os');
```

## History

View the [changelog](CHANGELOG.md)

## Contributing

Everyone is encouraged to help improve this project. Here are a few ways you can help:

- [Report bugs](https://github.com/ankane/hightop-php/issues)
- Fix bugs and [submit pull requests](https://github.com/ankane/hightop-php/pulls)
- Write, clarify, or fix documentation
- Suggest or add new features

To get started with development:

```sh
git clone https://github.com/ankane/hightop-php.git
cd hightop-php
composer install
composer test
```
