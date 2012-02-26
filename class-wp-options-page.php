<?php
/*
 * 22.02.2012 | maurizio
 * blank template per create options page inside wordpress backend
 * 
 *
 * http://codex.wordpress.org/Creating_Options_Pages
 * http://ottodestruct.com/blog/2009/wordpress-settings-api-tutorial/
 * http://codex.wordpress.org/Settings_API	
 * http://ottopress.com/2009/wordpress-settings-api-tutorial/
 */


class WP_OptionsPage {
	
	private	$parent_slug;
	private	$page_title;
	private	$menu_title;
	private	$capability;
	private	$menu_slug;
	private	$function;
	
	private	$viewpath;
	
	private $setting_name;
	private $setting_region = array();
	private $setting_field = array();	
	
	private $setting_callback = "";



	
	function  __construct( $args = array() ) { 

		$defaults = array(
			'parent_slug' => 'themes.php',
			'capability' =>	'administrator',
			'function_name'	=> array( $this , "show_custumm_page"),
			
			);
	
		$args = wp_parse_args( $args, $defaults );			
		extract( $args, EXTR_SKIP );


		
		// percorso del file con la vista
		$this->viewpath = dirname(__FILE__) . "/view.php";
		

		$this->parent_slug		= $parent_slug;
		$this->capability		= $capability;
		$this->function_name	= $function_name;

		$this->page_title		= "Custom Admin Page";
		$this->menu_title	    = "Custom Admin Page";
		$this->menu_slug		= $this->slugerize( $this->page_title );

	

		add_action('admin_menu', array( $this , "custom_admin_menu") , 999);
		
		
		// nome con cui vengono salvati i campi dentro le options
		$this->setting_name = "odm_setting";
		
		// esempio configurazione campi
		$this->addSettingRegion( "Main Setting" , $this->lorem_ipsum() );
		
		$this->addSettingField(	"Main Setting", "text field" );
		$this->addSettingField(	"Main Setting", "another text field" );		


		
		
		
		add_action( 'admin_init', array( $this , "register_settings")  );
		
		} 
		
		
	/*
	 * setter for title
	 */
	public function setTitle( $title){
		$this->page_title = $title;
		$this->menu_title = $title;	
		$this->menu_slug = $this->slugerize( $title );
		}

	/*
	 * setter for menu
	 */
	public function setMenu( $menu ){
		
		 /*
		 * esempi per parent_slug
		 *  For Dashboard: add_submenu_page( 'index.php', ... );
		 *  For Posts: add_submenu_page( 'edit.php', ... );
		 *  For Media: add_submenu_page( 'upload.php', ... );
		 *  For Links: add_submenu_page( 'link-manager.php', ... );
		 *  For Pages: add_submenu_page( 'edit.php?post_type=page', ... );
		 *  For Comments: add_submenu_page( 'edit-comments.php', ... );
		 *  For Appearance: add_submenu_page( 'themes.php', ... );
		 *  For Plugins: add_submenu_page( 'plugins.php', ... );
		 *  For Users: add_submenu_page( 'users.php', ... );
		 *  For Tools: add_submenu_page( 'tools.php', ... );
		 *  For Settings: add_submenu_page( 'options-general.php', ... ); 		
		 */ 
		
		$this->parent_slug = $menu;
		}		

	/*
	 * setter for Option Name
	 */
	public function setOptionName( $name ){
		$this->setting_name = $name; 
		}		
		

	/*
	 * setter for Settiong Region
	 */
	public function addSettingRegion( $region , $description ){

		$this->setting_region[] = array(	"name" 			=> $region,
											"slug" 			=> $this->slugerize( $this->setting_name . "-" . $region),
											"description" 	=> $description 
											); 
		}		

	/*
	 * setter for Option Field
	 */
	public function addSettingField( $region , $name, $input_type = "text" ){


		$this->setting_field[] = array(	"name" 			=> $name,
											"slug" 		=> $this->slugerize( $name ),
											"region" 	=> $this->slugerize( $this->setting_name . "-" . $region),
											"type" 		 => $input_type,
											); 
		}	
	

	/*
	 * creo la voce di menu usando add_submenu_page
	 *
	 * add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function ); 	
	 * http://codex.wordpress.org/Function_Reference/add_submenu_page
	 *
	 * The function which is hooked in to handle the output of the page must check
	 * that the user has the required capability as well.
	 *
	 * @param string $parent_slug The slug name for the parent menu (or the file name of a standard WordPress admin page)
	 * @param string $page_title The text to be displayed in the title tags of the page when the menu is selected
	 * @param string $menu_title The text to be used for the menu
	 * @param string $capability The capability required for this menu to be displayed to the user.
	 * @param string $menu_slug The slug name to refer to this menu by (should be unique for this menu)
	 * @param callback $function The function to be called to output the content for this page.
	 *
	 *
	 */
	function custom_admin_menu() {
	
	
		$hook = add_submenu_page( $this->parent_slug, 
													$this->page_title, 
													$this->menu_title, 
													$this->capability, 
													$this->menu_slug, 
													$this->function_name ); 	

		add_action('load-' . $hook, array( $this , 'load_custom_page' ) );     
	
		}
	
	
	/*
	 * gestione salvataggio optioni 
	 */
	function register_settings() {	
	
		// validation is optional
		// http://codex.wordpress.org/Function_Reference/register_setting

		register_setting( $this->setting_name , $this->setting_name  );		

		/*

		//  set section
		add_settings_section( '_main', 'Main Settings', array( $this ,'the_lorem_ipsum'), $this->setting_name );

		// set fields
		add_settings_field('version', 'Versione', array( $this , 'display_field' ) , $this->setting_name , '_main' , array("version") );		
		add_settings_field('licenza', 'Licenza', array( $this , 'display_field' ) , $this->setting_name ,  '_main' , array("licenza") );				

		*/

		foreach ( $this->setting_region as $region ) {
				$fuction = create_function('', "echo '<p>" . $region['description'] . "</p>'; return null;");
				add_settings_section( $region["slug"] , $region["name"] ,  $fuction  , $this->setting_name );		
			}

		foreach ( $this->setting_field as $field ) {
				//$fuction = create_function('', "echo '<p>" . $region['description'] . "</p>'; return null;");
				add_settings_field( $field["slug"], $field["name"] , array( $this , 'display_field' ) , $this->setting_name , $field["region"] , array( $field["slug"] ) );			
			}

		if ( $this->setting_callback != "" ) {
			call_user_func( $this->setting_callback );
			}

	}
	
	/*
	 * visualizzazione del campo
	 */ 
	function display_field(  $args = array() ) {
		
		$options = get_option( $this->setting_name );
		echo "<input id='" . $args[0] . "' name='" . $this->setting_name . "[" . $args[0] . "]' size='40' type='text' value='" . $options[ $args[0] ] . "' />";	

	} 

	/*
	 * validazione del campo
	 */ 
	function validate( $input ) {	
		// null
		echo "validazione <pre>";
		var_dump( $input);
		die();
		return $input;
	}
	
	/*
	 * template di base per la visualizzazione della pagina
	 */
	function show_custumm_page() {
		
			echo '<div class="wrap">';
			screen_icon('options-general'); 
			echo "<h2>" . $this->page_title . "</h2>";
			
			if ( $_GET["settings-updated"] == "true" ) {
				//<div id="setting-error-settings_updated" class="updated settings-error">
				echo '<div id="message" class="updated fade"><p>Salvataggio riuscito</p></div>';		
				}


			
			
			echo "<form action='options.php' method='post'>";

			settings_fields( $this->setting_name ); 


			/*
			if (file_exists( $this->viewpath )) {
				include( $this->viewpath );
			}
			*/

			do_settings_sections( $this->setting_name ); 
			
			// qui carico la vista della pagina
						
			echo $this->helper_view();
			
			echo "<p class='submit'>
					<input type='submit' class='button-primary' value='aggiorna' />
					</p>";
			
			echo '</form></div>';	
			
	}
	
	/*
	 * gestisco cosa deve essere visualizzato nella pagina di amministrazione
	 *
	 */
	function helper_view() {
							
		return "";
		}
	
	/*
	 * eseguita al caricamento di questa specifica pagina
	 */ 
	function load_custom_page() {
		
			// carico il css specifico della pagina solo se sono nella pagina di admin
			wp_register_style('admin', get_bloginfo('template_url') . '/admin/admin.css'); //CSS Generico				
			wp_enqueue_style( 'admin' );				

		}

	/*
	 * support function
	 */
	function lorem_ipsum( ) {
		return "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ut arcu sem, at porta elit. Phasellus molestie facilisis sem, eget interdum justo ultrices ut. Maecenas rhoncus condimentum condimentum.";
		}

	function the_lorem_ipsum( ) {
		echo $this->lorem_ipsum( );
		}

	/*
	 * support function
	 */
	function slugerize($phrase) {
		$result = strtolower($phrase);
	
		$result = preg_replace("/[^a-z0-9\s-]/", "", $result);
		$result = trim(preg_replace("/\s+/", " ", $result));
		$result = trim(substr($result, 0, 45));
		$result = preg_replace("/\s/", "-", $result);
		
	
		return $result;
	}	

}





