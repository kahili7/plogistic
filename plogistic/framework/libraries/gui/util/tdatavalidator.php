<?php

    /**
     * Quick PHP Framework
     * Copyright (c) 2010 QPHP team <support@qphp.net>
     * This file is part of the QPHP source code 
     * It may be used under the terms of the license http://qphp.net/license.php
     *
     * <pre>
     *
     * TDataValidator is used for validating user input data.
     *
     * <u>Example:</u>
     *
     *     // data validator
     *     $validator = new TDataValidator();
     *     
     *     $validator->isNotEmpty("", "Value must be specified."); // error
     *     $validator->isEqual("cFr#6", "dW1vHy6", "Both passwords must be equal."); // error
     *     $validator->isMoney(12.50, "Not a valid price"); // ok
     *     $validator->isLessThanOrEqual2(5.45, 8.90, "The first price must be cheaper than the second."); // ok
     *     
     *     if ($validator->hasErrors()) {
     *         // do something with the errors
     *         print_r($validator->getErrors());
     *         // it will print both "Value must be specified." and "Both passwords must be equal."
     *     }
     * </pre>
     *
     * @package System.Util
     */
    class TDataValidator {

        /**
         * Contains all the errors
         * @var array
         */
        private $fErrors = Array();




        /**
         * Constructor
         */
        public function TDataValidator() {
        }




        /**
         * Get errors
         *
         * @return array errors
         */
        public function getErrors() {
            return $this->fErrors;
        }


        /**
         * Check for errors
         *
         * @return bool True if any error exists
         */
        public function hasErrors() {
            return (count($this->fErrors)>0);
        }


        /**
         * Add error
         *
         * @param string $errorMsg Error message
         */
        public function addError($errorMsg) {
            if ($errorMsg) {
                $this->fErrors[] = $errorMsg;
            }
        }


        /**
         * Clear all acumulated errors.
         */
        public function clearErrors() {
            $this->fErrors = Array();
        }


        /**
         * Checks to see if $value is not null.
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isNotNull($value, $errorMsg = NULL) {
        	  if ($value === NULL) {
                $this->addError($errorMsg);
            	  return FALSE;
            }
            return TRUE;
        }


        /**
         * Checks to see if $value is not null.
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkNull($value, $errorMsg = NULL) {
        	  if ($value === NULL) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value is not empty. This means it must be not null or empty string "".
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isNotEmpty($value, $errorMsg = NULL) {
        	  if ($value === NULL || $value === "") {
                $this->addError($errorMsg);
            	  return FALSE;
            }
            return TRUE;
        }


        /**
         * Checks to see if $value is not empty. This means it must be not null or empty string "".
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkEmpty($value, $errorMsg = NULL) {
        	  if ($value === NULL || $value === "") {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value1 is equal to $value2.
         *
         * @param string $value1 First value
         * @param string $value2 Second value
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function equal($value1, $value2, $errorMsg = NULL) {
            return $this->isEqual($value1, $value2, $errorMsg);
        }


        /**
         * Checks to see if $value1 is equal to $value2.
         *
         * @param string $value1 First value
         * @param string $value2 Second value
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isEqual($value1, $value2, $errorMsg = NULL) {   
        	  if ($value1 != $value2) {
                $this->addError($errorMsg);
            	  return FALSE;
            }
            return TRUE;
        }


        /**
         * Checks to see if $value1 is equal to $value2.
         *
         * @param string $value1 First value
         * @param string $value2 Second value
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkEqual($value1, $value2, $errorMsg = NULL) {
            $inst = new TDataValidator();
        	  if (!$inst->isEqual($value1, $value2, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value is between $min and $max.
         *
         * @param string $value Value to be checked
         * @param char $min Minimum
         * @param char $max Maximum
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isCharRange($value, $min, $max, $errorMsg = NULL) {   
        	  if (strlen($value) < $min || strlen($value) > $max) {
                $this->addError($errorMsg);
            	  return FALSE;
            }
            return TRUE;
        }


        /**
         * Checks to see if $value is between $min and $max.
         *
         * @param string $value Value to be checked
         * @param char $min Minimum
         * @param char $max Maximum
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkCharRange($value, $min, $max, $errorMsg = NULL) {   
            $inst = new TDataValidator();
        	  if (!$inst->isCharRange($value, $min, $max, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value is an integer.
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isInt($value, $errorMsg = NULL) {   
        	  if (strpos($value, '.')===FALSE && is_numeric($value)) {
                return TRUE;
        	  }

            $this->addError($errorMsg);
            return FALSE;
        }


        /**
         * Checks to see if $value is an integer.
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkInt($value, $errorMsg = NULL) {   
            $inst = new TDataValidator();
        	  if (!$inst->isInt($value, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value1 is less than or equal to $value2.
         *
         * @param integer $value1 First value
         * @param integer $value2 Second value
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isLessThanOrEqual($value1, $value2, $errorMsg = NULL) {   
        	  if (strpos($value1, '.')===FALSE && is_numeric($value1) && strpos($value2, '.')===FALSE && is_numeric($value2) && $value1<=$value2) {
                return TRUE;
        	  }

            $this->addError($errorMsg);
            return FALSE;
        }


        /**
         * Checks to see if $value1 is less than or equal to $value2.
         *
         * @param integer $value1 First value
         * @param integer $value2 Second value
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkLessThanOrEqual($value1, $value2, $errorMsg = NULL) {   
            $inst = new TDataValidator();
        	  if (!$inst->isLessThanOrEqual($value1, $value2, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value1 is less than or equal to $value2.
         *
         * @param integer|real|double $value1 First value
         * @param integer|real|double $value2 Second value
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isLessThanOrEqual2($value1, $value2, $errorMsg = NULL) {   
        	  if (is_numeric($value1) && is_numeric($value2) && $value1<=$value2) {
                return TRUE;
        	  }

            $this->addError($errorMsg);
            return FALSE;
        }


        /**
         * Checks to see if $value1 is less than or equal to $value2.
         *
         * @param integer|real|double $value1 First value
         * @param integer|real|double $value2 Second value
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkLessThanOrEqual2($value1, $value2, $errorMsg = NULL) {   
            $inst = new TDataValidator();
        	  if (!$inst->isLessThanOrEqual2($value1, $value2, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value1 is greater than or equal to $value2.
         *
         * @param integer $value1 First value
         * @param integer $value2 Second value
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isGreaterThanOrEqual($value1, $value2, $errorMsg = NULL) {   
        	  if (strpos($value1, '.')===FALSE && is_numeric($value1) && strpos($value2, '.')===FALSE && is_numeric($value2) && $value1>=$value2) {
                return TRUE;
        	  }

            $this->addError($errorMsg);
            return FALSE;
        }


        /**
         * Checks to see if $value1 is greater than or equal to $value2.
         *
         * @param integer $value1 First value
         * @param integer $value2 Second value
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkGreaterThanOrEqual($value1, $value2, $errorMsg = NULL) {   
            $inst = new TDataValidator();
        	  if (!$inst->isGreaterThanOrEqual($value1, $value2, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value1 is greater than or equal to $value2.
         *
         * @param integer|real|double $value1 First value
         * @param integer|real|double $value2 Second value
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isGreaterThanOrEqual2($value1, $value2, $errorMsg = NULL) {   
        	  if (is_numeric($value1) && is_numeric($value2) && $value1>=$value2) {
                return TRUE;
        	  }

            $this->addError($errorMsg);
            return FALSE;
        }


        /**
         * Checks to see if $value1 is greater than or equal to $value2.
         *
         * @param integer|real|double $value1 First value
         * @param integer|real|double $value2 Second value
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkGreaterThanOrEqual2($value1, $value2, $errorMsg = NULL) {   
            $inst = new TDataValidator();
        	  if (!$inst->isGreaterThanOrEqual2($value1, $value2, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value is between $min and $max.
         *
         * @param integer $value Value to be checked
         * @param integer $min Minimum
         * @param integer $max Maximum
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isIntRange($value, $min, $max, $errorMsg = NULL) {   
            if ($value < $min || $value > $max ) {
                $this->addError($errorMsg);
            	  return FALSE;
            }

            return TRUE;
        }


        /**
         * Checks to see if $value is between $min and $max.
         *
         * @param integer $value Value to be checked
         * @param integer $min Minimum
         * @param integer $max Maximum
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkIntRange($value, $min, $max, $errorMsg = NULL) {   
            $inst = new TDataValidator();
        	  if (!$inst->isIntRange($value, $min, $max, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value contains only digits - 0,1,2..9
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isDigits($value, $errorMsg = NULL) {   
        	  if (!preg_match ("/^[0-9]+$/i", $value)) {
                $this->addError($errorMsg);
            	  return FALSE;
            }

            return TRUE;
        }


        /**
         * Checks to see if $value contains only digits - 0,1,2..9
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkDigits($value, $errorMsg = NULL) {   
            $inst = new TDataValidator();
        	  if (!$inst->isDigits($value, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value contains only letters and digits - 0,1,2..9,a,b..z,A,B..Z
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isLettersAndDigits($value, $errorMsg = NULL) {   
        	  if (!preg_match("/^[a-zA-Z0-9]+$/i", $value)) {
                $this->addError($errorMsg);
            	  return FALSE;
            }

            return TRUE;
        }


        /**
         * Checks to see if $value contains only letters and digits - 0,1,2..9,a,b..z,A,B..Z
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkLettersAndDigits($value, $errorMsg = NULL) {   
            $inst = new TDataValidator();
        	  if (!$inst->isLettersAndDigits($value, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value contains only letters, digits and _
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isIdentifier($value, $errorMsg = NULL) {   
        	  if (!preg_match ("/^[a-zA-Z0-9_]+$/i", $value)) {
                $this->addError($errorMsg);
            	  return FALSE;
            }
            return TRUE;
        }


        /**
         * Checks to see if $value contains only letters, digits and _
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkIdentifier($value, $errorMsg = NULL) {   
            $inst = new TDataValidator();
        	  if (!$inst->isIdentifier($value, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value has money structure:12.00 or 11.99(true), 15 or 12.955 or 12,00(false)
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isMoney($value, $errorMsg = NULL) {   
        	  if (!preg_match ("/^[0-9]{1,10}\.[0-9]{2,2}$/i", $value)) {
                $this->addError($errorMsg);
            	  return FALSE;
            }

            return TRUE;
        }


        /**
         * Checks to see if $value has money structure:12.00 or 11.99(true), 15 or 12.955 or 12,00(false)
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkMoney($value, $errorMsg = NULL) {
            $inst = new TDataValidator();
        	  if (!$inst->isMoney($value, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value has money structure and fits in range
         *
         * @param string $value Value to be checked
         * @param integer|real|double $minAmount Minimum amount
         * @param integer|real|double $maxAmount Maximum amount
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isMoneyRange($value, $minAmount, $maxAmount, $errorMsg = NULL) {   
            if (!preg_match ("/^[0-9]{1,10}\.[0-9]{2,2}$/i", $value)) {
                $this->addError($errorMsg);
            	  return FALSE;
            }

            if ($value < $minAmount || $value > $maxAmount) {
                $this->addError($errorMsg);
            	  return FALSE;
            }

            return TRUE;
        }


        /**
         * Checks to see if $value has money structure and fits in range
         *
         * @param string $value Value to be checked
         * @param integer|real|double $minAmount Minimum amount
         * @param integer|real|double $maxAmount Maximum amount
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkMoneyRange($value, $minAmount, $maxAmount, $errorMsg = NULL) {
            $inst = new TDataValidator();
        	  if (!$inst->isMoneyRange($value, $minAmount, $maxAmount, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value is like 12.00 or 21.00, but not 19.95
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isRoundMoney($value, $errorMsg = NULL) {
            if (!preg_match ("/^\d+\.00$/i", $value)) {
                $this->addError($errorMsg);
            	  return FALSE;
            }

            return TRUE;
        }


        /**
         * Checks to see if $value is like 12.00 or 21.00, but not 19.95
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkRoundMoney($value, $errorMsg = NULL) {
            $inst = new TDataValidator();
        	  if (!$inst->isRoundMoney($value, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value has phone structure:
         * +180039788347(true)
         * +1-800-3978 8347(true)
         * 1 800 3978 8347(true)
         * 1 800 3978 83 47(true)
         * 1.800.397883,47(false)
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isPhone($value, $errorMsg = NULL) {   
        	  if (!preg_match ("/^\+?[0-9,\-, ]+$/i", $value)) {
                $this->addError($errorMsg);
            	  return FALSE;
            }

            return TRUE;
        }


        /**
         * Checks to see if $value has phone structure:
         * +180039788347(true)
         * +1-800-3978 8347(true)
         * 1 800 3978 8347(true)
         * 1 800 3978 83 47(true)
         * 1.800.397883,47(false)
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkPhone($value, $errorMsg = NULL) {   
            $inst = new TDataValidator();
        	  if (!$this->isPhone($value, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value has date structure.
         * There are 3 popular date formats:
         * MM/DD/YYYY(us), DD.MM.YYYY(eu) and YYYY-MM-DD(iso)
         * It will return TRUE for any of them. Using strings like Jan/01/2000 
         * containing letters will result to False.
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isDate($value, $errorMsg = NULL) {   
            // there are 3 popular date formats - MM/DD/YYYY(us), DD.MM.YYYY(eu) and YYYY-MM-DD(iso)
            // I do a check for every one of them, is the day is in (1..31), month in (1..12) and the year must be greater than 0
            if (preg_match ("/^\d+\/\d+\/\d+$/i", $value)) {
                $date_parts = preg_split ("/[\/]+/", $value);
                if ($date_parts[0] < 1 || $date_parts[0] > 12 || $date_parts[1] < 1 || $date_parts[1] > 31 || $date_parts[2] == 0) {
                    $this->addError($errorMsg);
                	  return FALSE;
                }
            }
            else if (preg_match ("/^\d+\.\d+\.\d+$/i", $value)) {
                $date_parts = preg_split ("/[\.]+/", $value);
                if ($date_parts[0] < 1 || $date_parts[0] > 31 || $date_parts[1] < 1 || $date_parts[1] > 12 || $date_parts[2] == 0) {
                    $this->addError($errorMsg);
                	  return false;
                }
            }
            else if (preg_match ("/^\d+\-\d+\-\d+$/i", $value)) {
                $date_parts = preg_split ("/[\-]+/", $value);
                if ($date_parts[0] == 0 || $date_parts[1] < 1 || $date_parts[1] > 12 || $date_parts[2] < 1 || $date_parts[2] > 31) {
                    $this->addError($errorMsg);
                	  return FALSE;
                }
            }
            else {
                $this->addError($errorMsg);
            	  return FALSE;
            }
            
            return TRUE;
        }


        /**
         * Checks to see if $value has date structure.
         * There are 3 popular date formats:
         * MM/DD/YYYY(us), DD.MM.YYYY(eu) and YYYY-MM-DD(iso)
         * It will return TRUE for any of them. Using strings like Jan/01/2000 
         * containing letters will result to False.
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkDate($value, $errorMsg = NULL) {   
            $inst = new TDataValidator();
        	  if (!$inst->isDate($value, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if value has TIME structure:
         * 22:01(true), 18:15(true), 24:01(false), 23:60(false)
         * 22:01:05(true), 18:15:01(true), 22:01:60(false)
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isTime($value, $errorMsg = NULL) {
            $h; $m; $s;
            $flag = FALSE;
            if (preg_match("/^\d+\:\d+$/i", $value)) {
                $parts = preg_split ("/[\:]+/", $value);
                $h = $parts[0]; $m = $parts[1]; $s = 0;
                $flag = TRUE;
            }
            else if (preg_match("/^\d+\:\d+\:\d+$/i", $value)) {
                $parts = preg_split ("/[\:]+/", $value);
                $h = (int)$parts[0]; $m = (int)$parts[1]; $s = (int)$parts[2];
                $flag = TRUE;
            }

            if (!$flag) {
                $this->addError($errorMsg);
            	  return FALSE;
            }

            if ($h < 0 || $h > 23 || $m < 0 || $m > 59 || $s < 0 || $s > 59) {
                $this->addError($errorMsg);
            	  return FALSE;
            }

            return TRUE;
        }


        /**
         * Checks to see if value has TIME structure:
         * 22:01(true), 18:15(true), 24:01(false), 23:60(false)
         * 22:01:05(true), 18:15:01(true), 22:01:60(false)
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkTime($value, $errorMsg = NULL) {
            $inst = new TDataValidator();
        	  if (!$inst->isTime($value, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if value has DATETIME structure:
         * 2009-11-23 22:01(true), 11/23/2009 18:15:09(true)
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isDateTime($value, $errorMsg = NULL) {
            $parts = explode(" ", $value);
            if (count($parts) != 2) {
                $this->addError($errorMsg);
            	  return FALSE;
            }

            if (!$this->isDate($parts[0]) || !$this->isTime($parts[1])) {
                $this->addError($errorMsg);
            	  return FALSE;
            }

            return TRUE;
        }


        /**
         * Checks to see if value has DATETIME structure:
         * 2009-11-23 22:01(true), 11/23/2009 18:15:09(true)
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkDateTime($value, $errorMsg = NULL) {
            $inst = new TDataValidator();
        	  if (!$inst->isDateTime($value, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }


        /**
         * Checks to see if $value contains any malicious structure like:
         * delete from table, update... table, etc.
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        public function isSqlInjectionSafe($value, $errorMsg = NULL) {
        	  $v = $value;
        	  if (is_array($v)) {
        	      $v = implode(",", $v);
        	  }

            if (preg_match ("/^.*(drop|create|alter|replace|update|insert|delete|select|execute|grant).*(table|view|index|function|procedure|trigger|sequence|database|group|language|operator|user|role|schema|tablespace).*$/i", $v)) {
                $this->addError($errorMsg);
            	  return FALSE;
            }

            return TRUE;
        }


        /**
         * Checks to see if $value contains any malicious structure like:
         * delete from table, update... table, etc.
         *
         * @param string $value Value to be checked
         * @param string $errorMsg Error message. Optional
         * @return bool If False $errorMsg is added to a list of errors
         */
        static public function checkSqlInjectionSafe($value, $errorMsg = NULL) {
            $inst = new TDataValidator();
        	  if (!$inst->isSqlInjectionSafe($value, $errorMsg)) {
            	  throw new TDataValidatorException($errorMsg);
            }
        }
    }

?>