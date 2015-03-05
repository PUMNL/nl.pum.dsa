<?php

/**
 * Class with general functions to clean and resize a string
 *
 * @author Ralph Kersten
 * @license AGPL-V3.0
 *
 * examples:
 * $filter = CRM_Dsa_CharFilter::singleton();
 * $result = $filter->filteredResize('This! ís @á dèmöñstratiôn', 14);           // This is a demo
 * $result = $filter->filteredResize('!Kéèp? :ödd@ çhars', 20, 'x', TRUE, TRUE); // !Keep? :odd@ charsxx
 * $result = $filter->filteredResize('13', 5, '0', FALSE);                       // 00013
 * $result = $filter->filteredResize('13', 5, 'x', TRUE);                        // 13xxx
 */
 
class CRM_Dsa_CharFilter {

	protected static $_singleton;
	protected static $_replacements;
	
	protected function __construct() {
		self::_charReplacementBuild();
	}
	
	public static function singleton() {
		if (!self::$_singleton) {
			self::$_singleton = new CRM_Dsa_CharFilter();
		}
		return self::$_singleton;
	}
	
	/*
	 * Function to replace all occurances of "known odd characters" in a string by their "basic" variants
	 * Removes remaining "odd characters" when $skipCharRemoval FALSE or omitted, except 0-9, a-z, A-Z, space, slash and dash
	 */
	public function charFilter($dataStr, $skipCharRemoval=FALSE) {
		// Warning: $data may either grow or shrink in size
		// step 1: substitition of known characters
		$dataStr = str_replace(self::$_replacements['originals'], self::$_replacements['substitutes'], $dataStr); // replace all 'known' special characters by their 'known' replacement strings
		// step 2: removal of all other illegal characters
		if (!$skipCharRemoval) {
			$dataStr = PREG_REPLACE("/[^0-9a-zA-Z \/\-]/i", '', $dataStr); // remove all remaining special characters
		}
		return $dataStr;
	}
	
	/*
	 * Function to resize a value to a certain length for export to a fixed row-size file
	 * contains implicit replacement of "odd" characters
	 * 
	 * parameters:
	 * $value: 	the value to parse into an export line
	 * $size:	the length in which $value should be parsed
	 * $fillChar: the character to use to increase $values length if neccessary
	 * $alignLeft: if TRUE: $fillChar is appended, if FALSE $fillChar is prepended
	 * $skipCharRemoval: if TRUE: no additional removal of "unknown" characters is performed. If FALSE, only certain characters (0-9, a-z, A-Z, space, slash, dash) are returned (by charFilter())
	 */
	public function filteredResize($value='', $size, $fillChar=' ', $alignLeft=TRUE, $skipCharRemoval=FALSE) {	
		// replace odd characters
		$value = self::charFilter($value, $skipCharRemoval);
		// verify or set the desired length of the value
		if(is_numeric($size)) {
			$size = intval($size, 10);
		} else {
			$size = strlen($value);
		}
		// prepare filler string to append to value
		if ($size > strlen($value)) {
			$len = $size - strlen($value);
			$filler = substr(str_repeat($fillChar, $len), 0, $len);
		} else {
			$filler = '';
		}
		// set value to the intended size
		if($alignLeft) {
			$value .= $filler;
		} else {
			$value = $filler . $value;
		}
		$value = substr($value, 0, $size);
		return $value;
	}
	
	/*
	 * Internal function that builds an associative array containing
	 * - an array of special characters (to be replaced)
	 * - an array (same size) of their replacement strings
	 */
	 private function _charReplacementBuild() {
		self::$_replacements = array(
			'originals' => array(),
			'substitutes' => array(),
		);
		self::_charReplacementAdd('áàäãâ',	'a');
		self::_charReplacementAdd('ÁÀÄÃÂ',	'A');
		self::_charReplacementAdd('éèëê',	'e');
		self::_charReplacementAdd('ÉÈËÊ',	'E');
		self::_charReplacementAdd('íìïî',	'i');
		self::_charReplacementAdd('ÍÌÏÎ',	'I');
		self::_charReplacementAdd('óòöõôø',	'o');
		self::_charReplacementAdd('ÓÒÖÕÔØ',	'O');
		self::_charReplacementAdd('úùüû',	'u');
		self::_charReplacementAdd('ÚÙÜÛ',	'U');
		self::_charReplacementAdd('ýÿ',		'y');
		self::_charReplacementAdd('ÝŸ',		'Y');
		self::_charReplacementAdd('æ',		'ae');
		self::_charReplacementAdd('Æ',		'AE');
		self::_charReplacementAdd('ñ',		'n');
		self::_charReplacementAdd('Ñ',		'N');
		self::_charReplacementAdd('ç',		'c');
		self::_charReplacementAdd('Ç',		'C');
	}
	
	/*
	 * Internal helper function for _charReplaceBuild()
	 * Maps each character in $orgChars to the string in $replacementStr
	 */
	private function _charReplacementAdd($orgChars, $replacementStr) {
		for($i=0; $i<mb_strlen($orgChars); $i++) {
			self::$_replacements['originals'][] = mb_substr($orgChars, $i, 1);
			self::$_replacements['substitutes'][] = $replacementStr;
		}
	}
	

	
}