<?php

namespace restotech\standard\backend\components;

use Yii;
use restotech\standard\backend\models\Settings;
use yii\imagine\Image;
use yii\web\UploadedFile;

class Tools {

    private static $isIncluceScp;

    public static function loadIsIncludeScp() {

        Tools::$isIncluceScp = Settings::find()
            ->andWhere(['like', 'setting_name', 'tax_include_service_charge'])
            ->one();
    }

    public static function hitungServiceChargePajak($totalHarga, $persenServiceCharge, $persenPajak) {

        $modelSettingTaxIncludeServiceCharge = Tools::$isIncluceScp;

        $serviceCharge = round($totalHarga * $persenServiceCharge / 100);

        $pajak = 0;

        if ($modelSettingTaxIncludeServiceCharge->setting_value) {

            $pajak = round(($totalHarga + $serviceCharge) * $persenPajak / 100);
        } else {

            $pajak = round($totalHarga * $persenPajak / 100);
        }

        $arr['serviceCharge'] = $serviceCharge;
        $arr['pajak'] = $pajak;

        return $arr;
    }

    public static function jsHitungServiceChargePajak() {

        $modelSettingTaxIncludeServiceCharge = Settings::find()
            ->andWhere(['like', 'setting_name', 'tax_include_service_charge'])
            ->one();

        $pajak = '';
        if ($modelSettingTaxIncludeServiceCharge->setting_value) {

            $pajak = 'var pajakVal = Math.round((parseFloat(totalHarga) + serviceChargeVal) * parseFloat(persenPajak) / 100);';
        } else {

            $pajak = 'var pajakVal = Math.round(parseFloat(totalHarga) * parseFloat(persenPajak) / 100);';
        }

        $jscript = '
            var hitungServiceChargePajak = function(totalHarga, persenCharge, persenPajak) {
                var serviceChargeVal = Math.round(parseFloat(totalHarga) * parseFloat(persenCharge) / 100);' .
                $pajak . '

                var arr = {serviceCharge: serviceChargeVal, pajak: pajakVal};

                return arr;
            };
        ';
        return $jscript;
    }

    public static function array_sort($array, $on, $order = SORT_ASC) {
        $new_array = array();
        $sortable_array = array();

        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                } else {
                    $sortable_array[$k] = $v;
                }
            }

            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }

            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }

        return $new_array;
    }

    public static function printToServer($message) {

        $flag = false;

        if (($socket = socket_create(AF_INET, SOCK_STREAM, 0)) == false)
            $flag = false;

        $settings = Settings::getSettingsByName(['print_server_ip_address', 'print_server_port']);

        if (socket_connect($socket, $settings['print_server_ip_address'], $settings['print_server_port'])) {

            if (socket_write($socket, $message, strlen($message) + 1)) {
                $flag = true;
            } else {
                $flag = false;
            }
        } else {
            $flag = false;
        }

        socket_close($socket);

        return $flag;
    }

    public static function convertToCurrency($value, $isConvert = true) {
        if ($isConvert)
            return Yii::$app->formatter->asCurrency($value);
        else
            return $value;
    }

    public static function uploadFile($basePath, $model, $field, $fieldId, $suffix = '') {
        $name = null;

        $file = UploadedFile::getInstance($model, $field);
        if ($file) {

            if (!empty($model->oldAttributes[$field])) {
                $filename = Yii::getAlias('@uploads') . $basePath . $model->oldAttributes[$field];

                if (file_exists($filename))
                    unlink($filename);
            }

            $rand = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 16);

            $flag = $file->saveAs(Yii::getAlias('@uploads') . $basePath . $model->$fieldId . $rand . $suffix . '.' . $file->extension);

            if ($flag)
                $name = $model->$fieldId . $rand . $suffix . '.' . $file->extension;
        }

        return $name;
    }

    public static function thumb($basePath, $field, $width, $height, $keepRatio = false) {

        if (!empty($field)) {

            $filename = Yii::getAlias('@uploads') . $basePath . $width . 'x' . $height . ($keepRatio ? 'ratio' : '') . $field;

            if (!file_exists($filename)) {

                try {
                    if (!$keepRatio) {

                        Image::thumbnail('@uploads' . $basePath . $field, $width, $height)
                                ->save($filename, ['quality' => 100]);
                    } else {

                        Image::resize('@uploads' . $basePath . $field, $width, $height)
                                ->save($filename , ['quality' => 100]);
                    }

                } catch (\Exception $exc) {

                }
            }

            return $basePath . $width . 'x' . $height . ($keepRatio ? 'ratio' : '') . $field;
        }
    }

    public static function humanTiming($time) {
        $timestamp = $time;

        $time = time() - $time; // to get the time since that moment
        $time = ($time < 1) ? 1 : $time;
        $tokens = array(
            31536000 => 'year',
            2592000 => 'month',
            604800 => 'week',
            86400 => 'day',
            3600 => 'hour',
            60 => 'minute',
            1 => 'second'
        );

        foreach ($tokens as $unit => $text) {
            if ($time < $unit)
                continue;
            $numberOfUnits = floor($time / $unit);

            $string = $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ago';

            $stringAdded = '';

            if ($time >= 3600) {
                $stringAdded = ', ' . date('H:i:s', $timestamp);

                if (date('z', $timestamp) != date('z') || $time >= 86400) {
                    $stringAdded = ', ' . date('d M Y H:i:s', $timestamp);

                    if ((date('z') - date('z', $timestamp)) == 1)
                        $stringAdded = ', yesterday ' . date('H:i:s', $timestamp);
                }
            }

            $string .= $stringAdded;

            return $string;
        }
    }

}
