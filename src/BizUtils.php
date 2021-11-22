<?php

namespace Supremel\Utils;

class BizUtils
{
    /**
     * @example 16370446521825(14)
     * @return string
     */
    public static function genMicroTime () : string
    {
        return strval(microtime(true) * 10000);
    }

    /**
     * @example 24efcaf94da625dce8749c1865a958de(32)
     * @param string $salt 盐值
     * @return string
     */
    public static function genUniqBiz (string $salt = ''): string
    {
        return md5($salt . uniqid(md5(self::genMicroTime())));
    }

    /**
     * 生成bizNo
     * @return string
     */
    public static function genBizNo(string $prefix = '', int $length = 32)
    {
        $prefix .= self::genMicroTime();
        $length -= strlen($prefix);
        $strs = "1234567890";
        while (strlen($strs) <= $length) {
            $strs .= $strs;
        }
        $code = substr(str_shuffle($strs), mt_rand(0, strlen($strs) - $length - 1), $length);
        return $prefix . $code;
    }

}

