<?php
	/**
	 * Class That converts dates and times and defaults to converting Dates and Times from the DPlus Cobol System
	 */
	class DplusDateTime {
		static $defaultdate = 'm/d/Y';
		static $defaulttime = 'h:i A';
		static $fulltimestring = 'YmdHisu';
		static $shorttimestring = 'Hi';

	/**
	 * [format_dplustime description]
	 * @param  string $time          time string ex 16063372
	 * @param  string $currentformat format the time is in, default is Hi
	 * @param  string $desiredformat desired format, default is h:i A -> 10:19 AM
	 * @return string                Time Formatted
	 */
		public static function format_dplustime($time, $currentformat = 'Hi', $desiredformat = 'h:i A') {
			$formatted = DateTime::createFromFormat($currentformat, $time);
			return $formatted->format($desiredformat);
		}

		/**
		 * Formats Date
		 * @param  string $date         date in whatever format provided
		 * @param  string $formatstring the format in which time should be formatted to defualt is m/d/Y
		 * @return string               formatted result
		 */
		public static function format_date($date, $formatstring = 'm/d/Y') {
			return (strtotime($date) == strtotime("12/31/1969") || strtotime($date) == strtotime("0000-00-00 00:00:00")) ? '' : date($formatstring, strtotime($date));
		}

		/**
		 * Subtract two dates from each other
		 * after converting them into timestring
		 * then returing the number of days
		 * @param  string $fromdate ex. 01/25/2018
		 * @param  string $todate   ex. 01/30/2018
		 * @return int           	Number of days ex. 5
		 */
		public static function subtract_days($fromdate, $todate) {
			$from = strtotime($fromdate);
			$through = strtotime($todate);
			return floor(($through - $from) / (60*60*24));
		}
	}
