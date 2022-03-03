<?php

namespace SkoreLabs\LaravelStatus;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Enum\Enum;

class Status extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name', 'model_type', 'is_default',
    ];

    /**
     * The attributes that should be visible in arrays.
     *
     * @var array
     */
    protected $visible = ['name'];

    /**
     * Get default status from model.
     *
     * @param class-string<\Illuminate\Database\Eloquent\Model>  $modelClass
     * @param string|array<string> $column
     *
     * @return mixed
     *
     * @deprecated Removing this method on next major release of "skore-labs/laravel-status"
     */
    public static function getDefault($modelClass, $column = 'id')
    {
        /** @var \Illuminate\Database\Eloquent\Builder<\SkoreLabs\LaravelStatus\Status> $baseQuery */
        $baseQuery = self::where('model_type', $modelClass);

        $query = $baseQuery->where('is_default', true);

        if (!$query->exists()) {
            $query = $baseQuery;
        }

        if (is_array($column)) {
            return $query->first($column);
        }

        return $query->value($column);
    }

    /**
     * Get column from status enum class.
     *
     * @param \Spatie\Enum\Enum                   $enum
     * @param \Illuminate\Database\Eloquent\Model $from
     * @param string|array                        $column
     *
     * @return \Illuminate\Database\Eloquent\Model|object|\Illuminate\Database\Eloquent\Builder|null|mixed
     */
    public static function getFromEnum(Enum $enum, Model $from, $column = 'id')
    {
        $fromModelMorphClass = $from->getMorphClass();

        $query = self::query()
            ->where('model_type', $fromModelMorphClass)
            ->where('name', 'like', "%{$enum->label}%");

        if ($query->count('id') === 0) {
            $query->orWhere(function (Builder $query) use ($fromModelMorphClass) {
                $query->defaultFrom($fromModelMorphClass);
            });
        }

        return is_array($column)
            ? $query->first($column)
            : $query->value($column);
    }

    /**
     * Wrap status value into status enum class.
     *
     * @param mixed|\Spatie\Enum\Enum $class
     * @param mixed                   $value
     *
     * @return \Spatie\Enum\Enum|false
     */
    public static function toEnum($class, $value)
    {
        if (!$value || !method_exists($class, 'tryFrom')) {
            return false;
        }

        if (!($value instanceof Enum)) {
            return $class::tryFrom($value);
        }

        return $value;
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param  \Illuminate\Database\Query\Builder  $query
     * @return \SkoreLabs\LaravelStatus\StatusBuilder
     */
    public function newEloquentBuilder($query)
    {
        return new StatusBuilder($query);
    }
}
