<?php

namespace Supremel\Utils;

class StrUtils
{
    /**
     * 对身份证号码做掩码处理
     * @param $identity
     * @return string
     */
    public static function maskIdentity ($identity): string
    {
        if (empty($identity)) {
            return '';
        }
        return substr($identity, 0, 3) . '************' . substr($identity, 15, 3);
    }

    /**
     * 手机号掩码处理
     * @param $phone
     * @return bool|string
     */
    public static function maskPhone($phone)
    {
        if (empty($phone)) {
            return '';
        }
        return substr($phone, 0, 3) . '****' . substr($phone, -4);
    }

    /**
     * 银行卡号掩码处理
     * @param $cardNo
     * @return bool|string
     */
    public static function maskCardNo($cardNo)
    {
        if (empty($cardNo)) {
            return '';
        }
        return substr($cardNo, 0, 4) . '****';
    }

    /**
     * 对姓名做掩码处理
     * @param $name
     * @return string
     */
    public static function maskChineseName($name): string
    {
        if (empty($name)) {
            return '';
        }
        $l = mb_strlen($name);
        switch ($l) {
            case 2:
                $maskedName = '*' . mb_substr($name, $l - 1, 1);
                break;
            default:
                $maskedName = '**' . mb_substr($name, $l - 1, 1);
                break;
        }
        return $maskedName;
    }

    /**
     * 根据身份证号获取年龄
     * @param $identity
     * @return int
     */
    public static function getAgeByIdentity($identity): int
    {
        return intval(date('Y')) - intval(substr($identity, 6, 4));
    }

    const GENDER_WOMEN = 1; // 女
    const GENDER_MEN = 0; // 男
    /**
     * 根据身份证号获取性别
     * @param $idCard
     * @return int
     */
    public static function getSexByIdentity($idCard): int
    {
        $position = (strlen($idCard) == 15 ? -1 : -2);
        if (substr($idCard, $position, 1) % 2 == 0) {
            return self::GENDER_WOMEN;
        }
        return self::GENDER_MEN;
    }

}

