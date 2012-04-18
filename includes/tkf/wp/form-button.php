<?php
function tk_form_button( $value, $args = array(), $return_object = FALSE ){	tk_add_text_string( $value );
	return tk_button( $value, $args, $return_object );}