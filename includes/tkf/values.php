<?php

class TK_Values{
	var $option_group;
	
	/**
	 * PHP 4 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 */
	function tk_values( $option_group ){
		$this->__construct( $option_group );
	}
	
	/**
	 * PHP 5 constructor
	 *
	 * @package Themekraft Framework
	 * @since 0.1.0
	 * 
	 */
	function __construct( $option_group ){
		$this->option_group = $option_group;
	}
	
	function get_values(){
		$values = get_option( $this->option_group  . '_values' );
		
		if( $values != '' )
			return (object) $values;
			 
		return FALSE;
	}
	
	function get_post_values( $postID = FALSE ){
		global $post;
		
		if( $postID != FALSE ){
			return get_post_meta( $postID , $option_group , true );
		}else if( isset( $post ) ){
			return get_post_meta( $post->ID , $option_group , true );
		}else{
			return FALSE;
		}
	}
}

function tk_get_values( $option_group ){
	$val = new TK_Values( $option_group );
	return $val->get_values();
}