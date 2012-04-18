/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

function ts_show_edit_form(textdomain, description, mofile){
	$ = jQuery;
	//document.getElementById('tseditform').style.display = 'block';
	$('#tseditform').css('display', 'block');
	$('#textdomain').val(textdomain);
	$('#description').val(description);
	$('#mofile').val(mofile);

	return false;
}

function ts_delete_td(textdomain){
	return confirm('Möchten Sie die Textdomain "' + textdomain + '" wirklich löschen?');
}

