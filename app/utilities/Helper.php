<?php
namespace App\Utilities;

class Helper {
    public static function formatDate($date, $format = 'Y-m-d H:i:s') {
        if (empty($date)) {
            return '';
        }
        return date($format, strtotime($date));
    }

    public static function formatCurrency($amount) {
        return number_format($amount, 2);
    }

    public static function truncate($string, $length = 100, $suffix = '...') {
        if (strlen($string) <= $length) {
            return $string;
        }
        return substr($string, 0, $length) . $suffix;
    }

    public static function slug($string) {
        $string = strtolower(trim($string));
        $string = preg_replace('/[^a-z0-9-]/', '-', $string);
        $string = preg_replace('/-+/', '-', $string);
        return trim($string, '-');
    }
}

