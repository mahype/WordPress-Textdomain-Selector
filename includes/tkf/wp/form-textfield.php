<?php

class TK_WP_Form_Textfield extends TK_Form_Textfield{
	
	var $option_group;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $name Name of textfield
	 * @param array $args Array of [ $id , $value,  $extra Extra textfield code   ]
	 */
	function tk_wp_form_textfield( $name, $args = array() ){
		$this->__construct( $name, $args );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $name Name of textfield
	 * @param array $args Array of [ $id,  $extra Extra textfield code, $option_group Name of optiongroup where textfield have to be saved   ]
	 */
	function __construct( $name, $args = array() ){
		global $post, $tk_form_instance_option_group;
		
		$defaults = array(
			'id' => '',
			'extra' => '',
			'option_group' => $tk_form_instance_option_group,
			'before_element' => '',
			'after_element' => ''
		);
		
		$args = wp_parse_args( $args, $defaults );
		extract( $args , EXTR_SKIP );

		if( $post != '' ){

			$option_group_value = get_post_meta( $post->ID , $option_group , true );
			
			$field_name = $option_group . '[' . $name . ']';
			$value = $option_group_value[ $name ];

		}else{
			$value = get_option( $option_group  . '_values' );
			
			$this->option_group = $option_group;
			$field_name = $option_group . '_values[' . $name . ']';	
			
			$value = $value[ $name ];
		}
		
		$args['name'] = $field_name;
		$args['value'] = $value;

		parent::__construct( $args );

	}
		
}

function tk_form_textfield( $name, $args = array(), $return_object = FALSE ){
	$textfield = new TK_WP_Form_Textfield( $name, $args );
		
	if( TRUE == $return_object ){
		return $textfield;
	}else{
		return $textfield->get_html();
	}
}