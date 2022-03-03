<?php

namespace SkoreLabs\LaravelStatus;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @extends \Illuminate\Database\Eloquent\Builder<\SkoreLabs\LaravelStatus\Status>
 */
class StatusBuilder extends Builder
{
    /**
     * Get default status from model.
     *
     * @param string|\Illuminate\Database\Eloquent\Model $modelType
     *
     * @return $this
     */
    public function defaultFrom($modelType)
    {
        $this->where(function (self $query) use ($modelType) {
            $query->where(
                'model_type',
                $modelType instanceof Model
                    ? $modelType->getMorphClass()
                    : $modelType
            );
            
            $query->where('is_default', true);
        });

        return $this;
    }
}
