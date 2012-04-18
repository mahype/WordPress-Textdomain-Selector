<?php 

class TK_WP_Form_Select extends TK_Form_Select{
	
	var $option_group;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $name Name of select field
	 * @param array $args Array of [ $id , $extra Extra select field code   ]
	 */
	function tk_wp_form_select( $name, $args = array() ){
		$this->__construct( $name, $args );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $name Name of select field
	 * @param array $args Array of [ $id , $extra Extra select field code, $option_group Name of optiongroup where select field have to be saved ]
	 */
	function __construct( $name, $args = array() ){
		global $post, $tk_form_instance_option_group;
		
		$defaults = array(
			'id' => '',
			'extra' => '',
			'size' => '',
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
		
		$args['name'] = $field_name;
		$args['value'] = $value;
		
		parent::__construct( $args );

	}			
}

function tk_form_select( $name, $options, $args = array(), $return_object = FALSE ){
	$select = new TK_WP_Form_Select( $name, $args );

	if(!empty($options)){
		foreach ( $options AS $option ){
			if( !is_array( $option) ){
				$select->add_option( $option );
			}else{
				$option_name = $option['name'];
				$args = array(
					'value' => $option['value'],
					'extra' => $option['extra']
				);
				$select->add_option( $option_name, $args );
			}
		}
	}
	if( TRUE == $return_object ){
		return $select;
	}else{
		return $select->get_html();
	}
}