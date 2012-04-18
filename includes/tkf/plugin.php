<?php 
/*
Plugin Name: Themekraft framework
Plugin URI: http://themekraft.com/plugin/tk-framework
Description: Speed up your wordpress developement with our framework
Author: Sven Wagener, Sven Lehnert
Author URI: http://themekraft.com/
License: GNU GENERAL PUBLIC LICENSE 3.0 http://www.gnu.org/licenses/gpl.txt
Version: 0.1.0
Text Domain: tkframework
Site Wide Only: false
*/

function framework_init(){
 // Registering the form where the data have to be saved
 $args['forms'] = array( 'myform' );
 $args['text_domain'] = 'my_text_domain';
 
 require_once( 'loader.php' );
 tk_framework( $args );
}
add_action( 'plugins_loaded', 'framework_init' );
 
function init_backend(){
 /*
  * WML
  */
	
 $wml = '<?xml version="1.0" ?>
    		<wml>
				<menu title="Custom Menu">
					<page title="Tabs" headline="Tab test">
						<p>You can also put in Text here like in HTML</p>
						<tabs>
							<tab title="Tab 1">
								Content in Tab 1
							</tab>
							<tab title="Tab 2">
								Content in Tab 2
							</tab>
							<tab title="Tab 3">
								Content in Tab 3
							</tab>
						</tabs>
					</page>
					<page title="Accordions" headline="Accordion test">
						<p>You can also put in Text here like in HTML</p>
						<accordion>
							<section title="Section 1">
								Content in Section 1
							</section>
							<section title="Section 2">
								Content in Section 3
							</section>
							<section title="Section 3">
								Content in Section 3
							</section>
						</accordion>
					</page>
					<page title="Forms" headline="Form test">
						<p>You can also put in Text here like in HTML</p>
						<form name="myform">
							<textfield name="name" label="Name:" tooltip="Put in your name"/>
							<textarea name="longtext" label="Long text:" />
							<checkbox name="mycheckbox" label="Check this:" description="Description for button" />
							<radio name="radiotest" label="Radio test" value="1" description="Button 1" />
							<radio name="radiotest" value="2" description="Button 2" />
							<radio name="radiotest" value="3" description="Button 3" />
							<select name="myselect" label="Select box">
								<option name="First entry" value="first" />
								<option name="Second entry" value="second" />
								<option name="Third entry" value="third" />
								<option name="Fourth entry" value="fourth" />
							</select>
							<colorpicker name="colorforme" label="Color" tooltip="Select a colour" />
							<file name="ourfile" label="File" tooltip="Upload your file!" />
							<button name="Save" />
						</form>
					</page>
					<page title="Nested HTML" headline="Usind HTML in WML">
						<p>As you can see,you can use HTML in WML.</p>
						<table class="widefat">
							<tr>
								<td>HTML Table</td>
							</tr>
							<tr>
								<td>
									<accordion>
										<section title="Section 1">
											Content of an accordion in HTML
										</section>
									</accordion>
								</td>
							</tr>
						</table>
						
					</page>
				</menu>
    		</wml>';
 
 /*
  * Example
  */
 // tk_wml_parse( $wml );
 // Creating php file for translations
 // tk_wml_create_textfiles( $wml );
 
 /*
  * Hiding elemts by id 
  */
 tk_hide_element( 's1' );
 
 /*
  * Example with WML file
  */
 
 // Example for loading xml file
 tk_wml_parse_file( dirname( __FILE__ ) . '/hide_test.xml' );
 // tk_wml_create_textfiles_from_wml_file( dirname( __FILE__ ) . '/example.xml' );
 
 
 /*
  * Getting back values from form fields
  */
 $values = tk_get_values( 'myform' );
}
add_action( 'admin_menu', 'init_backend' );