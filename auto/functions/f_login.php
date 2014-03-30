<?php 

//redirects the user to $page
function blogsense_redirect($page)
{
	echo  '<meta http-equiv="refresh" content="0;URL='.$page.'" />';
	//debug("REDIRECT called to $page");
	die();

}
function checkSession()
{

	if(isset($_COOKIE[LOGGED_IN_COOKIE])&&wp_validate_auth_cookie($_COOKIE[LOGGED_IN_COOKIE], 'logged_in')||(isset($_SESSION['wp_custom_session']) && $_SESSION['wp_custom_session']!=""))
		return true;
	else
		return false;

}

function log_user_in($username,$password)
{
	if(!session_is_registered('wp_custom_session'))
		session_register('wp_custom_session');
		
		$user = get_userdatabylogin($username);
		$user = $user->ID;
		//echo $user;exit;
		wp_set_auth_cookie($user, $password, $already_md5 = false, $home = '', $siteurl = '') ;
		$_SESSION['wp_custom_session'] = $username;
}

function log_user_out()
{
	$_SESSION['wp_custom_session'] = "";
	session_unregister('wp_custom_session');
	wp_clearcookie();
}