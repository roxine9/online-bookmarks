<?php
require_once ("./header.php");

$secret = "dDWUc72sCcs20cXskcw";
$reg_register = set_post_bool_var ('reg_register', false);
$reg_username = set_post_string_var ('reg_username');
$reg_email = set_post_string_var ('reg_email');
$confirm = set_get_string_var ('confirm');

if ($reg_register) {
	if ($reg_username != "") {
					if (check_username ($reg_username)) {
						echo '<div style="color:red;">用户名$username已被注册，请选择其他用户名注册。</div>'."\n";
						$username = false;
					}
					else {
						$username = $reg_username;
					}
	}
	else {
		echo '<div style="color:red;">请输入用户名</div>'."\n";
		$username = false;
	}

	if (isset ($_POST['reg_password1']) && $_POST['reg_password1'] != "" &&
		  isset ($_POST['reg_password2']) && $_POST['reg_password2'] != "") {
		if (md5 ($_POST['reg_password1']) != md5 ($_POST['reg_password2'])) {
			echo '<div style="color:red;">密码不相符</div>'."\n";
			$password = false;
		}
		else {
			$password = md5 ($_POST['reg_password1']);
		}
	}
	else {
		echo '<div style="color:red;">请两次输入密码</div>'."\n";
		$password = false;
	}

	if ($reg_email != '') {
		if (preg_match ('/^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $reg_email)) {
			$query = "SELECT COUNT(*) AS result FROM user WHERE email='$reg_email'";
			if ($mysql->query ($query)) {
				if (mysql_result ($result, 0) > 0) {
					echo '<div style="color:red;">该邮件地址已注册</div>'."\n";
					$email = false;
				}
				else {
					$email = $reg_email;
				}
			}
			else {
				$email = false;
				message ($mysql->error);
			}
		}
		else {
			echo '<div style="color:red;">无效的邮件地址</div>'."\n";
			$email = false;
		}
	}
	else {
		echo '<div style="color:red;">请输入有效的邮件地址</div>'."\n";
		$email = false;
	}


	if ($username && $password && $email) {
		$query = "      INSERT INTO user
				(username, password, email, active)
				VALUES
				('$username', md5('$password'), '$email', '0')";

		if (mysql_query ("$query")) {
			# dieser key wird als username und secret md5 hash an den
			# user geschickt und f黵 die verifikation der registrierung gebraucht.
			$key = md5 ($username . $secret);

			$headers = "来自: noreply@yourdomain.com\r\n" .
			$subject = '你在 yourdomain.com 网络收藏夹的注册信息';
			$message  = "你好 $username,\r\n\r\n";
			$message .= "以下是你在我站 网络收藏夹 的注册信息： ";
			$message .= "你的用户名： '$username'。 为确保安全，你的注册密码不在此邮件中。 ";
			$message .= "激活你的帐户，请点击链接 URL:\r\n\r\n";
			$message .= "http://www.yourdomain.com/register.php?confirm=$key\r\n\r\n";
			$message .= "In case of complications regarding this user account registration, ";
			$message .= "please contact support@yourdomain.com\r\n\r\n";
			$message .= "With kind regards, your yourdomain.com Team";

			mail($email, $subject, $message, $headers);

			echo "  注册成功。请查阅你的邮箱，并激活注册邮件中的链接。";
		}
		else {
			echo mysql_error ();
		}
	}
	else {
		display_register_form ();
	}
}
else if ($confirm != '' && strlen ($confirm) === 32) {
	$query = "SELECT username FROM user WHERE MD5(CONCAT(username,'$secret'))='$confirm' AND active='0'";
	$result = mysql_query ("$query");
	if (mysql_num_rows ($result) == 1) {
		# the registration confirmation was successufull,
		# thus we can enable the useraccount in the database.
		$username = mysql_result ($result, 0);
		$query = "UPDATE user SET active='1' WHERE username='$username' AND active='0'";
		if (mysql_query ($query)) {
			echo "注册成功。";
		}
	}
	else {
		display_register_form ();
	}
}
else {
	display_register_additional_text ();
	display_register_form ();
}

function display_register_form () {
?>

<form method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>" name="loginform">
<table border="0">
	<tr>
		<td>用户名:</td>
		<td><input name="reg_username" type="text" value=""></td>
	</tr>
	<tr>
		<td>密码:</td>
		<td><input name="reg_password1" type="password" value=""></td>
	</tr>
	<tr>
		<td>再次输入:</td>
		<td><input name="reg_password2" type="password" value=""></td>
	</tr>
	<tr>
		<td>电子邮件:</td>
		<td><input name="reg_email" type="text" value=""></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="确认注册" name="reg_register"></td>
	</tr>
</table>
</form>

<?php
}

function display_register_additional_text () {
	?>
	<p>请填写注册信息。</p>

	<p>如果已经是注册会员，请<a class="orange" href="./index.php">登录</a>。</p>
	<?php
}

require_once ("./footer.php");
?>
