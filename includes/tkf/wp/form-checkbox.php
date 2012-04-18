<?php

class TK_WP_Form_Checkbox extends TK_Form_Checkbox{
	
	var $option_group;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $name Name of checkbox
	 * @param array $args Array of [ $id Id, $extra Extra checkbox code   ]
	 */
	function tk_wp_form_checkbox( $name, $args = array()){
		$this->__construct( $name, $args );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $name Name of checkbox
	 * @param array $args Array of [ $id Id, $extra Extra checkbox code, $option_group Name of optiongroup where checkbox have to be saved ]
	 */
	function __construct( $name, $args = array() ){
		global $tk_hidden_elements, $post, $tk_form_instance_option_group;
		
		$defaults = array(
			'id' => '',
			'value' => '',
			'extra' => '',
			'option_group' => $tk_form_instance_option_group,
			'before_element' => '',
			'after_element' => ''
		);
		
		$args = wp_parse_args($args, $defaults);
		extract( $args , EXTR_SKIP );
		
		if( $post != '' ){

			$option_group_value = get_post_meta( $post->ID , $option_group , TRUE );
			
			$field_name = $option_group . '[' . $name . ']';
			$value = $option_group_value[ $name ];

		}else{
			$value = get_option( $option_group  . '_values' );
						
			$this->option_group = $option_group;
			$field_name = $option_group . '_values[' . $name . ']';	
			
			$value = $value[ $name ];
		} 
		
		$checked = FALSE;
		
		if( $value != '' ){
			$checked = TRUE;
		}
		
		$args['name'] = $field_name;
		$args['checked'] = $checked;
		
		parent::__construct( $args );

	}		
}
function tk_form_checkbox( $name, $args = array(), $return_object = FALSE ){
	$checkbox = new TK_WP_Form_Checkbox( $name, $args  );
	
	if( TRUE == $return_object ){
		return $checkbox;
	}else{
		return $checkbox->get_html();
	}
}