<?php

// Worth looking at: http://pixel-apes.com/safehtml/ I use a slightly different approach. I am only interested in white listed tags everything else is rejected.


class filter_bv
{

	var $text;
	
	var $mArrWhiteTags;
	var $mArrWhiteAttributes;
	
	var $mCurrentTag; // Holds the current tag being processed.
	var $mArrCleanAttributes;
	
	Function filter_bv()
	{
		// TODO we need to decode the text first
		
		$this->mArrWhiteTags = array('div', 'b', 'i', 'code', 'p', 'u', 'a', 'br', 'ul', 'li', 'img');
		
		$this->mArrWhiteAttributes = array('a'=>array('href'), 'img'=>array('src'));
		
		$this->mArrCleanAttributes = array(); // Initialise. Conainer for clean attributes.
		$GLOBALS['mArrCleanAttributes'] = array(); // For some reason we have to use the global scope with PHP4.
	}
	
	/**
	  *
	  *
	  **/
	Function CleanUp($text){
		
		//-----------------------
		// Taken from safehtml.php http://pixel-apes.com/safehtml/
		 //Copyright  2004-2005 Roman Ivanov <thingol@mail.ru> BSD license
		
		// Web documents shouldn't contains \x00 symbol
		$text = str_replace("\x00", '', $text);
		
		// Opera6 bug workaround
		$text = str_replace("\xC0\xBC", '&lt;', $text);
		
		// UTF-7 encoding ASCII decode
		$text = $this->repackUTF7($text);
		//-----------------------
		
		$text = $this->Decode($text);
		
		// Look for tags
		$text = preg_replace_callback('!<((?:/ | [a-zA-Z])[\w]*)(( "[^"]*" | \'[^\']*\' | [^>])*)>!x', array($this, 'CallbackCleanUpTags'), $text);
		
		return $text;
	}
	
	/**
	  * From daniel at brightbyte dot de, on the PHP manual "html_entity_decode" page.
	  *
	  **/
	function Decode($text) {
	   $text= html_entity_decode($text, ENT_QUOTES, 'ISO-8859-1'); // NOTE: UTF-8 does not work!
	   $text= preg_replace('/&#(\d+);/me', "chr(\\1)", $text); // decimal notation
	   $text= preg_replace('/&#x([a-f0-9]+);/mei', "chr(0x\\1)", $text);  // hex notation
	   return $text;
	}
	
	/**
	  *
	  *
	  **/
	Function CallbackCleanUpTags($anArrMatches)
	{
		$tag = ltrim($anArrMatches[1], '/');
		
		$attributes = ''; // Initialise
		
		if (isset($anArrMatches[2])){ // We have attributes
			
			$this->mCurrentTag = $tag; // We may need this in the CallbackCleanUpAttributes function
			
			$attributes = trim($anArrMatches[2]);
			
			// Only process attributes that have an equal sign.
			preg_replace_callback('/([^ \s]+) = ( "[^"]*" | \'[^\']*\' | [^"\' ]+ )/x', array($this, 'CallbackCleanUpAttributes') , $attributes);
			
		}
		
		if (!in_array(strtolower($tag), $this->mArrWhiteTags))
		{
			return '';
		}
		else{
			// $strAttribute = implode('', $this->mArrCleanAttributes);
			$strAttribute = implode('', $GLOBALS['mArrCleanAttributes']);
			
			$this->mArrCleanAttributes = array(); // Purge
			$GLOBALS['mArrCleanAttributes'] = array();
			
			return '<'.$anArrMatches[1].$strAttribute.'>'; // Purge
		}
	}
	
	/**
	  *
	  *
	  **/
	Function CallbackCleanUpAttributes($anArrMatches) 
	{		
		$attribute = strtolower($anArrMatches[1]);
		$value = trim($anArrMatches[2], '\'"'); // Clean up the leading and trailing " or '
		
		// If the tag entry exists and the attribute matches the white list
		if (isset($this->mArrWhiteAttributes[$this->mCurrentTag]) && in_array($attribute, $this->mArrWhiteAttributes[$this->mCurrentTag]))
		{
			if ($attribute === 'href'){ // only accept http links.	
				if (preg_match('!^http://!', $value)){// must start with http
					$this->mArrCleanAttributes[] = " $attribute=\"$value\"";
					$GLOBALS['mArrCleanAttributes'][] = " $attribute=\"$value\"";
				}
				else if (preg_match('|^[\d\w.]+$|', $value)){// or must only contain alpha num and dot
					$this->mArrCleanAttributes[] = " $attribute=\"$value\"";
					$GLOBALS['mArrCleanAttributes'][] = " $attribute=\"$value\"";
				}
			}
			if ($attribute === 'src'){
				if (preg_match('!^/[^:]!', $value)){// must start with a forward slash and contain no :
					$this->mArrCleanAttributes[] = " $attribute=\"$value\"";
					$GLOBALS['mArrCleanAttributes'][] = " $attribute=\"$value\"";
				}
			}
			else {
				$this->mArrCleanAttributes[] = " $attribute=\"$value\"";
			}
		}
		
	}
	
	
	/**
	 * UTF-7 decoding fuction
	 * Copyright  2004-2005 Roman Ivanov <thingol@mail.ru> BSD license
	 *
	 * @param string $str HTML document for recode ASCII part of UTF-7 back to ASCII
	 * @return string Decoded document
	 * @access private
	 */
	function repackUTF7($str)
	{
		return preg_replace_callback('!\+([0-9a-zA-Z/]+)\-!', array($this, 'repackUTF7Callback'), $str);
	}
	
	/**
	 * Additional UTF-7 decoding fuction
	 * Copyright  2004-2005 Roman Ivanov <thingol@mail.ru> BSD license
	 *
	 * @param string $str String for recode ASCII part of UTF-7 back to ASCII
	 * @return string Recoded string
	 * @access private
	 */
	function repackUTF7Callback($str)
	{
		$str = base64_decode($str[1]);
		$str = preg_replace_callback('/^((?:\x00.)*)((?:[^\x00].)+)/', array($this, 'repackUTF7Back'), $str);
		return preg_replace('/\x00(.)/', '$1', $str);
	}
	
	/**
	* Additional UTF-7 encoding fuction
	*
	* @param string $str String for recode ASCII part of UTF-7 back to ASCII
	* @return string Recoded string
	* @access private
	*/
	function repackUTF7Back($str)
	{
		return $str[1].'+'.rtrim(base64_encode($str[2]), '=').'-';
	}
	
	/**
	  *
	  *
	  **/
	Function PrintFilter()
	{
		return $this->text;
	}


}
