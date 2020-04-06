<?php

namespace SkoreLabs\LaravelStatus\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Statusable
{
    /**
     * Get, set or check status relationship.
     *
     * @param bool $value
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|bool
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
     * Set status by name or using a previous status.
     *
     * @param array|string $name
     *
     * @return bool
     */
    public function setStatus($name);

    /**
     * Set status relation as attribute.
     *
     * @param mixed $value
     *
     * @return void
     */
    public function setStatusAttribute($value = null);

    /**
     * Get if current model has status(es).
     *
     * @param string|array $value
     *
     * @return bool
     */
    public function hasStatus($value);

    /**
     * Get current model status or default instead.
     *
     * @param string $column
     *
     * @return mixed
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
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $value
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, $name);
}
