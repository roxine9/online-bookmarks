<?php
require_once ('./header.php');
logged_in_only ();

$delete = set_post_string_var ('delete');
$create = set_post_string_var ('create');
$new_username = set_post_string_var ('new_username');
$new_password = set_post_string_var('new_password');
$new_admin = set_post_bool_var ('new_admin', false);
$existing_user = set_post_string_var ('existing_user');
$noconfirm = set_get_noconfirm ();

$message1 = '';
$message2 = '';
?>

<h1 id="caption">����Ա����</h1>

<!-- Wrapper starts here. -->
<div style="min-width: <?php echo 230 + $settings['column_width_folder']; ?>px;">
	<!-- Menu starts here. -->
	<div id="menu">
		<h2 class="nav">��ǩҳ</h2>
		<ul class="nav">
		  <li><a href="./index.php">�ҵ���ǩ</a></li>
		  <li><a href="./shared.php">������ǩ</a></li>
		</ul>
	
		<h2 class="nav">������</h2>
		<ul class="nav">
			<?php if (admin_only ()) { ?>
			<li><a href="./admin.php">����Ա</a></li>
			<?php } ?>
			<li><a href="./import.php">������ǩ</a></li>
			<li><a href="./export.php">������ǩ</a></li>
			<li><a href="./sidebar.php">View as Sidebar</a></li>
			<li><a href="./settings.php">ϵͳ����</a></li>
			<li><a href="./index.php?logout=1">�˳�</a></li>
		</ul>
	<!-- Menu ends here. -->
	</div>

	<!-- Main content starts here. -->
	<div id="main">
	
	<?php
	if (!admin_only ()) {
		message ("�㲻�ǹ���Ա.");
	}
	
	if ($create == 'Create') {
		if ($new_username == '' || $new_password == '') {
			$message1 = '�û��������벻��Ϊ��.';
		}
		else if (check_username ($new_username)) {
			$message1 = '�û��Ѵ���.';
		}
		else {
			$query = sprintf ("INSERT INTO user (username, password, admin) VALUES ('%s', md5('%s'), '%d')", 
					$mysql->escape ($new_username),
					$mysql->escape ($new_password),
					$mysql->escape ($new_admin));
	
			if ($mysql->query ($query)) {
				$message1 = "�û��� $new_username �ѽ���.";
			}
			else {
				message ($mysql->error);
			}
			unset ($new_password, $_POST['new_password']);
		}
	}
	?>
	
				<div style="border: 1px solid #bbb; margin: 10px; padding: 10px;">
					<h2 class="caption">���û�</h2>
	
					<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="POST">
						<table>
							<tr>
								<td>�û���:
								</td>
								<td>
									<input type="text" name="new_username">
								</td>
							</tr>
	
							<tr>
								<td>��  ��:
								</td>
								<td>
									<input type="password" name="new_password">
								</td>
							</tr>
	
							<tr>
								<td>�Ƿ�������Ա:
								</td>
								<td>
									<input type="checkbox" name="new_admin" value="1">
								</td>
							</tr>
	
							<tr>
								<td>
								</td>
								<td>
									<input type="submit" name="create" value="ȷ��"> <?php echo $message1; ?>
								</td>
							</tr>
						</table>
					</form>
	
				</div>

				<div style="border: 1px solid #bbb; margin: 10px; padding: 10px;">
					<h2 class="caption">ɾ���û�</h2>

					<?php
					if ($delete == 'Delete') {
						if (check_username ($existing_user)) {
							if ($noconfirm) {
								$query = sprintf ("DELETE FROM user WHERE md5(username)=md5('%s')", 
										$mysql->escape ($existing_user));
								if ($mysql->query ($query)) {
									$message2 = "�û� $existing_user ��ɾ��.<br>";
								}
								else {
									message ($mysql->error);
								}
					
								$query = sprintf ("DELETE FROM bookmark WHERE md5(user)=md5('%s')", 
										$mysql->escape ($existing_user));
								if (!$mysql->query ($query)) {
									message ($mysql->error);
								}
					
								$query = sprintf ("DELETE FROM folder WHERE md5(user)=md5('%s')", 
										$mysql->escape ($existing_user));
								if (!$mysql->query ($query)) {
									message ($mysql->error);
								}
								list_users ();
							}
							else {
								?>
								
								<p>Are you sure you want to delete the user <?php echo $existing_user; ?> and all it's Bookmarks and Folders?</p>
								<form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?noconfirm=1"; ?>" method="POST" name="userdelete">
								<input type="hidden" name="existing_user" value="<?php echo $existing_user; ?>">
								<input type="submit" name="delete" value="ɾ��">
								<input type="button" value="ȡ��" onClick="self.location.href='./admin.php'"> 
								</form>
								
								<?php
							}
						}
						else {
							$message2 = '���û�������.';
							list_users ();
						}
					}
					else {
						list_users ();
					}
	
				function list_users () {
					global $mysql, $message2;;
					?>
	
					<form action="<?php echo $_SERVER['SCRIPT_NAME']; ?>" method="POST">
						<div style="height: 200px; width: 300px; overflow:auto;">
							<?php
							$query = "SELECT username, admin FROM user ORDER BY username";
			
							if ($mysql->query ($query)) {
								while ($row = mysql_fetch_object ($mysql->result)) {
									echo '<input type="radio" name="existing_user" value="'.$row->username.'">';
									if ($row->admin) {
										echo " <b>" . $row->username . "</b><br>\n";
									}
									else {
										echo " " . $row->username . "<br>\n";
									}
								}
							}
							else {
								message ($mysql->error);
							}
							?>
						</div>
						<input type="submit" name="delete" value="Delete">
						<?php echo $message2; ?>
	
					</form>
	
					<?php
				}
	
				?>
	
				</div>

				<div style="border: 1px solid #bbb; margin: 10px; padding: 10px;">
						<h2 class="caption">����汾</h2>

						<table>
							<tr>
								<td>�汾��:</td>
								<td><?php @readfile (ABSOLUTE_PATH . "VERSION"); ?></td>
							</tr>
						
							<tr>
								<td><a href="http://www.frech.ch/online-bookmarks/" target="_new">�鿴�°汾:</a></td>
								<td><a href="http://www.jackfans.com/products/" target="_new">�鿴�����汾</a></td>
							</tr>
						</table>
						
						<?php
						function check_version () {
							$version = null;
							if ($fp = @fsockopen ("www.frech.ch", 80)) {
								$get = "GET /online-bookmarks/bookmarks/VERSION HTTP/1.0\r\n\r\n";
								$data = null;
								fwrite ($fp, $get);
								while (!feof ($fp)) {
									$data .= fgets ($fp, 128);
								}
								fclose ($fp);
								$pos = strpos($data, "\r\n\r\n") + 4;
								$version = substr ($data, $pos, strlen ($data));
							}
							return $version;
						}
						?>
	
				</div>
		</div>

<?php
print_footer ();
require_once (ABSOLUTE_PATH . 'footer.php');
?>
