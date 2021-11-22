<?php

namespace Supremel\Utils;

class RedisLock
{
    /**
     * Attempt to acquire the lock.
     *
     * @param string $name
     * @param  string|null  $owner
     * @param int $seconds
     * @return bool
     */
    public static function acquire ($redisConnect, string $name, string $owner, int $seconds)
    {
        $result = $redisConnect->setnx($name, $owner);

        if ($result === 1 && $seconds > 0) {
            $redisConnect->expire($name, $seconds);
        }

        return $result === 1;
    }

    /**
     * Release the lock.
     *
     * @param string $name
     * @param  string|null  $owner
     * @return void
     */
    public static function release ($redisConnect, string $name, string $owner)
    {
        $redisConnect->eval(self::releaseLock(), 1, $name, $owner);
    }

    /**
     * Releases this lock in disregard of ownership.
     *
     * @return void
     */
    public static function forceRelease ($redisConnect, string $name)
    {
        $redisConnect->del($name);
    }

    /**
     * Attempt to acquire the lock.
     *
     * @param string $name
     * @param  string|null  $owner
     * @param int $seconds
     * @param callable|null $callback
     * @return mixed
     */
    public static function get ($redisConnect, string $name, string $owner, int $seconds, callable $callback = null)
    {
        $result = self::acquire($redisConnect, $name, $owner, $seconds);

        if ($result && is_callable($callback)) {
            try {
                return $callback();
            } finally {
                self::release($redisConnect, $name, $owner);
            }
        }

        return $result;
    }

    /**
     * Attempt to acquire the lock for the given number of seconds.
     *
     * @param  int  $seconds
     * @param  callable|null  $callback
     * @return bool
     *
     */
    public static function block ($redisConnect, string $name, string $owner, int $seconds, int $blockSeconds, callable $callback = null)
    {
        $starting = time();

        while (! self::acquire($redisConnect, $name, $owner, $seconds)) {
            usleep(250 * 1000);

            if (time() - $blockSeconds >= $starting) {
                return false;
            }
        }

        if (is_callable($callback)) {
            try {
                return $callback();
            } finally {
                self::release($redisConnect, $name, $owner);
            }
        }

        return true;
    }

    /**
     * Get the Lua script to atomically release a lock.
     *
     * KEYS[1] - The name of the lock
     * ARGV[1] - The owner key of the lock instance trying to release it
     *
     * @return string
     */
    public static function releaseLock()
    {
        return <<<'LUA'
if redis.call("get",KEYS[1]) == ARGV[1] then
    return redis.call("del",KEYS[1])
else
    return 0
end
LUA;
    }
}