<?php

namespace SkoreLabs\LaravelStatus\Listeners;

use SkoreLabs\LaravelStatus\Events\StatusCreating;

class AttachDefaultStatus
{
    /**
     * Handle the event.
     *
     * @param \SkoreLabs\LaravelStatus\Events\StatusCreating $event
     *
     * @return void
     */
    public function handle(StatusCreating $event)
    {
        if (!$event->model->status_id || !$event->model->status) {
            $event->model->status()->associate(
                $event->model->getDefaultStatus('id')
            );
        }
    }
}
