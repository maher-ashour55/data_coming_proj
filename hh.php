<?php
$new_password = "maher12345";  // كلمة السر الجديدة اللي بدك تحطها
$hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
echo $hashed_password;
?>
