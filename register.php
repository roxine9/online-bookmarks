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
						echo '<div style="color:red;">�û���$username�ѱ�ע�ᣬ��ѡ�������û���ע�ᡣ</div>'."\n";
						$username = false;
					}
					else {
						$username = $reg_username;
					}
	}
	else {
		echo '<div style="color:red;">�������û���</div>'."\n";
		$username = false;
	}

	if (isset ($_POST['reg_password1']) && $_POST['reg_password1'] != "" &&
		  isset ($_POST['reg_password2']) && $_POST['reg_password2'] != "") {
		if (md5 ($_POST['reg_password1']) != md5 ($_POST['reg_password2'])) {
			echo '<div style="color:red;">���벻���</div>'."\n";
			$password = false;
		}
		else {
			$password = md5 ($_POST['reg_password1']);
		}
	}
	else {
		echo '<div style="color:red;">��������������</div>'."\n";
		$password = false;
	}

	if ($reg_email != '') {
		if (preg_match ('/^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i', $reg_email)) {
			$query = "SELECT COUNT(*) AS result FROM user WHERE email='$reg_email'";
			if ($mysql->query ($query)) {
				if (mysql_result ($result, 0) > 0) {
					echo '<div style="color:red;">���ʼ���ַ��ע��</div>'."\n";
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
			echo '<div style="color:red;">��Ч���ʼ���ַ</div>'."\n";
			$email = false;
		}
	}
	else {
		echo '<div style="color:red;">��������Ч���ʼ���ַ</div>'."\n";
		$email = false;
	}


	if ($username && $password && $email) {
		$query = "      INSERT INTO user
				(username, password, email, active)
				VALUES
				('$username', md5('$password'), '$email', '0')";

		if (mysql_query ("$query")) {
			# dieser key wird als username und secret md5 hash an den
			# user geschickt und f�r die verifikation der registrierung gebraucht.
			$key = md5 ($username . $secret);

			$headers = "����: noreply@yourdomain.com\r\n" .
			$subject = '���� yourdomain.com �����ղؼе�ע����Ϣ';
			$message  = "��� $username,\r\n\r\n";
			$message .= "������������վ �����ղؼ� ��ע����Ϣ�� ";
			$message .= "����û����� '$username'�� Ϊȷ����ȫ�����ע�����벻�ڴ��ʼ��С� ";
			$message .= "��������ʻ����������� URL:\r\n\r\n";
			$message .= "http://www.yourdomain.com/register.php?confirm=$key\r\n\r\n";
			$message .= "In case of complications regarding this user account registration, ";
			$message .= "please contact support@yourdomain.com\r\n\r\n";
			$message .= "With kind regards, your yourdomain.com Team";

			mail($email, $subject, $message, $headers);

			echo "  ע��ɹ��������������䣬������ע���ʼ��е����ӡ�";
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
			echo "ע��ɹ���";
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
		<td>�û���:</td>
		<td><input name="reg_username" type="text" value=""></td>
	</tr>
	<tr>
		<td>����:</td>
		<td><input name="reg_password1" type="password" value=""></td>
	</tr>
	<tr>
		<td>�ٴ�����:</td>
		<td><input name="reg_password2" type="password" value=""></td>
	</tr>
	<tr>
		<td>�����ʼ�:</td>
		<td><input name="reg_email" type="text" value=""></td>
	</tr>
	<tr>
		<td></td>
		<td><input type="submit" value="ȷ��ע��" name="reg_register"></td>
	</tr>
</table>
</form>

<?php
}

function display_register_additional_text () {
	?>
	<p>����дע����Ϣ��</p>

	<p>����Ѿ���ע���Ա����<a class="orange" href="./index.php">��¼</a>��</p>
	<?php
}

require_once ("./footer.php");
?>
