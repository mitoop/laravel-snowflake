<?php

namespace Mitoop\LaravelSnowflake;

use Closure;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Mitoop\Snowflake\Snowflake;

class ServiceProvider extends LaravelServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->singleton(Snowflake::class, static function () {

            $snowflake = new Snowflake($this->config('epoch'));

            if (! is_null($this->config('datacenter_id'))) {
                $snowflake->setDatacenterId($this->config('datacenter_id'));
            }

            if (! is_null($this->config('worker_id'))) {
                $snowflake->setWorkerId($this->config('worker_id'));
            }

            if (! is_null($this->config('sequence_strategy'))) {
                $snowflake->setSequenceStrategy(new ($this->config('sequence_strategy')));
            }
        });

        $this->app->alias(Snowflake::class, 'snowflake');

        Blueprint::mixin(new class
        {
            public function snowflake(): Closure
            {
                return fn (string $column = 'id') => $this->unsignedBigInteger($column);
            }
        });
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $path = realpath(__DIR__.'/../config/laravel-snowflake.php');

            $this->publishes([$path => config_path('laravel-snowflake.php')], 'config');

            $this->mergeConfigFrom($path, 'laravel-snowflake');
        }
    }

    public function provides(): array
    {
        return ['snowflake', Snowflake::class];
    }

    protected function config($key, $default = null)
    {
        return config("laravel-snowflake.$key", $default);
    }
}
