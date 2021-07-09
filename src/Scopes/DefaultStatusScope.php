<?php

namespace SkoreLabs\LaravelStatus\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use SkoreLabs\LaravelStatus\Contracts\Statusable;
use SkoreLabs\LaravelStatus\Status;

class DefaultStatusScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \SkoreLabs\LaravelStatus\Contracts\Statusable|Illuminate\Database\Eloquent\Model $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->where('status_id', $model->getDefaultStatus('id'));
    }
}
