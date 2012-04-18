<?php

class textdomain_selector {
	/**
	 * The plugin version
	 */
	const VERSION = '0.1';

	/**
	 * Minimum required WP version
	 */
	const MIN_WP = '3.3.1';

	/**
	 * Minimum required PHP version
	 */
	const MIN_PHP = '5.2.1';

	/**
	 * Name of the plugin folder
	 */
	static $plugin_name;
	/**
	 * Can the plugin be executed
	 */
	static $active = false;
	/**
	 * storage for our own mofile sets
	 *
	 * @var array
	 */
	static $mofiles = array();
	/**
	 * storage of all used textdomains
	 *
	 * @var array
	 */
	static $textdomains = array();

	/**
	 * PHP5 constructor
	 *
	 * @since 	1.3
	 * @access 	public
	 * @uses	plugin_basename()
	 * @uses	add_action()
	 */
	private function __construct() {

	}

	private function __clone() {

	}

	public function init() {
		// TKF Stuff
		self::$plugin_name = plugin_basename(__FILE__);

		self::constants();

		add_action('after_setup_theme', array(__CLASS__, 'framework'), 0);

		add_action('init', array(__CLASS__, 'check_requirements'), 10);
		add_action('init', array(__CLASS__, 'start'), 10);

		add_action('admin_menu', array(__CLASS__, 'init_admin'), 10);

		add_action('activated_plugin', array(__CLASS__, 'activate'), 10);

		// TS stuff
		// Only .mo files are allowed to upload
		add_filter('upload_mimes', array(__CLASS__, 'set_upload_mimetype'));
		add_action('admin_head', array(__CLASS__, 'add_adminstylejs'));

		// load our .mo file, if possible
		add_filter('load_textdomain_mofile', array(__CLASS__, 'overwrite_textdomain'), 0, 2);

		// change actions for our forms
		add_filter('tk_form_start_tsnewform', array(__CLASS__, 'set_tsnewform_action'));
		add_filter('tk_form_start_tseditform', array(__CLASS__, 'set_tseditform_action'));

		// create the list
		add_filter('tk_admin_page_before_content_textdomains', array(__CLASS__, 'list_textdomains'));

		// add_filter( 'tk_admin_page_before_content_new_textdomain', array(__CLASS__, 'list_textdomains'));
		add_filter('load_textdomain_mofile', array(__CLASS__, 'catch_textdomain'), 0, 2);

		add_action('wp_loaded', array(__CLASS__, 'load_textdomains'), 999);

		add_action('tk_select_add_option', array(__CLASS__, 'add_select_options'), 0);
		add_action('tk_select_delete_option', array(__CLASS__, 'delete_select_options'), 0);
		//add_filter('tk_select_options_filter', array(__CLASS__, 'set_select_options'), 11, 2);



		// Load known custom textdomains
		self::get_textdomains();

		switch (true) {
			case (isset($_POST['tsnewform_values'])):
				self::save_textdomain($textdomain = null);
				break;
			case (isset($_POST['tseditform_values'])):
				self::update_textdomain();
				break;
			case (isset($_POST['submit_delete'])):
				self::delete_textdomain();
				break;
		}
	}

	/**
	 * Check for required versions
	 *
	 * Checks for WP, BP, PHP and Jigoshop versions
	 *
	 * @since 	0.1
	 * @access 	public
	 * @global 	string 	$wp_version 	Current WordPress version
	 * @return 	boolean
	 */
	public function check_requirements() {
		self::$active = (!$error ) ? true : false;
	}

	/**
	 * Starting framework
	 *
	 * Loading the themekraft framework and starting it
	 *
	 * @since 	0.1
	 * @access 	public
	 * @global 	string 	$wp_version 	Current WordPress version
	 * @return 	boolean
	 */
	public function framework() {
		if (!function_exists('tk_framework'))
			require_once TS_PLUGIN_ABSPATH . 'includes/tkf/loader.php';

		/*
		 *  Setup the Framework
		 */

		$args['jqueryui_components'] = array('jquery-fileuploader', 'jquery-ui-tabs', 'jquery-ui-accordion', 'jquery-colorpicker', 'jquery-ui-autocomplete'); // Selecting jquerui components
		$args['forms'] = array('TS_PLUGINform'); // Setting up forms needed

		tk_framework($args); // Starting Framework
	}

	/**
	 * Initializing Admin
	 *
	 * Loading the WML File for the admin
	 *
	 * @since 	0.1
	 * @access 	public
	 */
	public function init_admin() {

		// Checking user rights
		if (!current_user_can('level_10')) {
			return false;
		}

		// Add the hooks to add your functions to the framework here!

		/*
		 *  Example - Hooking into a jquery tab
		 *  add_filter( 'tk_wp_jqueryui_tabs_after_content_YOUR_ELEMENT_ID', 'myfunctiontoaddcontenttotab' );
		 *
		 */
		$wml = TS_PLUGIN_ABSPATH . 'components/admin/backend.xml'; // Using the backend.xml in components dir as default

		tk_wml_parse_file($wml);

		// tk_register_wp_option_group( 'sp_post_metabox' );
	}

	/**
	 * Load all related files
	 *
	 * Attached to bp_include. Stops the plugin if certain conditions are not met.
	 *
	 * @since 	1.3
	 * @access 	public
	 */
	public function start() {
		if (self::$active === false)
			return false;

		// Load your components here!

		/*
		 *  Example - Facebook component
		 * 	require_once TS_PLUGIN_ABSPATH . 'components/facebook/loader.php';
		 */
	}

	/**
	 * On plugin activations
	 *
	 * @since 	1.3
	 * @access 	public
	 */
	public function activate($plugin) {

		// Functions on activation of the plugin in
	}

	/**
	 * Load the languages
	 *
	 * @since 	1.0
	 * @uses 	load_plugin_textdomain()
	 */
	public function translate() {
		load_plugin_textdomain('tsplugin', false, dirname(self::$plugin_name) . '/languages/');
	}

	/**
	 * Declare all constants
	 *
	 * @since 	1.3
	 * @access 	private
	 */
	private function constants() {
		define('TS_PLUGIN_PLUGIN', self::$plugin_name);
		define('TS_PLUGIN_VERSION', self::VERSION);
		define('TS_PLUGIN_FOLDER', plugin_basename(dirname(__FILE__)));
		define('TS_PLUGIN_ABSPATH', trailingslashit(str_replace("\\", "/", WP_PLUGIN_DIR . '/' . TS_PLUGIN_FOLDER)));
		define('TS_PLUGIN_URLPATH', trailingslashit(plugins_url('/' . TS_PLUGIN_FOLDER)));
	}

	// TS Stuff
	static function get_textdomains() {
		self::$mofiles = get_option('textdomain_selector', array());
	}

	static function overwrite_textdomain($mofile, $domain) {
		if (isset(self::$mofiles[$domain])) {
			$mofile = file_exists(self::$mofiles[$domain]['mofile']) ? self::$mofiles[$domain]['mofile'] : $mofile;
		}
		return $mofile;
	}

	static function load_textdomains() {
		foreach (self::$mofiles AS $name => $textdomain) {
			if (file_exists($textdomain['mofile'])) {
				load_textdomain($name, $textdomain['mofile']);
			}
		}
	}

	/**
	 *
	 * @param array $upload_mimes
	 * @return array
	 */
	static function set_upload_mimetype($upload_mimes = array()) {
		// Das ist für Testzwecke um herauszufinden, wie wir dem Fileuploader beibringen nur .mo Dateien zu akzeptieren, wenn er von unserem Plugin aufgerufen wurde.
		$text = "GET:\n" . var_export($_GET, true) . "\n";
		$text .= "\nPOST:\n" . var_export($_POST, true) . "\n";
		$text .= "\nREF:\n" . $_SERVER['HTTP_REFERER'] . "\n";
		file_put_contents(ABSPATH . '/ts_log.txt', $text);

		// Vorerst müssen wir .mo allgemein erlauben
		$upload_mimes['mo'] = 'application/octet-stream';
		return $upload_mimes;
	}

	static function add_adminstylejs() {
		echo '<link id="textdomain_selector_css" rel="stylesheet" href="' . WP_PLUGIN_URL . '/textdomain-selector/admin/style.css' . '" type="text/css" />' . "\n";
		echo '<script id="textdomain_selector_js" type="text/javascript" src="' . WP_PLUGIN_URL . '/textdomain-selector/admin/scripts.js' . '"></script>' . "\n";
	}

	static function set_tsnewform_action($html) {
		$html = preg_replace('/action=\"[^\"]+\"/', 'action=""', $html);
		return $html;
	}

	static function set_tseditform_action($html) {
		$html = preg_replace('/action=\"[^\"]+\"/', 'action=""', $html);
		return $html;
	}

	static function save_textdomain($textdomain = null) {
		extract($_POST['tsnewform_values']);
		$fileparts = explode('/', $mofile);
		$uploaddir = wp_upload_dir();
		$mofile = $uploaddir['path'] . '/' . $fileparts[count($fileparts) - 1];
		self::$mofiles[$textdomain] = array(
			'textdomain' => $textdomain,
			'description' => $description,
			'mofile' => $mofile
		);
		update_option('textdomain_selector', self::$mofiles);
	}

	static function update_textdomain() {
		self::save_textdomain($_POST['tseditform']['textdomain']);
	}

	static function delete_textdomain() {
		if (isset(self::$mofiles[$_POST['textdomain']])) {
			unset(self::$mofiles[$_POST['textdomain']]);
			update_option('textdomain_selector', self::$mofiles);
		}
	}

	static function list_textdomains($html = '') {


		$td_list = '';
		foreach (self::$mofiles as $themofile) {
			$textdomain = $themofile['textdomain'];
			$description = $themofile['description'];
			$mofile = $themofile['mofile'];
			ob_start();
			include WP_PLUGIN_DIR . '/textdomain-selector/templates/list_textdomains_row.php';
			$td_list .= ob_get_clean();
		}

		ob_start();
		include WP_PLUGIN_DIR . '/textdomain-selector/templates/list_textdomains_table.php';
		$html .= ob_get_clean();

		return $html;
	}

	static function catch_textdomain($mofile, $domain) {
		$textdomain = array('name' => $domain, 'mofile' => $mofile);
		array_push(self::$textdomains, $textdomain);
		return $mofile;
	}

	static function add_select_options($select) {
		switch ($select->id) {
			case 'textdomain':
				foreach(self::$textdomains as $textdomain){
					$select->add_option_action($textdomain['name'], $textdomain['name']);
				}
				break;
		}
	}

	static function delete_select_options($select){
		switch($select->id){
			case 'textdomain':
				// Just an example, how to delete select options
				$select->delete_option_action('dummfug');
				break;
		}
	}

	static function set_select_options($options, $id) {
		switch ($id) {
			case 'textdomain':
				$options = '<option name="test1" value="test">Test</option>';
				return $options;
		}
	}

}