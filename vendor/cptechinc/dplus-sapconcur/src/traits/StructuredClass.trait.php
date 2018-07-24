<?php 
	/**
	 * Adds definition of Properties and functions needed
	 * for endpoints that have defined structures
	 */
	trait StructuredClassTraits {
		
		/**
		 * Returns an array keyed by the keys and values needed for the array 
		 * Takes the array structure, goes through the structure
		 * adds a key value, and uses the dbcolumn property from the structure if needed
		 * @param  array $arraywithvalues Key Value array from the Database
		 * @param  array $section         Section / Key of array to map
		 * @return array                  Array with the needed key values
		 */
		public function map_arraytostructuresection($arraywithvalues, $section) {
			$structuredarray = array();
			
			foreach ($this->structure[$section] as $key => $proparray) {
				if (empty($proparray['dbcolumn'])) {
					$structuredarray[$key] = $arraywithvalues[$key];
				} else {
					$structuredarray[$key] = $arraywithvalues[$proparray['dbcolumn']];
				}
			}
			return $structuredarray;
		}
	}
