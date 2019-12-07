<?php

namespace SkoreLabs\LaravelStatus\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface Statusable
{
    /**
     * Get current status relationship.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status();

    /**
     * Get statuses available for this model.
     *
     * @return mixed
     */
    public function getStatusesAttribute();

    /**
     * Set status by label to key.
     *
     * @param mixed $name
     * @return void
     */
    public function setStatus($name = null);

    /**
     * Get model's default status.
     *
     * @return \App\Model\Status|null
     */
    public function getDefaultStatus($columns = ['*']);

    /**
     * List all resources of a specified status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfStatus(Builder $query, $name);
}
