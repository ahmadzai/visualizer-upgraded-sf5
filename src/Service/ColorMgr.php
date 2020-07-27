<?php
namespace App\Service;
/**
 * Created by PhpStorm.
 * User: Wazir Khan
 * Date: 05/06/2018
 * Time: 01:57 PM
 */

class ColorMgr
{

    /**
     * @param $value
     * @param $min
     * @param $max
     * @param null $gradientColors
     * @param $reverse
     * @return string
     */
    public static function numberToColor($value, $min, $max, $gradientColors = null, $reverse = false)
    {
        if($reverse) {
            // Check if value is 0
            if($value == 0)
                $value = $max;
            else {
                if ($value >= $max)
                    $value = $min;
                elseif ($value < $max && $value > $min) {
                    $middle = (int)($min + $max) / 2;
                    if ($value < $middle) {
                        $diff = $middle - $value;
                        $value = $middle + $diff;
                    } elseif ($value > $middle) {
                        $diff = $value - $middle;
                        $value = $middle - $diff;
                    } else
                        $value = $middle;

                } elseif ($value <= $min)
                    $value = $max;
            }
        }
        // Ensure value is in range
        if ($value < $min) {
            $value = $min;
        }
        if ($value > $max) {
            $value = $max;
        }

        // Normalize min-max range to [0, positive_value]
        $max -= $min;
        $value -= $min;
        $min = 0;

        // Calculate distance from min to max in [0,1]
        $distFromMin = $max > 0 ? $value / $max : 0;

        // Define start and end color
        if ((is_array($gradientColors) && count($gradientColors) == 0) || $gradientColors === null) {
            return self::numberToColor($value, $min, $max, ['#CC0000', '#EEEE00', '#00FF00']);
        } else if (count($gradientColors) == 2) {
            $startColor = $gradientColors[0];
            $endColor = $gradientColors[1];
        } else if (count($gradientColors) > 2) {
            $startColor = $gradientColors[floor($distFromMin * (count($gradientColors) - 1))];
            $endColor = $gradientColors[ceil($distFromMin * (count($gradientColors) - 1))];

            $distFromMin *= count($gradientColors) - 1;
            while ($distFromMin > 1) {
                $distFromMin--;
            }
        } else {
            die("Please pass more than one color or null to use default red-green colors.");
        }

        // Remove hex from string
        if ($startColor[0] === '#') {
            $startColor = substr($startColor, 1);
        }
        if ($endColor[0] === '#') {
            $endColor = substr($endColor, 1);
        }

        // Parse hex
        list($ra, $ga, $ba) = sscanf("#$startColor", "#%02x%02x%02x");
        list($rz, $gz, $bz) = sscanf("#$endColor", "#%02x%02x%02x");

        // Get rgb based on
        $distDiff = 1 - $distFromMin;
        $r = intval(($rz * $distFromMin) + ($ra * $distDiff));
        $r = min(max(0, $r), 255);
        $g = intval(($gz * $distFromMin) + ($ga * $distDiff));
        $g = min(max(0, $g), 255);
        $b = intval(($bz * $distFromMin) + ($ba * $distDiff));
        $b = min(max(0, $b), 255);

        // Convert rgb back to hex
        $rgbColorAsHex = '#' .
            str_pad(dechex($r), 2, "0", STR_PAD_LEFT) .
            str_pad(dechex($g), 2, "0", STR_PAD_LEFT) .
            str_pad(dechex($b), 2, "0", STR_PAD_LEFT);

        return $rgbColorAsHex;
    }

    /**
     * @param $i
     * @param $min
     * @param $max
     * @return string
     */
    public static function numToColor($i, $min, $max) {
        $ratio = $i;
        if ($min> 0 || $max < 1) {
            if ($i < $min) {
                $ratio = 0;
            } else if ($i > $max) {
                $ratio = 1;
            } else {
                $range = $max - $min;
                $ratio = ($i-$min) / $range;
            }
        }

        // as the function expects a value between 0 and 1, and red = 0° and green = 120°
        // we convert the input to the appropriate hue value
        $hue = $ratio * 1.2 / 3.60;

        // we convert hsl to rgb (saturation 100%, lightness 50%)
        $rgb = self::hslToRgb($hue, 1, .5);
        // we format to css value and return
        return 'rgb('.$rgb[0].','.$rgb[1].','.$rgb[2].')';
    }


    /**
     * @param $h
     * @param $s
     * @param $l
     * @return array
     */
    public static function hslToRgb($h, $s, $l){
        $r = $b = $g = null;
        if($s == 0) {
            $r = $g = $b = $l; // achromatic
        } else {
            $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
            $p = 2 * $l - $q;
            $r = self::hue2rgb($p, $q, $h + 1/3);
            $g = self::hue2rgb($p, $q, $h);
            $b = self::hue2rgb($p, $q, $h - 1/3);
        }

        return [floor($r * 255), floor($g * 255), floor($b * 255)];
    }


    /**
     * @param $p
     * @param $q
     * @param $t
     * @return float|int
     */
    public static function hue2rgb($p, $q, $t){
        if($t < 0) $t += 1;
        if($t > 1) $t -= 1;
        if($t < 1/6) return $p + ($q - $p) * 6 * $t;
        if($t < 1/2) return $q;
        if($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
        return $p;
    }

}
