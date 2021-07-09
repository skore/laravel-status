<?php

namespace SkoreLabs\LaravelStatus\Tests\Fixtures;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SkoreLabs\LaravelStatus\Contracts\Statusable;
use SkoreLabs\LaravelStatus\Traits\HasStatuses;

class Post extends Model implements Statusable
{
    use HasStatuses;

    /**
     * The attributes that should be visible in serialization.
     *
     * @var array
     */
    protected $visible = ['title', 'content'];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * Get the statuses enum used for some utilities.
     *
     * @return string|\SkoreLabs\LaravelStatus\Tests\Fixtures\PostStatuses
     */
    public static function statusesClass()
    {
        return PostStatuses::class;
    }

    protected static function newFactory()
    {
        return PostFactory::new();
    }

    /**
     * Return whether the post is published.
     *
     * @return bool
     */
    public function getIsPublishedAttribute()
    {
        return static::statusesClass()::from($this->status->name)
            ->equals(static::statusesClass()::published());
    }
}
