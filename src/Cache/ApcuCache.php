<?php

namespace GcpRestGuzzleAdapter\Cache;

class ApcuCache implements CacheInterface
{
    /**
     * @inheritDoc
     */
    public function get($key)
    {
        return apcu_fetch($key);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value)
    {
        return apcu_store($key, $value);
    }
}