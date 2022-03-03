<?php

namespace SkoreLabs\LaravelStatus\Contracts;

use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 * @template TModelClass of \Illuminate\Database\Eloquent\Model
 * 
 * @property-read \SkoreLabs\LaravelStatus\Status|string|bool $status
 * @property-read int $status_id
 */
interface Statusable
{
    /**
     * Get or set current status for this model.
     *
     * @param bool $value
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo<\SkoreLabs\LaravelStatus\Status, TModelClass>|string|bool
     */
    public function status($value = false);

    /**
     * Get statuses available for this model as attribute.
     *
     * @return array
     */
    public function getStatusesAttribute();

    /**
     * Get statuses available for this model.
     *
     * @return array
     */
    public static function getStatuses();

    /**
     * Set status by label(s) to key and perform a save.
     *
     * @param array|string|\Spatie\Enum\Enum $name
     *
     * @return bool
     */
    public function setStatus($name);

    /**
     * Set status when current status is.
     *
     * @param mixed $current
     * @param mixed $new
     *
     * @return bool
     */
    public function setStatusWhen($current, $new);

    /**
     * Set status relation as attribute.
     *
     * @param string|\Spatie\Enum\Enum $value
     *
     * @return void
     */
    public function setStatusAttribute($value = null);

    /**
     * Check current status is equals to.
     *
     * @param string|array|\Spatie\Enum\Enum $value
     *
     * @return bool
     */
    public function hasStatus($value);

    /**
     * Get model status or default instead.
     *
     * @param string $column
     *
     * @return string|null
     */
    public function getStatus($column = 'name');

    /**
     * Get default status for this model.
     *
     * @param string|array $column
     *
     * @return \Illuminate\Database\Eloquent\Model|object|\Illuminate\Database\Eloquent\Builder|null|mixed
     */
    public static function getDefaultStatus($column = 'name');

    /**
     * List all resources of a specified status.
     *
     * @param \Illuminate\Database\Eloquent\Builder<TModelClass> $query
     * @param string|\Spatie\Enum\Enum              $name
     *
     * @return \Illuminate\Database\Eloquent\Builder<TModelClass>
     */
    public function scopeStatus(Builder $query, $name);
}
