<?php
/*
 * 22.02.2012 | maurizio
 * template per la gestione della pagina di amministrazione
 *
 * http://codex.wordpress.org/Creating_Options_Pages
 * http://ottodestruct.com/blog/2009/wordpress-settings-api-tutorial/
 * http://codex.wordpress.org/Settings_API	
 * http://ottopress.com/2009/wordpress-settings-api-tutorial/
 */


class MavidaAdminPage {
	
	private	$parent_slug;
	private	$page_title;
	private	$menu_title;
	private	$capability;
	private	$menu_slug;
	private	$function;
	
	private	$viewpath;
	private $settings_fields;
	
	
	function  __construct() { 
		
		// percorso del file con la vista
		$this->viewpath = dirname(__FILE__) . "/view.php";
		

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

		$this->parent_slug		= "themes.php";  // apparenza
		$this->page_title		= "Pagina di amministrazione";
		$this->menu_title	    = "Pagina di amministrazione";
		$this->capability		= "administrator";
		$this->menu_slug		= "pagina-di-amministrazione";
		$this->function_name	= array( $this , "show_custumm_page");	
	

		add_action('admin_menu', array( $this , "custom_admin_menu") , 999);
		
		
		// nome con cui vengono salvati i campi dentro le options
		$this->settings_fields = "odm_setting";
		
		add_action( 'admin_init', array( $this , "register_settings")  );
		
		} 
	
	/*
	 * setter for title
	 */
	public function setTitle( $title){
		$this->page_title = $title;
		$this->menu_title = $title;		
	}

	/*
	 * setter for menu
	 */
	public function setMenu( $menu ){
		$this->parent_slug = $menu;
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
		//register_setting( $this->settings_fields, 'version' );
		
		//register_setting( $this->settings_fields , $this->settings_fields , array( $this ,'plugin_options_validate' ));
		
		// la validazione è opzionale
		register_setting( $this->settings_fields , $this->settings_fields  );		

		//  imposto la prima sezione
		add_settings_section( $this->settings_fields . '_main', 'Main Settings', array( $this ,'lorem_impsum'), $this->settings_fields );

		//  imposto la seconda sezione
		add_settings_section( $this->settings_fields . '_footer', 'Footer Settings', array( $this ,'lorem_impsum'), $this->settings_fields );		

		// associo i campi da salvare
		add_settings_field('version', 'Versione', array( $this , 'display_field' ) , $this->settings_fields , $this->settings_fields . '_main' , array("version") );		
		add_settings_field('licenza', 'Licenza', array( $this , 'display_field' ) , $this->settings_fields , $this->settings_fields . '_main' , array("licenza") );				
	
	}
	
	/*
	 * visualizzazione del campo
	 */ 
	function display_field(  $args = array() ) {
		
		$options = get_option( $this->settings_fields );
		echo "<input id='" . $args[0] . "' name='" . $this->settings_fields . "[" . $args[0] . "]' size='40' type='text' value='" . $options[ $args[0] ] . "' />";	

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

			settings_fields( $this->settings_fields ); 


			/*
			if (file_exists( $this->viewpath )) {
				include( $this->viewpath );
			}
			*/

			do_settings_sections( $this->settings_fields ); 
			
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
	 * funzione di supporto
	 */
	function lorem_impsum( ) {
		echo "<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas ut arcu sem, at porta elit. Phasellus molestie facilisis sem, eget interdum justo ultrices ut. Maecenas rhoncus condimentum condimentum. </p>";
		}


}





