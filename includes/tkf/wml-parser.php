<?php
/**
 * WordPress Markup Language parser (WPL Parser)
 * 
 * @author Sven Wagener <svenw_at_themekraft_dot_com>
 * @copyright themekraft.com
 * 
 */
class TK_WML_Parser{

	var $display;
	var $functions;
	var $bound_content;
	var $errstr;
	var $text_strings;
	var $create_textfiles;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 */
	function tk_wpml_parser( $return_object = TRUE ){
		$this->__construct( $return_object );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 */
	function __construct( $return_object = TRUE ){
		$this->display= array();
		
		// Menu & Pages
		$functions['menu'] = array( 'id' => '', 'title' => '', 'page' => array(), 'slug' => '', 'capability' => 'edit_posts', 'parent' => '',  'icon' => '', 'position' => '', 'return_object' => $return_object );
		$functions['page'] = array( 'id' => '', 'title' => '', 'content' => '', 'headline' => '', 'slug' => '', 'icon' => '' );
		$bound_content['menu'] = 'page';
		
		// Tabs
		$functions['tabs'] = array( 'id' =>'', 'tab' => array(), 'return_object' => $return_object );
		$functions['tab'] = array( 'id' => '', 'title' => '', 'content' => '' );
		$bound_content['tabs'] = 'tab';
		
		// Accordion
		$functions['accordion'] = array( 'id' => '', 'section' => array(), 'return_object' => $return_object );
		$functions['section'] = array( 'id' => '', 'title' => '', 'content' => '' );
		$bound_content['accordion'] = 'section';
		
		// Autocomplete
		$functions['autocomplete'] = array( 'name' => '', 'value' => array(), 'label' => '', 'return_object' => $return_object );
		$functions['value'] = array( 'content' => '' );
		$bound_content['autocomplete'] = 'value';
		
		// Form
		$functions['form'] = array( 'id' => '', 'name' => '', 'content' => '', 'return_object' => $return_object );
		
		// Form elements
		$functions['textfield'] = array( 'name' => '', 'label' => '', 'tooltip' => '' , 'return_object' => $return_object );
		$functions['textarea'] = array( 'name' => '', 'label' => '', 'tooltip' => '', 'return_object' => $return_object );
		$functions['colorpicker'] = array( 'name' => '', 'label' => '', 'tooltip' => '', 'return_object' => $return_object );
		$functions['file'] = array( 'name' => '', 'label' => '', 'tooltip' => '', 'return_object' => $return_object );
				
		$functions['checkbox'] = array( 'name' => '', 'description' => '', 'label' => '', 'tooltip' => '', 'return_object' => $return_object );
		$functions['radio'] = array( 'name' => '', 'value' => '', 'description' => '', 'label' => '', 'tooltip' => '', 'return_object' => $return_object );
		
		$functions['select'] = array( 'name' => '', 'option' => array(), 'label' => '', 'tooltip' => '',  'return_object' => $return_object );
		$functions['option'] = array( 'name' => '', 'value' => '' );
		$bound_content['select'] = 'option';		
		
		$functions['button'] = array( 'name' => '', 'return_object' => $return_object );
		
		$this->bound_content = $bound_content;
		
		// Putting all functions in an array
		$this->function_names = array_keys( $functions );
		$this->functions = $functions;
	}
	
	function load_wml( $xml_string, $return_object = FALSE ){
		
		// Checking if DOMDocument is installed
		if ( ! class_exists('DOMDocument') )
			return FALSE;
		
		// Loading XML
		$doc = new DOMDocument();
				
		set_error_handler( array( $this, 'wml_error' ) );
		if( !$doc->loadXML( $xml_string ) ){
			return FALSE;
		}
		restore_error_handler();
		
		return $this->load_dom( $doc );
	}
	
	function load_wml_file( $source ){
		
		$doc = new DOMDocument();
		if ( !file_exists( $source ) ){
			$this->errstr = '<strong>' . __( 'WML Document error: ' ) . '</strong>' .  __( 'File not found! Be sure, the full document path is given.' );
			add_action( 'all_admin_notices', array( $this, 'error_box' ), 1 );
			return FALSE;
		}
		set_error_handler( array( $this, 'wml_error' ) );
		if( !$doc->load( $source ) ){
			return FALSE;
		}
		restore_error_handler();
				
		return $this->load_dom( $doc );
	}
	
	function load_dom( $dom ){
		// Getting main node
		$node = $dom->getElementsByTagName( 'wml' );
		$mainnode = $node->item(0);
		
		// Getting object
		$this->display= $this->tk_obj_from_node( $mainnode );
				
		return TRUE;		
	}
	
	function wml_error($errno, $errstr, $errfile, $errline){		
	    if ( $errno == E_WARNING && ( substr_count( $errstr,"DOMDocument::loadXML()" ) > 0 ) ){
	       	$this->errstr = '<strong>' . __( 'WML Document error: ' ) . '</strong>' . substr( $errstr, 79, 1000 );
			add_action( 'all_admin_notices', array( $this, 'error_box' ), 1 );
	    }elseif ( $errno == E_WARNING && ( substr_count( $errstr,"DOMDocument::load()" ) > 0 ) ){
	    	$this->errstr = '<strong>' . __( 'WML Document error: ' ) . '</strong>' . substr( $errstr, 70, 1000 );
			add_action( 'all_admin_notices', array( $this, 'error_box' ), 1 );	    	
	    }
	    else
	        return false;
	}
	
	function error_box(){
		echo '<div id="message" class="error"><p>' . $this->errstr . '</p></div>';
	}


	function tk_obj_from_node( $node, $function_name = FALSE, $is_html = FALSE ){
		global $tkf_create_textfiles;
		
		// Getting node values
		$node_name = $node->nodeName;
   		$node_value = $node->nodeValue;
		$node_attr = $node->attributes;
		$node_list = $node->childNodes;
		
		/*
		 * Running node attributes 
		 */
		foreach( $node_attr as $attribute ){
			$params[$attribute->nodeName] = $attribute->nodeValue;
		}
		
		// Functions have to be executed before executing inner functions			
		if( FALSE != $function_name ){
			// Setting global form instance name
			if( $function_name == 'form' ){
				global $tk_form_instance_option_group;					
				$tk_form_instance_option_group = $params['name'];									
			}
		}		
		
		/*
		 * Running sub nodes 
		 */
		for( $i=0;  $i < $node_list->length; $i++ ){
			$subnode = $node_list->item( $i );
			$subnode_name = $subnode->nodeName;
			$subnode_value = $subnode->nodeValue;
			$subnode_attributes = $subnode->attributes;
			
			// WML Tag
			if( in_array( $subnode_name, $this->function_names ) ){												
				$params['content'][$i] = $this->tk_obj_from_node( $subnode, $subnode_name );
			
			// HTML Tag
			}elseif( $subnode->nodeType != XML_TEXT_NODE ){
				
				// Getting Tag attributes
				$attributes = '';
				foreach ( $subnode_attributes as $attr_name => $attrNode )
            		$attributes.= ' ' . $attr_name . '="' . $attrNode->value . '"'; 
				
				// Set up Tag
				$params['content'][$i] = array ( '<' . $subnode->nodeName . $attributes . '>', $this->tk_obj_from_node( $subnode, FALSE, TRUE ), '</' . $subnode->nodeName . '>' );
				
			// Text 
			}else{
				if( $subnode->nodeType == XML_TEXT_NODE && trim( $subnode_value ) != '' ){
					$params['content'][$i] = __( trim( $subnode_value )  , $tkf_text_domain );
					if( $tkf_create_textfiles ) tk_add_text_string( trim( $subnode_value ) );
				}
			}
		}
		
		/*
		 * Calling function / Returning value
		 */
		if( FALSE != $function_name ){
			$params = $this->cleanup_function_params( $function_name, $params );
			$function_result = call_user_func_array( 'tk_db_' . $function_name , $params );
			return $function_result;
		}elseif( $is_html ){
			return $params['content'];
		}else{
			return $params;
		}
	}

	function cleanup_function_params( $function_name, $params ){
		
		// Checking each param for function
		foreach( $this->functions[ $function_name ] AS $key => $function_params ){
			
			// If function was bound to content or has no content
			if( $params[ $key ] == '' ){
				// If function was bound to content
				if( $this->bound_content[ $function_name ] != '' && $key == $this->bound_content[ $function_name ] ){
					$params_new[ $this->bound_content[ $function_name ] ] = $params[ 'content' ];
				}else{
					// Rewrite key to function name
					$params_new[ $key ] = $this->functions[ $function_name ][ $key ];
				}

			// Getting content from set param
			}else{
				$params_new[ $key ] = $params[ $key ];
			}
		}
		return $params_new;
	}
	
	function get_html(){		
		$db = new TK_Display();
		return $db->get_html( $this->display );
	}
	
	function write_text_files( $file_src = FALSE ){
		global $tkf_text_domain, $tkf_text_domain_strings;
		
		$file_content = '<?php ' . chr(10) . chr(10);
		
		foreach ( $tkf_text_domain_strings AS $string ){
			if( $tkf_text_domain != '' ){
				$file_content.= '_e( \'' . $string . '\', \'' . $tkf_text_domain . '\' );' . chr(10);
			}else{
				$file_content.= '_e( \'' . $string . '\' );' . chr(10);
			}	
		}
		if( $tkf_text_domain != '' && !$file_src ){
			$file_src = dirname( __FILE__ ) . '/langfile_' . $tkf_text_domain . '.php';
		}elseif( !$file_src ) {
			$file_src = dirname( __FILE__ ) . '/langfile_' . md5( time() ) . '.php';
		}
		
		if( $file = fopen( $file_src, 'w' ) ){
			fwrite( $file, $file_content ); 
			fclose( $file );
		}else{
			$this->errstr = '<strong>' . __( 'Texdomain creation error: ' ) . '</strong> Can´t create text file';
			add_action( 'all_admin_notices', array( $this, 'error_box' ), 1 );
			return FALSE;
		}
		return TRUE;						
	}
}
/*
 * Menu functions
 */
function tk_db_menu( $id = '', $title = '', $elements = array(), $menu_slug = '', $capability = '', $parent_slug = '', $icon_url = '', $position = '', $return_object = FALSE ){
	
	$args = array(
			'id' => $id,
			'menu_title' => $title,
			'page_title' => $title,
			'capability' => $capability,
			'parent_slug' => $parent_slug,
			'menu_slug' => $menu_slug,			
			'icon_url' => $icon_url,
			'position' => $position,
			'object_menu' => TRUE
	);
	return tk_admin_pages( $elements, $args, $return_object );
}
function tk_db_page( $id, $title, $content, $headline = '', $menu_slug = '' , $icon_url = '' ){
	$page = array( 'id' => $id, 'menu_title' => $title, 'page_title' => $title, 'content' => $content, 'headline' => $headline, 'menu_slug' => $menu_slug, 'icon_url' => $icon_url );
	return $page;
}

/*
 * Tab functions
 */
function tk_db_tabs( $id = '', $elements = array(), $return_object = TRUE ){
	return tk_tabs( $id, $elements, $return_object );
}
function tk_db_tab( $id, $title, $content = '' ){
	return array( 'id' => $id, 'title' => $title, 'content' => $content );
}

/*
 * Accordion functions
 */
function tk_db_accordion( $id, $elements = array(), $return_object = TRUE ){
	return tk_accordion( $id, $elements, $return_object );
}
function tk_db_section( $id, $title, $content = '' ){
	return array( 'id' => $id, 'title' => $title, 'content' => $content );
}
/*
 * Autocomplete functions
 */
function tk_db_autocomplete( $name = '', $values = array(), $label, $return_object = TRUE ){
	if( trim( $label ) != '' ){
		
		tk_add_text_string( $label );
		tk_add_text_string( $tooltip );
		
		$before_element = '<div class="tk_field_row"><div class="tk_field_label"><label for="' . $name . '" title="' . $tooltip . '">' . $label . '</label></div><div class="tk_field">';
		$after_element = '</div></div>';
	}	
	$args = array(
		'id' => $name,
		'before_element' => $before_element,
		'after_element' => $after_element
	);

	return tk_jqueryui_autocomplete( $name, $values, $args, $return_object );
}
function tk_db_value( $value ){
	return $value;
}

/*
 * Form function
 */
function tk_db_form( $id, $name, $content = '', $return_object = TRUE ){
	$form = tk_form( $id, $name, $content, $return_object );
	return $form;
}

/*
 * Form element functions
 */
function tk_db_textfield( $name, $label, $tooltip, $return_object = TRUE ){
	if( trim( $label ) != '' ){
			
		tk_add_text_string( $label );
		tk_add_text_string( $tooltip );
		
		$before_element = '<div class="tk_field_row"><div class="tk_field_label"><label for="' . $name . '" title="' . $tooltip . '">' . $label . '</label></div><div class="tk_field">';
		$after_element = '</div></div>';
	}		 
	$args = array(
		'id' => $name,
		'before_element' => $before_element,
		'after_element' => $after_element
	);
	return tk_form_textfield( $name, $args, $return_object );
}

function tk_db_textarea( $name, $label, $tooltip, $return_object = TRUE ){
	if( trim( $label ) != '' ){

		tk_add_text_string( $label );
		tk_add_text_string( $tooltip );
		
		$before_element = '<div class="tk_field_row"><div class="tk_field_label"><label for="' . $name . '" title="' . $tooltip . '">' . $label . '</label></div><div class="tk_field">';
		$after_element = '</div></div>';
	}		 
	$args = array(
		'id' => $name,
		'before_element' => $before_element,
		'after_element' => $after_element
	);
	return tk_form_textarea( $name, $args, $return_object );
}
function tk_db_checkbox( $name, $description, $label, $tooltip, $return_object = TRUE ){
	if( trim( $label ) != '' ){
		
		tk_add_text_string( $label );
		tk_add_text_string( $tooltip );
		
		$before_element = '<div class="tk_field_row"><div class="tk_field_label"><label for="' . $name . '" title="' . $tooltip . '">' . $label . '</label></div><div class="tk_field">';
		$after_element = ' ' . $description . '</div></div>';
	}else{
		$after_element = ' ' . $description;
	}
	$args = array(
		'id' => $name,
		'before_element' => $before_element,
		'after_element' => $after_element
	);
	return tk_form_checkbox( $name, $args, $return_object );
}
function tk_db_radio( $name, $value, $description, $label, $tooltip, $return_object = TRUE ){
	if( trim( $label ) != '' ){
		
		tk_add_text_string( $label );
		tk_add_text_string( $tooltip );
		tk_add_text_string( $description );
		
		$before_element = '<div class="tk_field_row"><div class="tk_field_label"><label for="' . $name . '" title="' . $tooltip . '">' . $label . '</label></div><div class="tk_field">';
		$after_element = ' ' . $description . '</div></div>';
	}else{
		$after_element = ' ' . $description;
	}
	$args = array(
		'id' => $name,
		'before_element' => $before_element,
		'after_element' => $after_element
	);
	return tk_form_radiobutton( $name, $value, $args, $return_object );
}

function tk_db_select( $name, $options, $label, $tooltip, $return_object = TRUE ){
	if( trim( $label ) != '' ){
			
		tk_add_text_string( $label );
		tk_add_text_string( $tooltip );
		
		$before_element = '<div class="tk_field_row"><div class="tk_field_label"><label for="' . $name . '" title="' . $tooltip . '">' . $label . '</label></div><div class="tk_field">';
		$after_element = '</div></div>';
	}		 
	$args = array(
		'id' => $name,
		'before_element' => $before_element,
		'after_element' => $after_element
	);
	
	$select = tk_form_select( $name, $options, $args , true );
	$select->doactions();
	return $select->get_html();
}
function tk_db_option( $name, $value ){
	tk_add_text_string( $value );
	return array( 'name' => $name, 'value' => $value );
}

function tk_db_button( $name, $return_object = TRUE ){
		
	tk_add_text_string( $name );
	
	$args = array(
		'id' => $name,
		'before_element' => $before_element,
		'after_element' => $after_element
	);
	return tk_form_button( $name, $args, $return_object );
}

function tk_db_colorpicker( $name, $label, $tooltip, $return_object = TRUE ){
	if( trim( $label ) != '' ){
		
		tk_add_text_string( $label );
		tk_add_text_string( $tooltip );
		
		$before_element = '<div class="tk_field_row"><div class="tk_field_label"><label for="' . $name . '" title="' . $tooltip . '">' . $label . '</label></div><div class="tk_field">';
		$after_element = '</div></div>';
	}		 
	$args = array(
		'id' => $name,
		'before_element' => $before_element,
		'after_element' => $after_element
	);
	return tk_form_colorpicker( $name, $args, $return_object );
}

function tk_db_file( $name, $label, $tooltip, $return_object = TRUE ){
	if( trim( $label ) != '' ){
		
		tk_add_text_string( $label );
		tk_add_text_string( $tooltip );
		
		$before_element = '<div class="tk_field_row"><div class="tk_field_label"><label for="' . $name . '" title="' . $tooltip . '">' . $label . '</label></div><div class="tk_field">';
		$after_element = '</div></div>';
	}		 
	$args = array(
		'id' => $name,
		'before_element' => $before_element,
		'after_element' => $after_element
	);
	return tk_form_fileuploader( $name, $args, $return_object );
}

/*
 * Shortener functions ( For instancing without classes )
 */
function tk_wml_parse( $wml ){
	if( !empty( $wml ) ){
		$wml_parser = new TK_WML_Parser();	
		$wml_parser->load_wml( $wml );
		return $wml_parser->get_html();
	}else{
		return false;
	}
}
function tk_wml_parse_file( $source ){
	$wml_parser = new TK_WML_Parser();	
	$wml_parser->load_wml_file( $source );
	return $wml_parser->get_html();
}
function tk_wml_create_textfiles( $wml, $destination_src = FALSE ){
	global $tkf_create_textfiles;
	
	$tkf_create_textfiles = TRUE;
	
	if( !empty( $wml ) ){
		$wml_parser = new TK_WML_Parser( TRUE, TRUE );	
		$wml_parser->load_wml( $wml );
		return $wml_parser->write_text_files( $destination_src );
	}else{
		return false;
	}
}
function tk_wml_create_textfiles_from_wml_file( $source, $destination_src = FALSE ){
	global $tkf_create_textfiles;
	
	$tkf_create_textfiles = TRUE;
	
	$wml_parser = new TK_WML_Parser();	
	$wml_parser->load_wml_file( $source );
	return $wml_parser->write_text_files( $destination_src );
}
function tk_add_text_string( $string ){
	global $tkf_text_domain_strings, $tkf_text_domain, $tkf_create_textfiles;
	
	if( $tkf_create_textfiles && trim( $string ) != '' && !in_array( $string, $tkf_text_domain_strings) ){
		array_push( $tkf_text_domain_strings, $string );
	}
}