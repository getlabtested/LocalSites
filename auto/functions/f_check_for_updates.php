<?php
$version ="2.8.1";
$ch = curl_init();
$query = "http://www.blogsense-wp.com/2/update.php?version=$version";
curl_setopt($ch, CURLOPT_URL, $query);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$answer = curl_exec($ch);
curl_close ($ch);		
	
if ($answer!=1)
{ 
    ?>
   <script type="text/javascript">
        var retVal = confirm("New Update Available!\nWould you like to update?");
        if( retVal == true ){
	  window.location = "update.php"
	}
    </script>
    <?php
}

?>
	