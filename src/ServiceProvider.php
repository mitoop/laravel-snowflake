<?php

namespace Mitoop\LaravelSnowflake;

use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;
use Illuminate\Support\Str;
use Mitoop\Snowflake\Snowflake;

class ServiceProvider extends LaravelServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Snowflake::class, function () {
            $snowflake = new Snowflake($this->config('epoch'));

            if (! is_null($this->config('datacenter_id'))) {
                $snowflake->setDatacenterId($this->config('datacenter_id'));
            }

            if (! is_null($this->config('worker_id'))) {
                $snowflake->setWorkerId($this->config('worker_id'));
            }

            if (is_callable($this->config('sequence_strategy'))) {
                $snowflake->setSequenceStrategy(call_user_func($this->config('sequence_strategy')));
            }

            return $snowflake;
        });

        $this->app->alias(Snowflake::class, 'snowflake');

        /**
         * @param $prefix
         * @return string
         */
        Str::macro('snowflakeId', fn ($prefix = '') => $prefix.app('snowflake')->id());

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

    protected function config($key, $default = null)
    {
        return config("laravel-snowflake.$key", $default);
    }
}
