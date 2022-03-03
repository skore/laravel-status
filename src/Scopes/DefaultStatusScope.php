<?php

namespace SkoreLabs\LaravelStatus\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * @template TModelClass of \Illuminate\Database\Eloquent\Model
 */
class DefaultStatusScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * @param \Illuminate\Database\Eloquent\Builder<TModelClass> $builder
     * @param \Illuminate\Database\Eloquent\Model                $model
     *
     * @return void
     */
    public function apply(Builder $builder, Model $model)
    {
        /** @var \SkoreLabs\LaravelStatus\Contracts\Statusable<TModelClass> $model */
        $builder->where('status_id', $model->getDefaultStatus('id'));
    }
}
