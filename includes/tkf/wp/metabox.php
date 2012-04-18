<?php

class TK_WP_Metabox extends TK_HTML{
	
	var $option_group;
	var $title;
	var $page;
	
	function tk_wp_metabox( $option_group, $title, $page = 'post' ){
	}
	
	function __construct( $option_group, $title, $page = 'post' ){
		parent::__construct();
		
		$this->option_group = $option_group;
		$this->title = $title;
		$this->page = $page;
	}
	
	function get_html(){
		$html = wp_nonce_field( $this->option_group , $this->option_group . '_nonce' );
		$html.= parent::get_html();
		return $html;
	}
		
	function create(){
		add_meta_box( $this->option_group, $this->title, array( $this, 'write_html' ) , $this->page );
	}
	
}