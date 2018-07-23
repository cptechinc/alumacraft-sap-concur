<?php
	/**
	 * Functions that let you create objects from array or from another object
	 * by looping through indexes or properties
	 */
	trait CreateFromObjectArrayTraits {
		/**
		 * Creates an object with the class of this type and fills in the properties from an array
		 * @param  array  $array array that has values that pertain to properties of an object
		 * @return object        Creates an object with the class that has this trait
		 */
		public static function create_fromarray(array $array) {
			$myClass = get_class();
			$object  = new $myClass(); 

			foreach ($array as $key => $val) {
				$object->$key = $val;
			}
			return $object;
		}
		
		/**
		 * Creates an object with the class of this type and fills in the properties from another object
		 * @param  object $object object that has values that pertain to properties of this new object of this class
		 * @return object         Creates an object with the class that has this trait
		 */
		public static function create_fromobject($object) {
			if (!is_object($object)) return false;
			
			if (method_exists ($object, '_toArray')) {
				$properties = $object->_toArray();
			} else {
				$properties = get_class_vars(get_class());
			}
			$myClass = get_class();
			$newobject = new $myClass();
			
			foreach ($properties as $property => $value) {
				$newobject->$property = $object->$property;
			}
			return $newobject;
		}
	}
