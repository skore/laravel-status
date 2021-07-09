<?php

namespace SkoreLabs\LaravelStatus\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use SkoreLabs\LaravelStatus\Events\StatusCreating;
use SkoreLabs\LaravelStatus\Status;
use Spatie\Enum\Enum;

trait HasStatuses
{
    /**
     * @var bool
     */
    protected $savingStatus;

    public static function bootHasStatuses()
    {
        if (config('status.enable_events', true)) {
            static::creating(function ($model) {
                event(new StatusCreating($model));
            });
        }
    }

    /**
     * Initialize the has statuses attribute trait for an instance.
     *
     * @return void
     */
    public function initializeHasStatuses()
    {
        $this->fillable = array_merge($this->fillable, ['status']);
        $this->guarded = array_merge($this->guarded, ['status_id']);

        if (config('status.enable_events', true)) {
            $this->addObservableEvents($this->getStatusObservables());

            static::saving(function () {
                if ($this->savingStatus) {
                    $this->savingStatus = false;
                    $this->fireModelEvent('saved'.$this->formatStatusName($this->getStatus()), false);
                }
            });
        }
    }

    /**
     * Get the statuses enum used for some utilities.
     *
     * @return string|\Spatie\Enum\Enum
     */
    public static function statusesClass()
    {
        return config('status.enums_path').class_basename(self::class).'Status';
    }

    /**
     * Get statuses custom observables events.
     *
     * @return array
     */
    protected function getStatusObservables()
    {
        $statusEventsArr = [];
        $statuses = static::getStatuses();

        foreach ($statuses as $status) {
            $status = $this->formatStatusName($status);

            $statusEventsArr[] = "saving${status}";
            $statusEventsArr[] = "saved${status}";
        }

        return $statusEventsArr;
    }

    /**
     * Get current status for this model.
     *
     * @param bool $value
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|string|bool
     */
    public function status($value = false)
    {
        if ($value) {
            return is_array($value) && Arr::isAssoc($value)
                ? $this->setStatus($value)
                : $this->hasStatus($value);
        }

        return $this->belongsTo(
            config('status.use_model', Status::class)
        );
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
        return static::statusesClass()::toValues();
    }

    /**
     * Check if current model status is the provided and return.
     *
     * @param string|array $name
     *
     * @return mixed|false
     */
    protected function checkCurrentStatus($name)
    {
        $name = (array) $name;

        $checkNamesArr = array_filter([
            array_key_first($name) ?? null,
            head($name) ?? null,
        ]);

        if (count($checkNamesArr) > 1 && $this->hasStatus($checkNamesArr) !== head($checkNamesArr)) {
            return false;
        }

        return last($checkNamesArr);
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
        return in_array($name, with(new static())->formatStatusName(
            array_flip(
                static::statusesClass()::toArray()
            )
        ));
    }

    /**
     * Set status by label(s) to key and perform a save.
     *
     * @param array|string|\Spatie\Enum\Enum $name
     *
     * @return bool
     */
    public function setStatus($name = null)
    {
        $name = $this->checkCurrentStatus(
            $name instanceof Enum ? $name->value : $name
        );

        $this->setStatusAttribute($name);

        if (is_null($name) || !$this->savingStatus) {
            return false;
        }

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
        $value = $this->formatStatusName($value);

        $this->savingStatus = $this->fireModelEvent("saving${value}") !== false;

        $this->status()->associate(
            $this->status()->getModel()::getFromEnum(static::statusesClass()::from($value), $this)
        );
    }

    /**
     * Get status relation as appended attribute.
     *
     * @param string|array|\Spatie\Enum\Enum $value
     *
     * @return bool
     */
    public function hasStatus($value)
    {
        $enumFromStatusInstance = static::statusesClass()::from($this->getStatus());

        return Collection::make((array) $value)->mapInto(static::statusesClass())
            ->every(function ($item) use ($enumFromStatusInstance) {
                return $enumFromStatusInstance->equals($item);
            });
    }

    /**
     * Get model status or default instead.
     *
     * @param string $column
     *
     * @return string|null
     */
    public function getStatus($column = 'name')
    {
        return $this->status()->value($column);
    }

    /**
     * Get default status for this model.
     *
     * @param string|array $column
     *
     * @return \Illuminate\Database\Eloquent\Model|object|\Illuminate\Database\Eloquent\Builder|null|mixed
     */
    public static function getDefaultStatus($column = 'name')
    {
        $modelInstance = new static();

        return $modelInstance->status()->getModel()
            ->query()
            ->defaultFrom($modelInstance)
            ->value($column);
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
     * Get status name capitalised.
     *
     * @param string|array|null $name
     *
     * @return string|string[]|false
     */
    protected function formatStatusName($name = null)
    {
        if (!$name) {
            return false;
        }

        $replaceStrFn = static function ($name) {
            return str_replace(' ', '', ucwords($name));
        };

        return is_array($name) ? array_map($replaceStrFn, $name) : $replaceStrFn($name);
    }
}
