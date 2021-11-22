<?php

namespace Supremel\Utils;

class SignUtils
{
    /**
     * SHA512withRSA方式生成16进制签名
     * @param $params
     * @return string
     */
    public static function genSha512WithRsa ($privateKey, $params): string
    {
        $pkResource = openssl_pkey_get_private($privateKey);
        $sign = '';
        openssl_sign(self::sortAndSpliceParams($params), $sign, $pkResource, OPENSSL_ALGO_SHA512);
        openssl_free_key($pkResource);
        return bin2hex($sign);
    }

    /**
     * 校验签名-SHA512withRSA方式生成16进制签名
     * @param $sign
     * @param $params
     * @return bool
     */
    public static function verifySha512WithRsa ($publicKey, $sign, $params): bool
    {
        $pkResource = openssl_pkey_get_public($publicKey);
        $result = openssl_verify(self::sortAndSpliceParams($params), hex2bin($sign), $pkResource, OPENSSL_ALGO_SHA512);
        openssl_free_key($pkResource);
        return $result === 1;
    }

    /**
     * 参数排序和拼接
     * @param $params
     * @return string
     */
    private static function sortAndSpliceParams ($params)
    {
        if (is_string($params)) {
            return $params;
        }

        $str = '';
        ksort($params);
        foreach ($params as $key => $val) {
            if (is_array($val)) {
                if (ArrUtils::isAssoc($val)) {
                    $str .= $key . self::sortAndSpliceParams($val);
                } else {
                    $str .= $key;
                    foreach ($val as $row) {
                        $str .= self::sortAndSpliceParams($row);
                    }
                }
            } else {
                $str .= $key . $val;
            }
        }
        return $str;
    }

    /*生成证书*/
    public static function genOpenSSLKey ()
    {
        $config = array(
            "digest_alg"        => "sha512",
            "private_key_bits"  => 1024,           //字节数  512 1024 2048  4096 等
            "private_key_type"  => OPENSSL_KEYTYPE_RSA,   //加密类型
        );
        $res = openssl_pkey_new($config);
        if($res == false) return false;
        openssl_pkey_export($res, $private_key);
        $public_key = openssl_pkey_get_details($res);
        $public_key = $public_key["key"];
        openssl_free_key($res);

        return compact('private_key', 'public_key');
    }

}