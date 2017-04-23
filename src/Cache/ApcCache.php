<?php

namespace GcpRestGuzzleAdapter\Cache;

class ApcCache implements CacheInterface
{
    /**
     * @inheritDoc
     */
    public function get($key)
    {
        return apc_fetch($key);
    }

    /**
     * @inheritDoc
     */
    public function set($key, $value)
    {
        return apc_add($key, $value);
    }
}