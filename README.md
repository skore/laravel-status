# Laravel status

Laravel code-typed statuses for Eloquent models.

## Status

[![packagist version](https://img.shields.io/packagist/v/skore-labs/laravel-status)](https://packagist.org/packages/skore-labs/laravel-status) [![StyleCI](https://github.styleci.io/repos/226506454/shield?style=flat&branch=master)](https://github.styleci.io/repos/226506454) [![Codacy Badge](https://api.codacy.com/project/badge/Grade/b9eb60ed572b4baab58ac3c4c9c06e7f)](https://www.codacy.com/manual/d8vjork/laravel-status?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=skore/laravel-status&amp;utm_campaign=Badge_Grade) [![Scc Count Badge](https://sloc.xyz/github/skore/laravel-status?category=code)](https://github.com/skore/laravel-status) [![Scc Count Badge](https://sloc.xyz/github/skore/laravel-status?category=comments)](https://github.com/skore/laravel-status)

## Getting started

You can install the package via composer:

```
composer require skore-labs/laravel-status
```

Then you will need to publish the package config and migrations, so then you can modify and/or migrate the new statuses table:

```
php artisan vendor:publish --provider="SkoreLabs\LaravelStatus\ServiceProvider"
```

### Setup models

Add statuses to your model by adding `SkoreLabs\LaravelStatus\Traits\HasStatuses` and the interface `SkoreLabs\LaravelStatus\Contracts\Statusable` so that it can pass some predefined events (see above), here's an example:

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use SkoreLabs\LaravelStatus\Contracts\Statusable;
use SkoreLabs\LaravelStatus\Traits\HasStatuses;

class Post extends Model implements Statusable
{
    use HasStatuses;

    // Your model logic here...
}
```

Customize enum for status check (using _spatie/enum_ package, [check their documentation](https://docs.spatie.be/enum/v2/introduction/)):

```php
    /**
     * Get the statuses enum used for some utilities.
     * 
     * @return string|\Spatie\Enum\Enum
     */
    public static function statusesClass()
    {
        return \App\Statuses\PostStatuses::class;
    }
```

**Note: This is not required, only if you DON'T have all your model status enum classes stored in `App\Enums` as `ModelStatus`.**

## Usage

- [hasStatus](#hasStatus)
- [setStatus](#setStatus)
- [status](#status)
- [statuses](#statuses)
- [getDefaultStatus](#getDefaultStatus)

**Note: All methods doesn't have case sensitive on status names.**

### hasStatus

Check if model has status(es).

**Note: It returns the current matched status name.**

```php
// Post has status Published
$post->hasStatus('published');

// Post has status Published or Was Published
$post->hasStatus(['published', 'was published']);
```

### setStatus

Set status or mutate status **only if the previous status match the key.**

```php
// Set post status to Was Published
$post->setStatus('was published');

// Change if post has status Published to Was Published.
$post->setStatus(['published' => 'was published'])
```

You can also use the attribute to set a status:

```php
$post->status = 'was published';

// Better use status method for this
if ($post->hasStatus('published')) {
    $post->status = 'was published';
}

// When save it check and attach the status
$post->save();
```

### status

If a parameter is provided, it acts as an alias of [hasStatus](#hasStatus).

If an associative array is provided, it acts as an alias of [setStatus](#setStatus).

Otherwise, it will just retrieve the relationship as `$post->status` or `$post->status()->first()`

Also you can filter by scope:

```php
Post::status('published');
Post::where('user_id', Auth::id())->status('published');
```

### statuses

Get all the possible model statuses.

```php
Post::statuses();

// You can use Status model as well
Status::getFrom(Post::class);
// Also specify value to return like '->value('id')'
Status::getFrom(Post::class, 'id');
// Or return the object with columns like '->first(['id', 'name'])'
Status::getFrom(Post::class, ['id', 'name']);
```

### getDefaultStatus

Get the model's default status.

```php
// Default status for post is Published, so it returns Published
Post::getDefaultStatus();

// You can use Status model query scope as well
Status::query()->defaultFrom(Post::class)->first();
```

## Support

This and all of our Laravel packages follows as much as possibly can the LTS support of Laravel.

Read more: https://laravel.com/docs/master/releases#support-policy

## Credits

- Ruben Robles ([@d8vjork](https://github.com/d8vjork))
- Skore ([https://www.getskore.com/](https://www.getskore.com/))
- Spatie for the Enum package ([https://spatie.be/](https://spatie.be/))
- [And all the contributors](https://github.com/skore-labs/laravel-status/graphs/contributors)
