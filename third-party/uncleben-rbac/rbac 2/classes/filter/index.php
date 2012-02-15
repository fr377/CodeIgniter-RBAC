
<?php
// I think we have to do this in two passes. First get all the tag element. then send it to a callback function
// The call back function will then look at the tag, if it is allowed, look at the attributes and see if they are allowed.

//$_POST['regex'] = addslashes('!<([A-Z]\w*) (?:\s* (?:\w+) \s* = \s* (?(?=["\']) (["\'])(?:.*?\2)+ | (?:[^\s>]*) ) )* \s* (\s/)? >!ix');

// !<([A-Z]\w*) (?:\s* (\w+) \s* =? \s* (?(?=["']) (["'])(.*?)\3+ | (?<=[=])(.*?(?:\s|'|"|(?=[>])))   ) )* \s* (\s/)? >!ix

//!<([A-Z]\w*) \s* (.+(?(?=["']) (["'])(.*?) | (.*?)))!ixs

// This is good:
//!<([A-Z]\w*) ((?:\s* (?:\w+) \s* =? \s* (?(?=["']) (["'])(?:.*?\3)+ | (?:[^\s>]) ) )* \s* (\s/)? )>!ixs
?>

<form action="" method="POST">
<table>
	<tr>
		<td>Type in your regular expression here
	<tr>
		<td><textarea cols="120" rows="2" name="regex"><?php isset($_POST['regex']) ? print(stripslashes($_POST['regex']) ): ''; ?></textarea>
	<tr>
		<td>Replace with
	<tr>
		<td><INPUT type="text" name="replace" value="<?php isset($_POST['replace']) ? print(stripslashes($_POST['replace'])) : '';?>">
	<tr>
		<td>Type in your text here
	<tr>
		<td><textarea cols="120" rows="5" name="text"><?php isset($_POST['text']) ? print(stripslashes($_POST['text'])) : '';?></textarea>
	<tr>
		<td align="center"><input type="submit" name="submit" value="Go">
</table>
</form>

<?php

Function print_tag($match){
	//print_r($match);
	
	if (isset($match[1])){
		return str_replace('>', '', $match[0]);
	}
	else {
		return $match[0];
	}
	//print("The tag is {$match[1]} | {$match[2]} <br>\n");
	
	//preg_replace_callback( '!(.*?)=(\s*.*?)(\s|$)!ix' , 'print_attributes', $match[2]);
	
	// Find a closing bracket within quotes !(?(?=['"]) (['"]).*?\1 | .*?(?=['"]))!ix
}

Function print_attributes($match){
	//print_r($match);
	print(" - - The attributes are {$match[1]} =={$match[2]}<br>\n");
	
}


if (isset($_POST['submit'])){
	
	$_html = preg_replace_callback(stripslashes($_POST['regex']), 'print_tag', stripslashes($_POST['text']));
	
	//$_html = preg_replace_callback(stripslashes($_POST['regex']), 'print_out',stripslashes($_POST['replace']), stripslashes($_POST['text']));
	
	echo '<textarea cols="40" rows="5" >'.$_html.'</textarea>';
}


?>
