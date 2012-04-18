<?php

class TK_Form_Textfield extends TK_Form_Element{
	var $extra;
	var $before_element;
	var $after_element;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param array $args Array of [ $id Id, $name Name, $value Value, $extra Extra textfield code ]
	 */
	function tk_form_textfield( $args ){
		$this->__construct( $args );		
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param array $args Array of [ $id Id, $name Name, $value Value, $extra Extra textfield code ]
	 */
	function __construct( $args ){
		$defaults = array(
			'id' => '',
			'name' => '',
			'value' => '',
			'extra' => '',
			'before_element' => '',
			'after_element' => ''
		);
		
		$args = wp_parse_args($args, $defaults);
		extract( $args , EXTR_SKIP );
		
		parent::__construct( $args );
		
		$this->id = $id;
		$this->extra = $extra;
		
		$this->before_element = $before_element;
		$this->after_element = $after_element;		
	}
	
	/**
	 * Getting HTML of textfield
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @return string $html The html of the textfield
	 */
	function get_html(){
		if( $this->id != '' ) $id = ' id="' . $this->id . '"';
		if( $this->name != '' ) $name = ' name="' . $this->name . '"';
		if( $this->value != '' ) $value = ' value="' . $this->value . '"';
		if( $this->extra != '' ) $extra = $this->extra;
		
		$html = $this->before_element;
		$html.= '<input' . $id . $name . $value . $extra . ' type="text" />';
		$html.= $this->after_element;
		
		return $html;
	}
}

function tk_textfield( $args, $return_object = FALSE ){
	$textfield = new TK_Form_Textfield( $args );

	if( TRUE == $return_object ){
		return $textfield;
	}else{
		return $textfield->get_html();
	}	
}