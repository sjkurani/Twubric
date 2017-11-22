<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login with Twitter in CodeIgniter by CodexWorld</title>

</head>
<body>
<?php

if(!empty($error_msg)){
	echo '<p class="error">'.$error_msg.'</p>';	
}
?>

<?php
if(!empty($userData)){
echo '<h3><a style="float:right;" href="'.base_url().'app/logout">Logout from application</a></h3>';
	
	$outputHTML = '
		<div class="wrapper">
			<h1>Twitter Profile Details </h1>
			<div class="tw_box">
				<p class="image"><img src="'.$userData['picture_url'].'" alt="" width="80" height="60"/></p>
				<p><b>Twitter Username : </b>'.$userData['username'].'</p>
				<p><b>Name : </b>'.$userData['first_name'].' '.$userData['last_name'].'</p>
				<p><b>Twitter Profile Link : </b><a href="'.$userData['profile_url'].'" target="_blank">'.$userData['profile_url'].'</a></p>
				<p><b><a href="'.base_url().'app/followers">Click here for your followers list.</a></b></p>';

	$outputHTML .= '</div>';

}else{
	$outputHTML = '<a href="'.$oauthURL.'">Signin Using twitter account</a>';
}
?>
<?php echo $outputHTML; ?>

</body>
</html>