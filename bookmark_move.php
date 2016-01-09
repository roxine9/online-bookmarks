<?php
require_once ("./header.php");
logged_in_only ();

$bmlist = set_post_num_list ('bmlist');

if (count ($bmlist) == 0) {
	?>

	<h2 class="title">移动书签到:</h2>
	<form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?folderid=" . $folderid; ?>" method="POST" name="bookmarksmove">

				<div style="width:100%; height:330px; overflow:auto;">

					<?php
					require_once (ABSOLUTE_PATH . "folders.php");
					$tree = & new folder;
					$tree->make_tree (0);
					$tree->print_tree ();
					?>

				</div>
				<br>
				<input type="hidden" name="bmlist">
				<input type="submit" value=" 确定 ">
				<input type="button" value=" 取消 " onClick="self.close()">
				<input type="button" value=" 新建文件夹 " onClick="self.location.href='javascript:foldernew(<?php echo $folderid; ?>)'">

	</form>

	<script type="text/javascript">
	document.bookmarksmove.bmlist.value = self.name;
	</script>

	<?php
}
else if ($folderid == '') {
	message ('请选择目标文件夹');
}
else {
	$query = sprintf ("UPDATE bookmark SET childof='%d' WHERE id IN (%s) AND user='%s'",
		$mysql->escape ($folderid),
		$mysql->escape (implode (",", $bmlist)),
		$mysql->escape ($username));

	if ($mysql->query ($query)) {
		echo "书签移动成功<br>\n";
		echo '<script language="JavaScript">reloadclose();</script>';
	}
	else {
		message ($mysql->error);
	}
}

require_once (ABSOLUTE_PATH . "footer.php");
?>