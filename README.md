What is wpadmin?
--------------

A simpe template for your wordpress plugin or template option page

Requirements
------------

WordPress >= 2.7

Installing
----------

Copy wp-admin.php inside your plugin or template and include the file.

Using
-----

require("wp-admin.php");
$adminpage = new WP_OptionsPage();


Example usage:

	$args = array(
			'parent_slug' => 'themes.php', 		// set menu position (default is apparence)
			'capability' =>	'administrator', 	// set user privilege (default is administrator)
		
			);

	$adminpage = new WP_OptionsPage($args) ;
			
			
	$adminpage->setMenu("themes.php");  // set menu position (default is apparence)
	$adminpage->setTitle("Custom Admin Page"); // set page title and menu title
	$adminpage->setOptionName("options_setting"); // set options name saved in wp_options table

	$adminpage->addSettingRegion( "Main Setting" , "Lorem ipsum" ); // add setting region

	$adminpage->addSettingField(	"Main Setting", "text field" ); // add setting field
	$adminpage->addSettingField(	"Main Setting", "another text field" );	// add setting field


Changelog
---------------

**0.2**

- added more comments and documentation
- add default setting
- rename width WordPress standard naming convention


**0.1**

- initial release

Contributors
------------


