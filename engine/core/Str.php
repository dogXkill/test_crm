<?php
	
	final class Str {
		
		private static $loadedStrings = array();
		
		public static function get($string, $params = array(), $to_utf8 = false) {
			if (strpos($string, '/') === false) {
				return '';
			}
			
			list($cat, $index) = explode('/', $string);
			
			if (isset(self::$loadedStrings[$cat])) {
				$strings = self::$loadedStrings[$cat];
			} else {
				$file = STRINGSPATH . '/' . $cat . '.php';
				
				$strings = array();
				
				if (file_exists($file)) {
					$strings = require_once($file);
				}
				
				self::$loadedStrings[$cat] = $strings;
			}
			
			if (isset($strings[$index])) {
				if ($params != false) {
					$return = str_replace(array_keys($params), array_values($params), $strings[$index]); 
				} else {
					$return = $strings[$index];
				}
				
				return $to_utf8 ? iconv("Windows-1251", "UTF-8//IGNORE", $return) : $return; 
			}
			
			return '';
		}
	}