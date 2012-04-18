<?php
/*
  Plugin Name: Textdomain Selector
  Plugin URI: http://themekraft.com/
  Description: Select your languagefiles for your Plugins. 
  Author: Sven Wagener<svenw@themekraft.com>, themekraft.com<contact@themekraft.com>
  Version: 0.1.0
  License: unknown
  Network: true
 */

if (!class_exists('textdomain_selector'))
    require_once dirname(__FILE__).'/textdomain-selector.php';

textdomain_selector::init();