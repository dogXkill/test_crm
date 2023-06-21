<?php

	class DatetimeHelper {

		public static function getRusMonth($month) {
			
			$months = array(
				1 => 'января',
				2 => 'февраля',
				3 => 'марта',
				4 => 'апреля',
				5 => 'мая',
				6 => 'июня',
				7 => 'июля',
				8 => 'августа',
				9 => 'сентября',
				10 => 'октября',
				11 => 'ноября',
				12 => 'декабря'
			);
			
			return $months[intval($month)];
		}
		
		public static function format($datetime, $format) {
			return date($format, strtotime($datetime));
		}
		
	}


