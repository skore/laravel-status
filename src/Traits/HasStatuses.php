<?php

namespace SkoreLabs\LaravelStatus\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use SkoreLabs\LaravelStatus\Events\StatusCreating;
use SkoreLabs\LaravelStatus\Status;

trait HasStatuses
{
    /**
     * @var \Spatie\Enum\Enum
     */
    protected static $statuses;

    /**
     * Boot trait function.
     *
     * @return void
     */
    public static function bootHasStatuses()
    {
        if (!class_exists(static::$statuses)) {
            static::$statuses = config('status.enums_path').class_basename(self::class).'Status';
        }
    }

    /**
     * Initialize the has statuses attribute trait for an instance.
     *
     * @return void
     */
    public function initializeHasStatuses()
    {
        $this->dispatchesEvents['creating'] = StatusCreating::class;
        $this->fillable = array_merge($this->fillable, ['status']);
        $this->guarded = array_merge($this->guarded, ['status_id']);
    }

    /**
     * Get current status for this model.
     *
     * @param bool $value
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|bool
     */
    public function status($value = false)
    {
        if ($value) {
            return is_array($value) && Arr::isAssoc($value)
                ? $this->setStatus($value)
                : $this->hasStatus($value);
        }

        return $this->belongsTo($this->getStatusModel());
    }

    /**
     * Get statuses available for this model as attribute.
     *
     * @return array
     */
    public function getStatusesAttribute()
    {
        return static::getStatuses();
    }

    /**
     * Get statuses available for this model.
     *
     * @return array
     */
    public static function getStatuses()
    {
        return static::$statuses::getValues();
    }

    /**
     * Check if name is a possible status.
     *
     * @param mixed|null $name
     *
     * @return bool
     */
    protected static function checkStatus($name = null)
    {
        $statusesArr = array_map(static function ($item) {
            return strtolower($item);
        }, array_flip(static::$statuses::toArray()));

        return in_array(strtolower($name), $statusesArr);
    }

    /**
     * Set status by label to key and perform a save.
     *
     * @param array|string $name
     *
     * @return bool
     */
    public function setStatus($name = null)
    {
        if (is_array($name)) {
            if (!$this->hasStatus(array_key_first($name))) {
                return false;
            }

            $name = head($name);
        }

        $this->status()->associate(
            $this->getStatusModel()::getFromEnum(static::$statuses::make(ucwords($name)), 'id')
        );

        return $this->save();
    }

    /**
     * Set status relation as attribute.
     *
     * @param mixed $value
     *
     * @return void
     */
    public function setStatusAttribute($value = null)
    {
        if ($value && static::checkStatus($value)) {
            $this->status()->associate(
                $this->getStatusModel()::getFromEnum(static::$statuses::make(ucwords($value)))
            );
        }
    }

    /**
     * Get status relation as appended attribute.
     *
     * @param string|array $value
     *
     * @return bool
     */
    public function hasStatus($value)
    {
        $searchStatusArr = array_search(
            strtolower(static::$statuses::make($this->getStatus())->getValue()),
            array_map('strtolower', (array) $value)
        );

        return is_int($searchStatusArr)
            ? $searchStatusArr >= 0
            : $searchStatusArr;
    }

    /**
     * Get model status or default instead.
     *
     * @param string $column
     *
     * @return mixed
     */
    public function getStatus($column = 'name')
    {
        $this->loadMissing('status');

        return data_get($this, "status.$column")
            ?: static::getDefaultStatus($column);
    }

    /**
     * Get default status for this model.
     *
     * @param string|array $column
     *
     * @throws mixed
     *
     * @return \Illuminate\Database\Eloquent\Model|object|\Illuminate\Database\Eloquent\Builder|null|mixed
     */
    public static function getDefaultStatus($column = 'name')
    {
        $modelInstance = new static();

        return $modelInstance->getStatusModel()::getDefault($modelInstance->getMorphClass(), $column);
    }

    /**
     * List all resources of a specified status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed                                 $value
     *
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeStatus(Builder $query, $name)
    {
        return $query->whereHas('status', function (Builder $query) use ($name) {
            $query->where('name', 'like', $name);
        });
    }

    /**
     * Get status model class from config or default instead.
     *
     * @return \SkoreLabs\LaravelStatus\Status
     */
    public function getStatusModel()
    {
        return config('status.use_model', Status::class);
    }
}
