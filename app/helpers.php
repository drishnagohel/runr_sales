
<?php

//generate accesstoken
if (!function_exists('generateAccessToken')) {

  function generateAccessToken($n=30) {
      $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
      $randomString = '';

      for ($i = 0; $i < $n; $i++) {
          $index = rand(0, strlen($characters) - 1);
          $randomString .= $characters[$index];
      }

      return $randomString;
  }

}

?>