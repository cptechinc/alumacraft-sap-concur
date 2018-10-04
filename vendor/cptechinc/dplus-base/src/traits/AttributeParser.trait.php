<?php
    namespace Dplus\Base;
    
	/**
	 * Functions that parse attributes for html
	 */
    trait AttributeParser {
        /**
         * Takes a string of attributes and parses it out by a delimiter (|)
         * @param  string $vars string of attributes separated by | 
         * @return string       string of atrributes and values like class=""
         */
        protected function attributes($vars) {
            $attributesarray = array();
            $attributes = '';
            
            if (!empty($vars)) {
                $values = explode('|', $vars);
                foreach ($values as $value) {
                    $pieces = explode('=', $value);
                    $attributesarray[array_shift($pieces)] = implode('=', $pieces);
                }
            }
            
            if (!empty($attributesarray)) {
                foreach ($attributesarray as $key => $value) {
                    if ($value == 'noparam') {
                        $attributes .= ' ' . $key;
                    } else {
                        $attributes .= ' ' . $key . '="' . $value . '"';
                    }
                }
            }
            return $attributes;
        }
		
		/**
         * Takes a string of attributes and parses it out by a delimiter (|)
         * @param  string $attributes string of attributes separated by | 
         * @return string             string of atrributes and values like class=""
         * @uses attributes()
         */
		protected function generate_attributes($attributes) {
			return $this->attributes($attributes);
		}
		
		/** 
		 * Takes the $this->ajaxdata string and formats it for contento
		 * @return string pipe delimited representation of the ajax data ex data-focus=#this|data-loadinto=
		 */
		protected function generate_ajaxdataforcontento() {
			return str_replace(' ', '|', str_replace("'", "", str_replace('"', '', $this->ajaxdata)));
		}
		
		/**
		 * Takes in $ajaxdata and parses it based on type
		 * @param  mixed $ajaxdata Array or String
		 * @return string          Parsed ajaxdata
		 * @uses
		 */
		protected function parse_ajaxdata($ajaxdata) {
			switch (gettype($ajaxdata)) {
				case 'array':
					$attributes = implode('|', $ajaxdata);
					return $this->attributes($attributes);
					break;
				case 'string':
					return (strpos($ajaxdata, '|') !== false) ? $this->attributes($ajaxdata) : $ajaxdata;
					break;
			}
		}
    }
