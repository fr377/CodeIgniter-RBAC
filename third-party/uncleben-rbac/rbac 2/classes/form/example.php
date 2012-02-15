<form enctype="multipart/form-data" method="post" action="">
<input type="file" size="32" name="image_field" value="">
<input type="submit" name="Submit" value="upload">
</form>

<?php

include_once 'class.upload.php';

if (isset($_POST['Submit'])){
	print_r($_FILES);
	
	
	$MyObject = new upload($_FILES['image_field']);
	
   if ($MyObject->uploaded) {
       $MyObject->file_new_name_body   = 'image_resized';
       $MyObject->image_resize         = true;
       $MyObject->image_x              = 200;
       $MyObject->image_ratio_y        = true;
       $MyObject->process('/var/www/classes/form/uploads/');
       if ($MyObject->processed) {
           echo $MyObject->log;
           $MyObject->clean();
       } else {
           echo 'error : ' . $MyObject->error;
       }
   }
}



