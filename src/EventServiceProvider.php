<?php

namespace SkoreLabs\LaravelStatus;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SkoreLabs\LaravelStatus\Events\StatusCreating;
use SkoreLabs\LaravelStatus\Events\StatusSaving;
use SkoreLabs\LaravelStatus\Listeners\AttachDefaultStatus;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        StatusCreating::class => [
            AttachDefaultStatus::class,
        ],
    ];
}
