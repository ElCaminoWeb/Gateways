<?php
//if they DID upload a file...
if(isset($_FILES['photo'])){
$target = "img/" . time() . "." . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION) ;
//print_r($_FILES);

if(move_uploaded_file($_FILES['photo']['tmp_name'],$target)) echo "OK!";//$chmod o+rw galleries

}

?>
