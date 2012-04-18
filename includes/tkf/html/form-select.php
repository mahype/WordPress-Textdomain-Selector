<?php

class TK_Form_select extends TK_Form_element{
	
	var $extra;
	var $elements;
	var $size;
	var $before_element;
	var $after_element;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param array $args Array of [ $id Id, $name Name, $value Value, $extra Extra selectfield code ]
	 */
	function tk_form_select( $args ){
		$this->__construct( $args );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param array $args Array of [ $id Id, $name Name, $value Value, $extra Extra selectfield code ]
	 */
	function __construct( $args ){
		$defaults = array(
			'id' => '',
			'name' => '',
			'value' => '',
			'size' => '',
			'extra' => ''
		);
		
		$args = wp_parse_args($args, $defaults);
		extract( $args , EXTR_SKIP );
		
		parent::__construct( $args );
		
		$this->size = $size;		
		$this->elements = array();
		$this->extra = $extra;
		$this->before_element = $before_element;
		$this->after_element = $after_element;
	}
	
	/**
	 * Adds an option to the select field
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @param string $option The option to show in list
	 * @param array $args Array of [ $value Value, $extra Extra option code ]
	 */
	function add_option( $option, $args = array() ){
		$defaults = array(
			'value' => '',
			'extra' => ''
		);
		
		$args = wp_parse_args($args, $defaults);
		extract( $args , EXTR_SKIP );
		
		$element = array( 'option' => $option, 'value' => $value, 'extra' => $extra );
		array_push( $this->elements, $element  );
	}
	
	/**
	 * Getting HTML of select box
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 * @return string $html The HTML of select box
	 */
	function get_html(){
		if( $this->id != '' ) $id = ' id="' . $this->id . '"';
		if( $this->name != '' ) $name = ' name="' . $this->name . '"';
		if( $this->size != '' ) $size = ' size="' . $this->size . '"';		
		if( $this->extra != '' ) $extra = $this->extra;
		
		$html = $this->before_element;
		$html.= '<select' . $id . $name . $size . $extra . '>';
		$options = '';
		if( count( $this->elements ) > 0 ){
			foreach( $this->elements AS $element ){
				$value = '';
				$extra = '';
				
				if( isset( $element['extra'] ) && $element['extra'] != '' ){ 
					$extra = $element['extra'];
				}
				
				if( isset( $element['value'] ) && $element['value'] != ''  ){
					$value =  ' value="' . $element['value'] . '"';
							
					if( $this->value == $element['value'] && $element['value'] != '' ){
						$options .=  '<option' . $value . ' selected' . $extra . '>' . $element['option'] . '</option>';
					}else{
						$options .=  '<option' . $value . $extra . '>' . $element['option'] . '</option>';
					}
				}else{
					if( $this->value == $element['option'] ){
						$options .=  '<option' . $value . ' selected' . $extra . '>' . $element['option'] . '</option>';
					}else{
						$options .=  '<option' . $value . $extra . '>' . $element['option'] . '</option>';
					}
				}
			}

		}

		$options = apply_filters('tk_select_options_filter', $options, $this->id);
		$html.= $options.'</select>';
		$html.= $this->after_element;
		
		return $html;
	}	

	public function add_option_action($value, $option_name = ''){
		// First delete an option with our value if exists
		$extras='';
		foreach($this->elements as $key => $element){
			if($element['value'] == $value){
				$extras = $element['extras'];
				unset($this->elements[$key]);
			}
		}
		$this->elements[] = array(
			'option' => $option_name,
			'value' => $value,
			'extras' => $extras,
		);
	}

	public function delete_option_action($value){
		foreach($this->elements as $key => $element){
			if($element['value'] == $value){
				unset($this->elements[$key]);
			}
		}

	}

	public function doactions(){
		do_action('tk_select_add_option', $this);
		do_action('tk_select_delete_option', $this);
	}
}
