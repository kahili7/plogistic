<?php

    /**
     * Quick PHP Framework
     * Copyright (c) 2010 QPHP team <support@qphp.net>
     * This file is part of the QPHP source code 
     * It may be used under the terms of the license http://qphp.net/license.php
     *
     * <pre>
     *
     * Provides default format functionality
     * 
     * </pre>
     *
     * @package System.Util
     */
    class TFormat {

        /**
         * Formats date. Takes as parameter 08/21/1987, 21.08.1987 or 1987-08-21
         * and returns 21 Aug 1987
         *
         * @param string $value Date 
         * @return string formatted date
         */
        static public function toDate($value) {

            if (!$value) {
                return "";
            }

            if (!TLocale::exist("month.shortname.1")) {
                registerWidget("date_and_time");
                $tmp = new TDateField();
                $tmp->loadLocaleStrings(TPage::getInstance()->getLocale());
            }

            $value = trim($value);

            $tokens = NULL;
            if (preg_match("/^\d+\-\d+\-\d+$/i", $value)) {
                $tmp = preg_split ("/[\-]+/", $value);
                if ($tmp[0] == 0 || $tmp[1] < 1 || $tmp[1] > 12 || $tmp[2] < 1 || $tmp[2] > 31) {
                    throw new TFormatException("Date is not formatted well - " . $value);
                }
                $tokens = Array('d' => $tmp[2], 'm' => $tmp[1], 'y' => $tmp[0]);
            }
            else if (preg_match("/^\d+\/\d+\/\d+$/i", $value)) {
                $tmp = preg_split("/[\/]+/", $value);
                if ($tmp[0] < 1 || $tmp[0] > 12 || $tmp[1] < 1 || $tmp[1] > 31 || $tmp[2] == 0) {
                    throw new TFormatException("Date is not formatted well - " . $value);
                }
                $tokens = Array('d' => $tmp[1], 'm' => $tmp[0], 'y' => $tmp[2]);
            }
            else if (preg_match("/^\d+\.\d+\.\d+$/i", $value)) {
                $tmp = preg_split ("/[\.]+/", $value);
                if ($tmp[0] < 1 || $tmp[0] > 31 || $tmp[1] < 1 || $tmp[1] > 12 || $tmp[2] == 0) {
                    throw new TFormatException("Date is not formatted well - " . $value);
                }
                $tokens = Array('d' => $tmp[0], 'm' => $tmp[1], 'y' => $tmp[2]);
            }

            if (!$tokens) {
                throw new TFormatException("Date is not formatted well - " . $value);
            }

            return str_pad($tokens['d'], 2, "0", STR_PAD_LEFT) . " " . TLocale::get("month.shortname." . ltrim($tokens['m'], "0")) . " " . $tokens['y'];
        }


        /**
         * Formats timestamp. Takes as parameter 08/21/1987 21:59:00,
         * 21.08.1987 21:59:00 or 1987-08-21 21:59:00
         * and returns 21 Aug 1987 21:59:00
         *
         * @param string $value Timestamp
         * @return string formatted timestamp
         */
        static public function toTimestamp($value) {

            if (!$value) {
                return "";
            }

            $value = trim($value);
            $arr = explode(" ", $value);

            if (count($arr) == 2) {
                return self::toDate($arr[0]) . " " . $arr[1];
            }
            
            throw new TFormatException("Value is not formatted well - " . $value);
        }
    }

?>