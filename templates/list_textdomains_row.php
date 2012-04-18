<tr>
	<td><?php echo $textdomain; ?></td>
	<td><?php echo $description; ?></td>
	<td>
		<form id="listform_<?php echo $textdomain; ?>" method="post" action="">
			<input name="textdomain" type="hidden" value="<?php echo $textdomain; ?>">
			<input name="submit_edit" type="submit" value="Bearbeiten" onclick="return ts_show_edit_form('<?php echo $textdomain; ?>', '<?php echo $description; ?>', '<?php echo $mofile; ?>')"><input name="submit_delete" type="submit" value="Löschen" onclick="return ts_delete_td('<?php echo $textdomain; ?>')">
		</form>
	</td>
</tr>