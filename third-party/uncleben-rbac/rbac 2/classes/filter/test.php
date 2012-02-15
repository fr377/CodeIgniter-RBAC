<?php


class test
{
	var $mCount;
	
	Function test()
	{
		$test = '<div> Hello " there "is "ben"</div>';
		
		$test = preg_replace_callback('/"/', array($this, 'replaceme'), $test);
		
		echo $test;
	}
	
	Function replaceme($matches)
	{
		
		$this->mCount++;
		
		echo "This is the $this->mCount found<br>";
	}


}


// NOTE: strip_tags() does not deal properly with > in quotes. So it is better not to use it.


include_once 'class.filter_bv.php';

$text = '
<div this should stay style="n>ice"> This is a test </div><br><span test="by>e" hello=\'th>ere\'>
<i> This tag should <a href="article_2.html">bye</a> be deleted</i><br />
<b hello=there nice="
try">This one < will be kept<br>
<div style="tes><t">
';

$filter = new filter_bv();

echo $filter->cleanUp($text);

//echo strip_tags($text); // , '<div><i><br>'
