<?php
require_once ("./header.php");
logged_in_only ();
require_once (ABSOLUTE_PATH . "folders.php");

$sourcefolder	= set_post_sourcefolder ();
$tree			= & new folder;
$parents		= $tree->get_path_to_root ($folderid);

if ($sourcefolder == "") {
	?>

	<h2 class="title">移动文件夹</h2>
  <form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?folderid=" . $folderid . "&expand=" . implode (",", $expand);?>" method="POST" id="fmove">

			<div style="width:100%; height:330px; overflow:auto;">

				<?php
				$tree->make_tree (0);
				$tree->print_tree ();
				?>

			</div>
			<br>
			<input type="hidden" name="sourcefolder">
			<input type="submit" value="确认">
			<input type="button" value="取消" onClick="self.close()">
			<input type="button" value="新建文件夹" onClick="self.location.href='javascript:foldernew(<?php echo $folderid; ?>)'">

	</form>

	<script type="text/javascript">
    this.focus();
    document.getElementById('fmove').sourcefolder.value = self.name;
	</script>

	<?php
}
else if ($sourcefolder == $folderid) {
	echo '<script language="JavaScript">self.close();</script>';
}
else if (in_array ($sourcefolder, $parents)){
	message ("不能移动到该文件夹下属子目录");
}
else if ($sourcefolder != "" && $sourcefolder != $folderid){
	$query = sprintf ("UPDATE folder SET childof='%d' WHERE id='%d' AND user='%s'", 
		$mysql->escape ($folderid),
		$mysql->escape ($sourcefolder),
		$mysql->escape ($username));

	if ($mysql->query ($query)) {
		echo "移动成功<br>\n";
		echo '<script language="JavaScript">reloadclose();</script>';
	}
	else {
		message ($mysql->error);
	}
}
require_once (ABSOLUTE_PATH . "footer.php");
?>
