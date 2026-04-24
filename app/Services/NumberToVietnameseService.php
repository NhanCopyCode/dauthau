<?php

namespace App\Services;

class NumberToVietnameseService
{
    protected static $numbers = [
        'không',
        'một',
        'hai',
        'ba',
        'bốn',
        'năm',
        'sáu',
        'bảy',
        'tám',
        'chín'
    ];

    protected static $units = ['', 'nghìn', 'triệu', 'tỷ'];

    public static function convert($number): string
    {
        if ($number == 0) {
            return 'Không đồng';
        }

        $result = '';
        $i = 0;

        while ($number > 0) {
            $block = $number % 1000;

            if ($block != 0) {
                $result = self::readThreeDigits($block) . ' ' . self::$units[$i] . ' ' . $result;
            }

            $number = floor($number / 1000);
            $i++;
        }

        $result = trim(preg_replace('/\s+/', ' ', $result));

        return ucfirst($result) . ' đồng';
    }

    protected static function readThreeDigits($number): string
    {
        $hundreds = floor($number / 100);
        $tens = floor(($number % 100) / 10);
        $units = $number % 10;

        $result = '';

        // Hàng trăm
        if ($hundreds > 0) {
            $result .= self::$numbers[$hundreds] . ' trăm ';
        }

        // Hàng chục
        if ($tens > 1) {
            $result .= self::$numbers[$tens] . ' mươi ';

            if ($units == 1) {
                $result .= 'mốt ';
            } elseif ($units == 5) {
                $result .= 'lăm ';
            } elseif ($units > 0) {
                $result .= self::$numbers[$units] . ' ';
            }
        } elseif ($tens == 1) {
            $result .= 'mười ';

            if ($units == 5) {
                $result .= 'lăm ';
            } elseif ($units > 0) {
                $result .= self::$numbers[$units] . ' ';
            }
        } elseif ($tens == 0 && $units > 0) {
            if ($hundreds > 0) {
                $result .= 'lẻ ';
            }
            $result .= self::$numbers[$units] . ' ';
        }

        return trim($result);
    }
}
