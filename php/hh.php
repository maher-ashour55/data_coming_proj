<?php
$new_password = "maherashour";  // كلمة السر الجديدة اللي بدك تحطها
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
echo $hashed_password;
?>
