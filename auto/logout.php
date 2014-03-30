<?php 
session_start();

include("../wp-config.php");
include("functions/f_login.php");

log_user_out();
blogsense_redirect("login.php");

?>