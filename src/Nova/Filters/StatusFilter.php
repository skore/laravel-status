<?php

namespace SkoreLabs\LaravelStatus\Nova\Filters;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\BooleanFilter;
use SkoreLabs\LaravelStatus\Scopes\DefaultStatusScope;
use SkoreLabs\LaravelStatus\Status;

class StatusFilter extends BooleanFilter
{
    /**
     * The type of resource that should be filtered on.
     *
     * @var \Illuminate\Database\Eloquent\Builder<\SkoreLabs\LaravelStatus\Status>
     */
    protected $statusesQuery;

    /**
     * @var array|bool
     */
    protected $defaultOption = false;

    /**
     * Create a new filter instance.
     *
     * @param string $type
     *
     * @return void
     */
    public function __construct($type)
    {
        $this->statusesQuery = Status::where([
            'model_type' => $type,
        ]);
    }

    /**
     * Apply the filter to the given query.
     *
     * @param \Illuminate\Http\Request                                                   $request
     * @param \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model> $query
     * @param mixed                                                                      $value
     *
     * @return \Illuminate\Database\Eloquent\Builder<\Illuminate\Database\Eloquent\Model>
     */
    public function apply(Request $request, $query, $value)
    {
        if (!array_filter($value)) {
            return $query;
        }

        $i = 0;

        foreach ($value as $status => $enabled) {
            if ($enabled) {
                $query->where($query->getModel()->getTable().'.status_id', '=', $status, $i === 0 ? 'and' : 'or');
                $i++;
            }
        }

        return $query->withoutGlobalScope(DefaultStatusScope::class);
    }

    /**
     * Get the filter's available options.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function options(Request $request)
    {
        return (clone $this->statusesQuery)
            ->pluck('id', 'name')
            ->all();
    }

    /**
     * Set the default options for the filter.
     *
     * @return array|bool
     */
    public function default()
    {
        if (!$this->defaultOption) {
            return parent::default();
        }

        return $this->defaultOption;
    }

    /**
     * Set option as default initial filter.
     *
     * @param string $value
     *
     * @return $this
     */
    public function setDefault($value)
    {
        $query = (clone $this->statusesQuery)->where('name', $value);

        if ($query->exists()) {
            $this->defaultOption = [$query->value('id') => true];
        }

        return $this;
    }
}
