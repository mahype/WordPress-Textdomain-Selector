<?php

class TK_Jqueryui_Autocomplete extends TK_WP_Form_Textfield{
	
	var $autocomplete_values;
	
	function tk_jqueryui_autocomplete( $name, $args = array() ){
		$this->__construct( $name, $args );
	}
	
	function __construct( $name, $args = array() ){
		parent::__construct( $name, $args );
		$this->autocomplete_values = array();
	}
	
	function add_autocomplete_value( $value ){
		array_push( $this->autocomplete_values, '"' . $value . '"' ); 
	}
	
	function get_html(){
		$html = parent::get_html();

		$values= implode( ',', $this->autocomplete_values );
		
		$html .= '
			<script type="text/javascript">
			  	jQuery(document).ready(function($){
				  	$("#' . $this->id . '").autocomplete({ source: [' . $values . '] });
			  	});
	  		</script>
	  	';	
		
	  	return $html;
	  
	}
}
function tk_jqueryui_autocomplete( $name, $values, $args, $return_object = false ){
	$autocomplete = new TK_Jqueryui_Autocomplete( $name, $args );
	
	foreach ( $values AS $value ){
		$autocomplete->add_autocomplete_value( $value[0] );
	}
	
	if( TRUE == $return_object ){
		return $autocomplete;
	}else{
		return $autocomplete->get_html();
	}
}