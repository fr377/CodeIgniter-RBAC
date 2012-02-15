<?php
/**
  * Template_bv is a minimalist templating engine that is extremely FAST,
  * does NOT use regular expressions and can easily be extended.
  *
  * Template does not have the problems most regular expression based templating engines suffer from, namely:
  * + Parsing the HTML document as many times as there are tokens which can be slow if there are many tokens
  *   and the content of each token is large. (As the document 'grows' the time it takes to parse a file increases.)
  * + Possibility of token name collisions if the content of one token has the name of another token in it.
  *
  * The way the template works is that it 'explodes' the template file and stores the pieces in an array.
  * New content is then assigned to the token names, which is stored in a container. Finally the pieces are 
  * put back together and returned.
  *
  * This template would suite someone who likes to embed HTML *inside* of PHP and not the other way around.
  *
  * NOTE: In order to keep things as simple as possible it is best to surround the tokens with < and >, so 
  *       that if no content is assigned to them they will not show up in your HTML document. You could also
  *       add a method to check for tokens but that would require a regular expression. Or alternatively, define
  *       and set the tokens manually so you know exactly what they are.
  *
  * USAGE: Please see the attached example, example.php.
  *
  * THANKS: I have been using Ben Yacoub Hatem's minitpl engine for a long time now and would like to thank him
  *         for his (indirect) contribution.
  *
  * The coding standards used in this file can be found here: http://www.dagbladet.no/development/phpcodingstandard/
  *
  *	All commets and suggestions welcome. Pleaes visit http://www.vhd.com.au/forum for support.
  *
  * CHANGES:
  * 1.0.1 (2006-02-14): - Changed the method name of Output() to PrintTemplate();
  * 1.1 (2006-02-25):	- Changed class name to Template_bv. (Used to be Template)
  *									- Added new method Enclose() which will simultaneously add content before and after a token
  *									- Changed the PrintTemplate() method to allow multiple instances of the same token to be replaced
  *									- Fixed the error message to actually see what tokens have not been replaced
  * 1.1.1 (2006-03-13): - Added the ClearContainer() method.
  *
  * @version 1.1.1 (2006-02-25)
  * @author Ben Vautier <classes@vhd.com.au>
  * @copyright Copyright (c) 2006
  * @license BSD or LGPL
  * @access public
  *
  **/
class Template_bv
{
	var $mTemplateName; // Template name including path.
	var $mTemplateContent; // Original template content as a long string.
	var $mArrTemplateContent; // Template content exploded and stored in an array.
	var $mArrContainer; // Container for token content (set by user.)
	var $mDelimiter; // Delimiter used to explode the template file. Default [#]
	
	/**
	  * Constructor
	  *
	  * Can either accept a file path or a string. If we already have the template
	  * content as a string set $is_string to TRUE
	  *
	  * @access public
	  * @param str $tpl filename path
	  * @param bool $is_string default FALSE
	  **/
	Function Template_bv($tpl, $is_string = FALSE){
		
		if ($tpl == '' && !$is_string){
			trigger_error('Please specify a template file to use.', E_USER_ERROR);
			return;
		}
		
		// Initialise
		$this->mDelimiter = '[#]'; // Set this to anything you want.
		$this->mArrContainer = array();
		
		if ($is_string){
			$this->mTemplateContent = $tpl;
		}
		else {
			$this->mTemplateName = $tpl;
			
			if (!$this->LoadTemplate()){
				return;
			}
		}
		
		$this->ParseTemplate();
	}
	
	/**
	  * Template_bv::LoadTemplate load the content of the template into a container.
	  *
	  * Will trigger an error if the template cannot be opened.
	  *
	  * @access public
	  **/
	Function LoadTemplate(){		
		
		if ($_hd = fopen($this->mTemplateName, 'r')) {
			$this->mTemplateContent = fread($_hd, filesize($this->mTemplateName));
			fclose($_hd);
			
			return TRUE;
		}
		else {
			trigger_error('The template file '.$this->mTemplateName.' could not be opened.', E_USER_ERROR);
			
			return FALSE;
		}
	}
	
	/**
	  * Template_bv::ParseTemplate will explode the template using the delimiter and store the pieces in an array
	  *
	  * @access public
	  **/
	Function ParseTemplate(){
		$this->mArrTemplateContent = explode($this->mDelimiter, $this->mTemplateContent);
	}
	
	/**
	  * Template_bv::Assign will assign some content to a token.
	  *
	  * @access public
	  * @param str $token is the token name
	  * @param str $content
	  **/
	Function Assign($token, $content){
		$this->mArrContainer[$token] = $content;
	}
	
	/**
	  * Template_bv::Append will append content to the end of an existing token, otherwise it will create a new entry.
	  *
	  * @access public
	  * @param str $token is the token name
	  * @param str $content
	  **/
	Function Append($token, $content){
		if (isset($this->mArrContainer[$token])){
			$this->mArrContainer[$token] = $this->mArrContainer[$token] . $content;
		}
		else {
			$this->Assign($token, $content);
		}
	}
	
	/**
	  * Template_bv::Prepend will prepend content to the beginning of an existing token, otherwise it will create a new entry.
	  *
	  * @access public
	  * @param str $token is the token name
	  * @param str $content
	  **/
	Function Prepend($token, $content){
		if (isset($this->mArrContainer[$token])){
			$this->mArrContainer[$token] = $content . $this->mArrContainer[$token];
		}
		else {
			$this->Assign($token, $content);
		}
	}
	
	/**
	  * Template_bv::Enclose will put information before and after the given token.
	  *
	  * @access public
	  * @param str $token is the token name
	  * @param str $before
	  * @param str $after
	  **/
	Function Enclose($token, $before, $after){
		$this->Prepend($token, $before);
		$this->Append($token, $after);
	}
	
	/**
	  * Template_bv::Output Merge the token container with the template and return completed page.
	  *
	  * @access public
	  **/
	Function PrintTemplate(){
		
		$_html = '';
		$arr_token_names = array(); // Store the token names that have been successfully substituted
		
		foreach ($this->mArrTemplateContent as $_tokent){ // tokent because it can be either a token or content
			if (isset($this->mArrContainer[$_tokent])){
				$_html .= $this->mArrContainer[$_tokent];
				$arr_token_names[] = $_tokent;
			}
			else {
				$_html .= $_tokent;
			}
		}
		
		// Simple error check. If a token in the container was not assigned it will throw a notice.
		// Generally this means that you assigned content to token that does not exist in your template or has been misspelled.
		$arr_diff = array_diff(array_keys($this->mArrContainer), $arr_token_names); // Will tell us if there are tokens that have not been substituted.
		if (!empty($arr_diff )){
			$left_token = "'".implode("', '", $arr_diff)."'";
			//trigger_error('The following tokens have not been assigned: '.htmlentities($left_token).'. Make sure these tokens are defined in the template file, and that there are no spelling mistakes.', E_USER_NOTICE);
		}
		
		return $_html;
	}
	
	/**
	  * Template_bv::ClearContainer will empty the container
	  *
	  * @access public
	  **/
	Function ClearContainer(){
		$this->mArrContainer = array();
	}
}
?>
