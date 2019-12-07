<?php

namespace SkoreLabs\LaravelStatus\Events;

use SkoreLabs\LaravelStatus\Contracts\Statusable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class StatusCreating
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var \SkoreLabs\LaravelStatus\Contracts\Statusable
     */
    public $model;

    /**
     * Create a new event instance.
     *
     * @param \App\Contracts\Statusable $statusable
     * @return void
     */
    public function __construct(Statusable $statusable)
    {
        $this->model = $statusable;
    }
}
