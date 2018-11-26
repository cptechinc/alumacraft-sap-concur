<?php 
	namespace Dplus\Base;
	
	/**
	 * Functions that throw, show, or log errors
	 */
    trait ThrowErrorTrait {
		/**
		 * Throws an error to be logged
		 * @param  string $error Description of Error
		 * @param  int $level What PHP Error Level
		 * Error constants can be found at
		 * http://php.net/manual/en/errorfunc.constants.php
		 */
        protected function error($error, $level = E_USER_ERROR) {
            $trace = debug_backtrace();
            $caller = next($trace); 
            $class = get_class($this);
			$error = (strpos($error, "DPLUS [$class]: ") !== 0 ? "DPLUS [$class]: " . $error : $error);
            $error .= PHP_EOL;
			$error .= PHP_EOL;
            
			if (isset($caller['file'])) {
                $error .= $caller['function'] . " called from " . $caller['file'] . " on line " . $caller['line'];
            } else {
                $error .= "Property may be trying to be loaded from database";
            }
			trigger_error($error, $level);
			return;
		}
    }
