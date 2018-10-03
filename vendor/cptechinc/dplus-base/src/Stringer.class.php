<?php
	class StringerBell {
		/**
		 * Checks to see if string is in a phone format
		 * @param  string $string
		 * @return bool
		 */
		public function does_matchphone($string) {
			$regex = "/\d{3}-?\d{3}-?\d{4}/i";
			return preg_match($regex, $string) ? true : false;
		}

		/**
		 * Returns the ordinal for the number provided
		 * @param  int $number Number to get ordinal for e.g. 10
		 * @return string      Number with ordinal e.g 10th
		 */
		public function generate_ordinal($number) {
			$ends = array('th','st','nd','rd','th','th','th','th','th','th');

			if ((($number % 100) >= 11) && (($number%100) <= 13)) {
				return $number. 'th';
			} else {
				return $number. $ends[$number % 10];
			}
		}

		/**
		 * Returns string with a s for plural if the qty is more than 1
		 * @param  string $qty  Quantity
		 * @param  string $word Word to append s to if need be
		 * @return string       Word with appended S
		 */
		public function generate_plural($qty, $word = '') {
			return $qty > 1 ? $word.'s' : $word.'';
		}
		/* =============================================================
			STRING FORMATTING FUNCTIONS
		============================================================ */
		/**
		 * Takes a String and formats into the phone format
		 * @param  string $string
		 * @return string formatted phone string
		 */
		public function format_phone($string) {
			$string = str_replace(array('-',' '), array('', ''), $string);
			return sprintf("%s-%s-%s",
			  substr($string, 0, 3),
			  substr($string, 3, 3),
			  substr($string, 6));
		}

		/**
		 * Formats string to follow currency convention of XX.XX
		 * @param  string $amt The string to convert
		 * @return float      Amount formatted
		 */
		public function format_money($amt) {
			return number_format($amt, 2, '.', ',');
		}

		/**
		 * Takes string and gives it a span of highlight to give it a highlighted look on the page
		 * @param  string $haystack the string to look through
		 * @param  string $needle   the word to look for
		 * @return string           html string with the $needle highlighted or returns just the string
		 */
		public function highlight($haystack, $needle) {
			$bootstrap = new HTMLWriter();
			if ($this->does_matchphone($haystack)) {
				$needle = $this->does_matchphone($needle);
			}
			$regex = "/(".str_replace('-', '\-?', $needle).")/i";
			$contains = preg_match($regex, $haystack, $matches);

			if ($contains) {
				$highlight = $bootstrap->span('class=highlight', $matches[0]);
				return preg_replace($regex, $highlight, $haystack);
			} else {
				return $haystack;
			}
		}

		/* =============================================================
			STRING CONVERSION FUNCTIONS
		============================================================= */
		/**
		 * Convert string to hexadecimal
		 * @param  string $string String to convert
		 * @return string         Hexadecimal string
		 */
		public function convert_strtohex($string){
			$hex = '';
			for ($i = 0; $i<strlen($string); $i++){
				$ord = ord($string[$i]);
				$hexCode = dechex($ord);
				$hex .= substr('0'.$hexCode, -2);
			}
			return strtoupper($hex);
		}

		/**
		 * Convert Hexadecimal to string
		 * @param  string $hex Hexadecimal String
		 * @return string      Converted from hex string
		 */
		public function convert_hextostr($hex){
			$string = '';
			for ($i = 0; $i < strlen($hex)-1; $i+=2){
				$string .= chr(hexdec($hex[$i].$hex[$i+1]));
			}
			return $string;
		}

		/**
		 * Converts Latin Characters into a more utf8 friendly string
		 * @param  string $string String to go through and convert characters
		 * @return string         Converted string
		 */
		public function convert_latintoutf($string) {
			$encode = array("â€¢" => '&bull;', "â„¢" => '&trade;', "â€" => '&prime;');
			foreach ($encode as $key => $value) {
				if (strpos($string, $key) !== false) {
					$string = str_replace($key, $value, $string);
				}
			}
			return $string;
		}

		/**
		 * Formats string for us in javascript
		 * main use is for itemids that have certain restricted characters.
		 * @param  string $str String to be converted
		 * @return string      removed unusable characters for javascripted and urlencoded
		 */
		public function format_forjs($str) {
			$replace = array(
				' ' => '-',
				'#' => ''
			);
			return urlencode(str_replace(array_keys($replace), array_values($replace), $str));
		}
	}
