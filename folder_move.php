<?php
require_once ("./header.php");
logged_in_only ();
require_once (ABSOLUTE_PATH . "folders.php");

$sourcefolder	= set_post_sourcefolder ();
$tree			= & new folder;
$parents		= $tree->get_path_to_root ($folderid);

if ($sourcefolder == "") {
	?>

	<h2 class="title">�ƶ��ļ���</h2>
  <form action="<?php echo $_SERVER['SCRIPT_NAME'] . "?folderid=" . $folderid . "&expand=" . implode (",", $expand);?>" method="POST" id="fmove">

			<div style="width:100%; height:330px; overflow:auto;">

				<?php
				$tree->make_tree (0);
				$tree->print_tree ();
				?>

			</div>
			<br>
			<input type="hidden" name="sourcefolder">
			<input type="submit" value="ȷ��">
			<input type="button" value="ȡ��" onClick="self.close()">
			<input type="button" value="�½��ļ���" onClick="self.location.href='javascript:foldernew(<?php echo $folderid; ?>)'">

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
	message ("�����ƶ������ļ���������Ŀ¼");
}
else if ($sourcefolder != "" && $sourcefolder != $folderid){
	$query = sprintf ("UPDATE folder SET childof='%d' WHERE id='%d' AND user='%s'", 
		$mysql->escape ($folderid),
		$mysql->escape ($sourcefolder),
		$mysql->escape ($username));

	if ($mysql->query ($query)) {
		echo "�ƶ��ɹ�<br>\n";
		echo '<script language="JavaScript">reloadclose();</script>';
	}
	else {
		message ($mysql->error);
	}
}
require_once (ABSOLUTE_PATH . "footer.php");
?>
