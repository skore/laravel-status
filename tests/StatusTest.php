<?php

namespace SkoreLabs\LaravelStatus\Tests;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;
use SkoreLabs\LaravelStatus\Events\StatusCreating;
use SkoreLabs\LaravelStatus\Listeners\AttachDefaultStatus;
use SkoreLabs\LaravelStatus\Status;
use SkoreLabs\LaravelStatus\Tests\Fixtures\AnotherStatuses;
use SkoreLabs\LaravelStatus\Tests\Fixtures\Post;
use SkoreLabs\LaravelStatus\Tests\Fixtures\PostStatuses;

class StatusTest extends TestCase
{
    use WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        Collection::make([
            Post::class => PostStatuses::toValues(),
        ])->each(function ($names, $modelType) {
            foreach ($names as $name) {
                Status::create([
                    'name'       => $name,
                    'model_type' => $modelType,
                    'is_default' => array_search($name, $names, true) === 0,
                ]);
            }
        });
    }

    public function test_status_assignment()
    {
        /** @var \SkoreLabs\LaravelStatus\Tests\Fixtures\Post $post */
        $post = Post::make()->forceFill([
            'title'   => $this->faker->words(3, true),
            'content' => $this->faker->paragraph(),
        ]);

        $post->setStatus('draft');

        $this->assertFalse($post->isDirty(), 'Model::status($value) should associate + save (persisting)');

        $this->assertTrue($post->status->name === 'draft', 'Model status should be the one previously assigned: "draft"');

        $this->assertTrue(
            $post->hasStatus('dRaFt') && $post->status(['dRafT']),
            'Model::hasStatus($name) & Model::status([$name, ...]) methods should work'
        );

        $this->assertFalse(
            $post->hasStatus(['published', 'archived']),
            'Model::hasStatus([$name, ...]) method shouldn\'t return true when array of statuses doesn\'t match the current "draft"'
        );

        $this->assertTrue(
            $post->hasStatus(PostStatuses::draft()),
            'Model::hasStatus($enum) method should also work with enums'
        );
    }

    public function test_model_status_default_assignation()
    {
        $this->markTestIncomplete('How can we make this working?');

        Event::fake();

        Event::assertListening(
            StatusCreating::class,
            AttachDefaultStatus::class
        );

        /** @var \SkoreLabs\LaravelStatus\Tests\Fixtures\Post $post */
        $post = Post::make()->forceFill([
            'title'   => $this->faker->words(3, true),
            'content' => $this->faker->paragraph(),
        ]);

        $this->assertEmpty(
            optional($post->status)->name,
            'When model isn\'t created yet, shouldn\'t have a status'
        );

        $post->save();

        Event::assertDispatched(StatusCreating::class, function (StatusCreating $event) use ($post) {
            return $event->model->is($post)
                && $event->model->getDefaultStatus() === optional($event->model->status)->name;
        });
    }

    public function test_status_model_methods()
    {
        /** @var \SkoreLabs\LaravelStatus\Tests\Fixtures\Post $post */
        $post = Post::make()->forceFill([
            'title'   => $this->faker->words(3, true),
            'content' => $this->faker->paragraph(),
        ]);

        $this->assertEquals(
            PostStatuses::draft(),
            Status::defaultFrom($post)->value('name'),
            'Status::defaultFrom($model) should get the default status for $model which is "draft"'
        );

        // TODO: Remove this once is fully deprecated
        $this->assertEquals(
            PostStatuses::draft(),
            Status::getDefault($post->getMorphClass(), 'name'),
            'Status::defaultFrom($model) should get the default status for $model which is "draft"'
        );

        $this->assertEquals(
            PostStatuses::draft(),
            Status::getFromEnum(AnotherStatuses::abc(), $post, 'name'),
            'Status::getFromEnum($enum, $model, \'name\') when enum & model instances doesn\'t match must return "draft"'
        );

        $post->setStatus(PostStatuses::published());

        $this->assertEquals(
            PostStatuses::published()->value,
            Status::getFromEnum(PostStatuses::published(), $post, 'name'),
            'Status::getFromEnum($enum, $model, \'name\') published from posts must return "published"'
        );

        $this->assertTrue(
            Status::where('name', (string) PostStatuses::published())->first(['*'])->is(
                Status::getFromEnum(PostStatuses::published(), $post, ['*'])
            ),
            'Status::getFromEnum($enum, $model, [\'*\']) must return a whole "Status" model result'
        );
    }
}
