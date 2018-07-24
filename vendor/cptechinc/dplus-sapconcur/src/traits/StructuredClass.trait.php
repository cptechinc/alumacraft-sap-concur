<?php 
	/**
	 * Adds definition of Properties and functions needed
	 * for endpoints that have defined structures
	 */
	trait StructuredClass {
		/**
		 * Directory of the JSON structure files for each structure type
		 * @var string
		 */
		protected $structuredirectory =  __DIR__.'/../../structures/';
		
		/**
		 * Array containing the Properties of each column
		 * @var array
		 */
		protected $structure;
		
		/**
		 * Loads the $this->structure with 
		 * an array derived from the JSON file
		 * @return void
		 */
		public function load_structure() {
			$file = $this->structuredirectory."$this->structurefile.json";
			
			if (file_exists($file)) {
				$this->structure = json_decode(file_get_contents($file), true);
				
				if (!$this->structure) {
					$this->error("failed to make structure out of $file");
				}
			} else {
				$this->error("$file does not exist to create json structure");
			}
		}
		
		/**
		 * Returns an array keyed by the keys and values needed for the array 
		 * Takes the array structure, goes through the structure
		 * adds a key value, and uses the dbcolumn property from the structure if needed
		 * @param  array $arraywithvalues Key Value array from the Database
		 * @return array                  Array with the needed key values
		 */
		public function map_arraytostructure($arraywithvalues) {
			$this->load_structure();
			$structuredarray = array();
			
			foreach ($this->structure as $key => $proparray) {
				if (empty($proparray['dbcolumn'])) {
					$structuredarray[$key] = $arraywithvalues[$key];
				} else {
					$structuredarray[$key] = $arraywithvalues[$proparray['dbcolumn']];
				}
			}
			return $structuredarray;
		}
	}
