<?php 
	namespace Dplus\Base;
	
	/**
	 * Traits that provide Magic Methods
	 * Functions include __get(), __isset()
	 */
	trait MagicMethodTraits {
		/**
		 * Properties are protected from modification without function, but
		 * We want to allow the property values to be accessed
		 * @param  string $property The property trying to be accessed
		 * @return mixed		   Property value or Error
		 */
		 public function __get($property) {
			$method = "get_{$property}";
			if (method_exists($this, $method)) {
				return $this->$method();
			} elseif (property_exists($this, $property)) {
				return $this->$property;
			} elseif ((isset($this->fieldaliases))) {
				if (array_key_exists($property, $this->fieldaliases)) {
					$realproperty = $this->fieldaliases[$property];
					return $this->$realproperty;
				} else {
					$this->error("This property and alias ($property) does not exist");
					return false;
				 }
			} else {
				$this->error("This property and alias ($property) does not exist");
				return false;
			}
		}
		 
		 /**
		 * Is used to PHP functions like isset() and empty() get access and see
		 * if variable is set
		 * @param  string  $property Property Name
		 * @return bool		   Whether Property is set
		 */
		public function __isset($property){
			return isset($this->$property);
		} 
		
		
		/**
		 * We don't want to allow direct modification of properties so we have this function
		 * look for if property exists then if it does it will set the value for the property
		 * @param string $property Property Name
		 * @param mixed $value    Value for Property
		 */
		 public function set($property, $value) {
 			if (property_exists($this, $property)) {
                 $this->$property = $value;
             } elseif (isset($this->fieldaliases)) {
				 if (array_key_exists($property, $this->fieldaliases)) {
					 $realproperty = $this->fieldaliases[$property];
	                 $this->$realproperty = $value;
				 } else {
	                 $this->error("This property or alias ($property) does not exist");
	                 return false;
	             }
             } else {
                 $this->error("This property or alias ($property) does not exist");
                 return false;
             }
 		}
	}
	
	
	
	
