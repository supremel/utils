<?php

namespace Supremel\Utils;

class ArrUtils
{
    /**
     * Determines if an array is associative.
     * An array is "associative" if it doesn't have sequential numerical keys beginning with zero.
     */
    public static function isAssoc(array $array): bool
    {
        $keys = array_keys($array);
        return array_keys($keys) !== $keys;
    }

    /**
     * 去除前后空格
     *
     * @param $input
     * @return array|string
     */
    public static function trimArray ($input)
    {
        if (!is_array($input)) {
            return trim($input);
        }

        return array_map(['self', 'trimArray'], $input);
    }

    /**
     * @param $input
     * @param $sep // 字符串分隔符
     * @return array|string
     *
     * @example ['1,2,3', '4,5,6', '3,2,5,7,8'] // 入参
     * @example ['1', '2', '3', '4', '5', '6', '7', '8'] // 返回
     *
     * @example '1,2,3' // 入参
     * @example ['1', '2', '3'] // 返回
     */
    public static function str2Arr ($input, $sep = ',')
    {
        if (!is_array($input)) {
            return array_filter(explode($sep, $input)); // 会把 值为 false、null、''、0、'0' 的元素过滤
        }

        return array_unique(call_user_func_array('array_merge', array_map(['self', 'str2Arr'], $input)));
    }

    /**
     * 概率分配
     * @param $arrRate // 参与分配的概率数组
     * @return mixed
     *
     * @example ['key1' => 50, 'key2' => 30, 'key3' => 20] // 入参
     * @example 'key1'  // 返回
     */
    public static function hitProbability(array $arrRate)
    {
        $result = null;
        $randRate = mt_rand(1, array_sum($arrRate));

        foreach ($arrRate as $key => $rate) {
            if ($randRate <= $rate) {
                $result = $key;
                break;
            } else {
                $randRate -= $rate;
            }
        }

        return $result;
    }

}

