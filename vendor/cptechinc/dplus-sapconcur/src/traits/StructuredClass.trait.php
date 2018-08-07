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
		 * @param  array $structure       Section / Key of array to map
		 * @param  array $values          Values array
		 * @return array                  Array with the needed key values
		 */
		protected function create_sectionarray($structure, $values) {
			$structuredarray = array();
			
			foreach ($structure as $field => $fieldproperties) {
				if (isset($fieldproperties['dbcolumn'])) {
					$structuredarray[$field] = $this->get_value($values, $field, $fieldproperties);
				} else {
					$structuredarray[$field] = $this->create_sectionarray($structure[$field], $values);
				}
			}
			return $structuredarray;
		}
		
		/**
		 * Determines the Value to get and format
		 * @param  array  $values          Array of Values, Key value array
		 * @param  string $field           Field to find value for
		 * @param  array  $fieldproperties Array of Properties for that field
		 * @return string                  Determined Value, formatted
		 */
		protected function get_value($values, $field, $fieldproperties) {
			$value = '';
			$field = !empty($fieldproperties['dbcolumn']) ? $fieldproperties['dbcolumn'] : $field;
			
            if (isset($fieldproperties['format'])) {
                switch ($fieldproperties['format']) {
                    case 'date':
                        $value = date($fieldproperties['date-format'], strtotime($this->clean_value($values[$field])));
                        break;
                }
            } else {
                $value = $this->clean_value($values[$field]);
            }
			return (empty($value) && isset($fieldproperties['default'])) ? $fieldproperties['default'] : $value;
        }
	}
