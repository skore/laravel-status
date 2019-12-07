<?php

namespace SkoreLabs\LaravelStatus\Traits;

use SkoreLabs\LaravelStatus\Contracts\Statusable;
use SkoreLabs\LaravelStatus\Events\StatusCreating;
use SkoreLabs\LaravelStatus\Status;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait HasStatuses
{
    /**
     * @var \Spatie\Enum\Enum
     */
    protected $statuses;

    /**
     * Initialize the has statuses attribute trait for an instance.
     *
     * @return void
     */
    public function initializeHasStatuses()
    {
        if (!class_exists($this->statuses)) {
            $this->statuses = config('status.enum_path') . class_basename(self::class) . 'Status';
        }

        $this->dispatchesEvents['creating'] = StatusCreating::class;
        $this->fillable = array_merge($this->fillable, ['status']);
        $this->guarded = array_merge($this->guarded, ['status_id']);
    }

    /**
     * Get current status.
     *
     * @param bool $getEnum
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|bool
     */
    public function status($value = false)
    {
        if ($value) {
            return $this->hasStatus($value);
        }

        return $this->belongsTo(Status::class);
    }

    /**
     * Get statuses available for this model.
     *
     * @return array
     */
    public function getStatusesAttribute()
    {
        return $this->statuses::getValues();
    }

    /**
     * Set status by label to key and perform a save.
     *
     * @param mixed $name
     * @return \SkoreLabs\LaravelStatus\Models\Status|false
     */
    public function setStatus($name = null)
    {
        $defaultStatus = $this->getDefaultStatus(['id']);

        $status = $this->status()->associate(
            Status::getFromEnum($this->statuses::make($name))
        );

        return $this->save() ? $status : false;
    }

    /**
     * Set status relation as attribute.
     *
     * @param mixed $value
     * @return void
     */
    public function setStatusAttribute($value = null)
    {
        if ($value && ($value = ucwords($value)) && key_exists($value, $this->statuses::toArray())) {
            $this->status()->associate(
                Status::getFromEnum($this->statuses::make($value))
            );
        }
    }

    /**
     * Get status relation as appended attribute.
     *
     * @param string|array $value
     * @param bool $strict
     * @return bool
     * @throws mixed
     * @throws \Spatie\Enum\Exceptions\DuplicatedValueException
     * @throws \Spatie\Enum\Exceptions\DuplicatedIndexException
     * @throws \Spatie\Enum\Exceptions\InvalidIndexException
     * @throws \Spatie\Enum\Exceptions\InvalidValueException
     */
    public function hasStatus($value, $strict = false)
    {
        $statusValue = $this->statuses::make(
            data_get($this->relations, 'status.name', $this->getStatus())
        )->getValue();
        $values = (array) $value;

        if (!$strict) {
            $values = array_map('strtolower', $values);
            $statusValue = strtolower($statusValue);
        }

        $searchStatusArr = array_search($statusValue, $values);

        return is_int($searchStatusArr)
            ? $searchStatusArr >= 0
            : $searchStatusArr;
    }

    /**
     * Get actual model status or default instead.
     *
     * @return mixed
     */
    protected function getStatus()
    {
        return $this->status()->value('name')
            ?: $this->getDefaultStatus('name');
    }

    /**
     * Get default status for this model.
     *
     * @param string|array $column
     * @return \Illuminate\Database\Eloquent\Model|object|\Illuminate\Database\Eloquent\Builder|null|mixed
     * @throws mixed
     */
    public function getDefaultStatus($column = 'id')
    {
        return Status::getDefault(self::class, $column);
    }

    /**
     * List all resources of a specified status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param mixed $value
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfStatus(Builder $query, $name)
    {
        return $query->whereHas('status', function (Builder $query) use ($name) {
            $query->where('name', $name);
        });
    }
}
