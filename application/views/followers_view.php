<!DOCTYPE HTML>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Login with Twitter in CodeIgniter by CodexWorld</title>

</head>
<body>
<?php

echo '<h3><a href="'.base_url().'app">Go Back to HOME</a> <a style="float:right;" href="'.base_url().'app/logout">Logout from application</a></h3>';
if(!empty($error_msg)){
	echo '<p class="error">'.$error_msg.'</p>';	
}
?>

<?php
if($is_valid){

	if(!empty($followers->users)){
		echo "<h1>Followers List</h1><b>Click Links to get more details about user.</b><br></br>";
		echo "<table width=100%><thead>";
		echo "<tr><td>USER SCREEN NAME</td><td>USER ID</td><td>USER NAME</td></tr>";
		echo "</thead><tbody>";
		foreach ($followers->users as $key => $value) {
			echo "<tr><td><a href=".base_url()."app/follower/".$value->screen_name."/twubric.json>".$value->screen_name."</td><td>".$value->id."</td><td>".$value->name."</td></tr>";
			//print_r($value);
		}
		echo "</tbody></table>";
	}
}
?>

</body>
</html>