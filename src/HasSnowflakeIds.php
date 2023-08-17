<?php

namespace Mitoop\LaravelSnowflake;

use Mitoop\Snowflake\Snowflake;

trait HasSnowflakeIds
{
    public function initializeHasSnowflakeIds(): void
    {
        $this->usesUniqueIds = true;
    }

    public function uniqueIds(): array
    {
        return [$this->getKeyName()];
    }

    public function newUniqueId()
    {
        return app(Snowflake::class)->id();
    }

    public function getIncrementing()
    {
        if (in_array($this->getKeyName(), $this->uniqueIds())) {
            return false;
        }

        return $this->incrementing;
    }
}
