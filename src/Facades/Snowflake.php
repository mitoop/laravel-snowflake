<?php

namespace Mitoop\LaravelSnowflake\Facades;

use Illuminate\Support\Facades\Facade;

class Snowflake extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'snowflake';
    }
}
