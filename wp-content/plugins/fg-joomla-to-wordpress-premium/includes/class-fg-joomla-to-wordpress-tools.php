<?php

/**
 * Useful functions
 *
 * @link       https://www.fredericgilles.net/fg-joomla-to-wordpress/
 * @since      2.0.0
 *
 * @package    FG_Joomla_to_WordPress_Premium
 * @subpackage FG_Joomla_to_WordPress_Premium/includes
 */

/**
 * Useful functions class
 *
 * @since      2.0.0
 * @package    FG_Joomla_to_WordPress_Premium
 * @subpackage FG_Joomla_to_WordPress_Premium/includes
 * @author     Frédéric GILLES
 */

if ( !class_exists('FG_Joomla_to_WordPress_Tools', false) ) {
	class FG_Joomla_to_WordPress_Tools {
		/**
		 * Convert string to latin
		 */
		public static function convert_to_latin($string) {
			$string = self::greek_to_latin($string); // For Greek characters
			$string = self::cyrillic_to_latin($string); // For Cyrillic characters
			return $string;
		}
		
		/**
		 * Convert Greek characters to latin
		 */
		private static function greek_to_latin($string) {
			static $from = array('Α','Ά','Β','Γ','Δ','Ε','Έ','Ζ','Η','Θ','Ι','Κ','Λ','Μ','Ν','Ξ','Ο','Π','Ρ','Σ','Τ','Υ','Φ','Χ','Ψ','Ω','α','ά','β','γ','δ','ε','έ','ζ','η','ή','θ','ι','ί','ϊ','κ','λ','μ','ν','ξ','ο','ό','π','ρ','ς','σ','τ','υ','ύ','φ','χ','ψ','ω','ώ','ϑ','ϒ','ϖ');
			static $to = array('A','A','V','G','D','E','E','Z','I','TH','I','K','L','M','N','X','O','P','R','S','T','Y','F','CH','PS','O','a','a','v','g','d','e','e','z','i','i','th','i','i','i','k','l','m','n','x','o','o','p','r','s','s','t','y','y','f','ch','ps','o','o','th','y','p');
			return str_replace($from, $to, $string);
		}

		/**
		 * Convert Cyrillic (Russian) characters to latin
		 */
		private static function cyrillic_to_latin($string) {
			static $from = array('ж',  'ч',  'щ',   'ш',  'ю',  'а', 'б', 'в', 'г', 'д', 'е', 'з', 'и', 'й', 'к', 'л', 'м', 'н', 'о', 'п', 'р', 'с', 'т', 'у', 'ф', 'х', 'ц', 'ъ', 'ь', 'я', 'Ж',  'Ч',  'Щ',   'Ш',  'Ю',  'А', 'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И', 'Й', 'К', 'Л', 'М', 'Н', 'О', 'П', 'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ц', 'Ъ', 'Ь', 'Я');
			static $to = array('zh', 'ch', 'sht', 'sh', 'yu', 'a', 'b', 'v', 'g', 'd', 'e', 'z', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'r', 's', 't', 'u', 'f', 'h', 'c', 'y', 'x', 'q', 'Zh', 'Ch', 'Sht', 'Sh', 'Yu', 'A', 'B', 'V', 'G', 'D', 'E', 'Z', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'R', 'S', 'T', 'U', 'F', 'H', 'c', 'Y', 'X', 'Q');
			return str_replace($from, $to, $string);
		}

	}
}
