<?php
	/**
	 * Functions that let the traited class have abilities to make arrays of an instance or
	 * an array of all the properties
	 */
    trait CreateClassArrayTraits {
		/**
		 * Returns an empty array with indexes that are the properties of the class with this trait
		 * @return array 1 dimensional array
		 */
        public static function generate_classarray() {
            $class = get_called_class();
 			return $class::remove_nondbkeys(get_class_vars($class));
 		}
 		
		/**
		 * Returns a Key-Value array of this object, but it removes the non-databased indexes
		 * @return array key value array, with properties as keys
		 */
        public function _toArray() {
			return $this::remove_nondbkeys(get_object_vars($this));
 		}
    }
