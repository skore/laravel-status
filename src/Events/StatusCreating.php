<?php

namespace SkoreLabs\LaravelStatus\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use SkoreLabs\LaravelStatus\Contracts\Statusable;

class StatusCreating
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @var \SkoreLabs\LaravelStatus\Contracts\Statusable<\Illuminate\Database\Eloquent\Model>
     */
    public $model;

    /**
     * Create a new event instance.
     *
     * @param \SkoreLabs\LaravelStatus\Contracts\Statusable<\Illuminate\Database\Eloquent\Model> $statusable
     *
     * @return void
     */
    public function __construct(Statusable $statusable)
    {
        $this->model = $statusable;
    }
}
