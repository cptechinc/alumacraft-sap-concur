<?php
	namespace Dplus\Base;
	
	use ProcessWire\WireInput;
	/**
	 * Functions that allow filtering of data through Arrays
	 */
	trait Filterable {
		/**
		 * Looks through the $input->get for properties that have the same name
		 * as filterable properties, then we populate $this->filter with the key and value
		 * @param  WireInput $input Use the get property to get at the $_GET[] variables
		 */
		public function generate_filter(WireInput $input) {
			if (!$input->get->filter) {
				$this->filters = false;
			} else {
				$this->filters = array();
				foreach ($this->filterable as $filter => $type) {
					if (!empty($input->get->$filter)) { // IF WE HAVE FILTERS IN THE GET ARRAY
						if (!is_array($input->get->$filter)) {
							$value = $input->get->text($filter);
							$this->filters[$filter] = explode('|', $value);
						} else {
							if (strlen($input->get->$filter[0])) {
								for ($i = 0; $i < sizeof($input->get->$filter); $i++) {
									if (empty($input->get->$filter[$i])) {
										unset($input->get->$filter[$i]);
									}
								}
								$this->filters[$filter] = $input->get->$filter;
							}
						}
					}
				}
			}
		}


		/**
		 * Looks through the $input->get for properties that have the same name
		 * as filterable properties, then we populate $this->filter with the key and value
		 * @param  WireInput $input Use the get property to get at the $_GET[] variables
		 */
		public function generate_defaultfilter(WireInput $input) {
			if (!$input->get->filter) {
				$this->filters = false;
			} else {
				$this->filters = array();
				foreach ($this->filterable as $filter => $type) {
					if (!empty($input->get->$filter)) { // IF WE HAVE FILTERS IN THE GET ARRAY
						if (!is_array($input->get->$filter)) {
							$value = $input->get->text($filter);
							$this->filters[$filter] = explode('|', $value);
						} else {
							if (strlen($input->get->$filter[0])) {
								for ($i = 0; $i < sizeof($input->get->$filter); $i++) {
									if (empty($input->get->$filter[$i])) {
										unset($input->get->$filter[$i]);
									}
								}
								$this->filters[$filter] = $input->get->$filter;
							}
						}
					}
				}
			}
		}

		/**
		 * Grab the value of the filter at index
		 * Goes through the $this->filters array, looks at index $filtername
		 * grabs the value at index provided
		 * @param  string $key        Key in filters
		 * @param  int    $index      Which index to look at for value
		 * @return mixed              value of key index
		 */
		public function get_filtervalue($key, $index = 0) {
			if (empty($this->filters)) return '';
			if (isset($this->filters[$key])) {
				return (isset($this->filters[$key][$index])) ? $this->filters[$key][$index] : '';
			}
			return '';
		}

		/**
		 * Checks if $this->filters has value of $value
		 * @param  string $key        string
		 * @param  mixed $value       value to look for
		 * @return bool               whether or not if value is in the filters array at the key $key
		 */
		public function has_filtervalue($key, $value) {
			if (empty($this->filters)) return false;
			return (isset($this->filters[$key])) ? in_array($value, $this->filters[$key]) : false;
		}
	}
