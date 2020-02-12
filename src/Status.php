<?php

namespace SkoreLabs\LaravelStatus;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
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
     * @var array
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
     * @param mixed  $modelClass
     * @param string $column
     *
     * @throws mixed
     *
     * @return \Illuminate\Database\Eloquent\Model|object|\Illuminate\Database\Eloquent\Builder|null|mixed
     */
    public static function getDefault($modelClass, $column = 'id')
    {
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
     * @param \Spatie\Enum\Enum $enum
     * @param string            $column
     *
     * @throws mixed
     *
     * @return \Illuminate\Database\Eloquent\Model|object|\Illuminate\Database\Eloquent\Builder|null|mixed
     */
    public static function getFromEnum(Enum $enum, $column = 'id')
    {
        $extractedModelClass = config('status.models_path').Str::before(class_basename($enum), 'Status');

        $query = self::where([
            ['name', $enum->getValue()],
            ['model_type', (new $extractedModelClass())->getMorphClass()],
        ]);

        if (is_array($column)) {
            return $query->first($column) ?: self::getDefault($extractedModelClass, $column);
        }

        return $query->value($column) ?: self::getDefault($extractedModelClass, $column);
    }
}
