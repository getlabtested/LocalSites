<?php
session_start();

        $string= $_POST['string'];
		$open = fopen("../includes/rssfeeds.txt", "w");
		fwrite($open, $string);
		fclose($open);
		header("Location: ../index.php?p=4&saved=y");

?>