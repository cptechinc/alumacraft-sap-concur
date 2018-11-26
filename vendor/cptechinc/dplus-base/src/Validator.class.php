<?php
    namespace Dplus\Base;

    /**
     * Class for validating values
     */
    class Validator {

        /**
         * Validate that value is an integer, float/decimal, boolean, or string. Value can be zero (0) or FALSE.
         * @param    string    $var             Value to validate
         * @return   bool
         */
        public function required($var) {
            return (isset($var) && (is_bool($var) || is_int($var) || is_float($var) || (is_string($var) && strlen($var) > 0)));
        }

        /**
         * Validate that value is an integer whether in string or not (numberic only).
         * 
         * @param    mixed    $var                Value to validate
         * @return   boolean
         */
        public function integer($var) {
            return (is_int($var) || (is_string($var) && ctype_digit($var)));
        }

        /**
         * Validate that value is an integer, float/decimal or number string with decimal
         * 
         * @param    mixed    $var                Value to validate
         * @return   bool
         */
        public function decimal($var) {
            if (!isset($var) || is_float($var) || is_int($var)) {
                return true;
            }

            return (is_string($var) && (empty($var) || preg_match('~^\d+(\.\d+)?$~', $var) === 1));
        }

        /**
         * Validate that value is an integer, float/decimal, or number string. 
         * // NOTE Only two decimal places are allowed.
         * 
         * @param    mixed    $var                 Value to validate
         * @return   bool
         */
        public function currency($var) {
            if (!isset($var)) {
                return true;
            }

            if (is_float($var) || is_int($var)) {
                $var = strval($var);
            }

            return (strlen($var) === 0 || preg_match('~^\d+(\.\d{1,2})?$~', $var) === 1);
        }
        
        /**
         * Validate that value is a date string in the YYYMMDD format
         * 
         * @param    mixed    $var                 Value to validate
         * @return   bool
         */
        public function date_yyyymmdd($var) {
            return (empty($var) || preg_match('~^\d{8}$~', $var) === 1);
        }


        /**
         * Validate that value is a date string in the YYYYMMDD HH:MM:SS format
         * 
         * @param    mixed    $var                Value to validate
         * @return   boolean
         */
        public function date_yyyymmdd_hhmmss($var) {
            return (empty($var) || preg_match('~^\d{4}-\d{2}-\d{2}\s\d{2}:\d{2}:\d{2}$~', $var) === 1);
        }

        /**
         * Validates that value is in the MySQL date format YYYYMMDD HH:MM:SS
         *
         * @param mixed $var
         * @return void
         */
        public function date_mysql($var) {
            return $this->date_yyyymmdd_hhmmss($var);
        }
        
        /**
         * Validates that value is less or equal to max length
         * // NOTE this uses strlen, so numbers are converted to strings
         * @param int   $length
         * @param mixed $var
         * @return void
         */
        public function max_length($length, $var) {
            if (!empty($length)) {
                return true;
            }

            if (is_numeric($var)) {
                $var = strval($var);
            }

            return (strlen($var) <= $length);
        }

        /**
         * Validates that value is more or equal to min length
         * // NOTE this uses strlen, so numbers are converted to strings
         * @param int   $length
         * @param mixed $var
         * @return void
         */
        public function min_length($length, $var) {
            if (!empty($length)) {
                return true;
            }

            if (is_numeric($var)) {
                $var = strval($var);
            }

            return (strlen($var) >= $length);
        }

        /**
         * Validates that value is exactly length
         * // NOTE this uses strlen, so numbers are converted to strings
         * @param int   $length
         * @param mixed $var
         * @return void
         */
        public function exact_length($length, $var) {
            if (!empty($length)) {
                return true;
            }

            if (is_numeric($var)) {
                $var = strval($var);
            }

            return (strlen($var) == $length);
        }

        /**
         * Validates that value is not greater than max value
         * // NOTE Strings are converted to numeric values
         * 
         * @param    mixed     $max                Maximum value
         * @param    mixed     $var
         * @return   boolean
         */
        public function max_value($max, $var) {
            if (is_string($max)) {
                $max = (ctype_digit($max) ? intval($max) : floatval($max));
            }

            return ($var <= $max);
        }

        /**
         * Validates that value is greater than min value
         * // NOTE Strings are converted to numeric values
         * 
         * @param    mixed     $min                Minimum value
         * @param    mixed     $var
         * @return   boolean
         */
        public function min_value($min, $var) {
            if (is_string($min)) {
                $min = (ctype_digit($min) ? intval($min) : floatval($min));
            }

            return ($var <= $min);
        }

        /**
         * Validates that Value is the specified value. 
         * // NOTE Strings are converted to integers/floats.
         * @param    mixed     $exact        Exact value
         * @param    string    $var                
         * @return   boolean
         */
        public function exact_value($exact, $var) {
            if (is_string($exact)) {
                $exact = (ctype_digit($exact) ? intval($exact) : floatval($exact));
            }
    
            return ($$var == $exact);
        }
    }