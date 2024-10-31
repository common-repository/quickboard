<?php
/**
 * Plugin Name:  Quickboard
 * Description: Quickboard ersetzt das Dashboard für die Rolle "Quickboard".
 * Plugin URI:  https://fab22.com/dashboardpro
 * Version:     1.1.0
 * Author:      fab22.com - ukischkel
 * Author URI:  https://fab22.com 
 * License:		GPL v2
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: quickboard
 * Domain Path: languages
*/

if ( ! defined( 'ABSPATH' ) ) exit; 

    $headerDescription = __( 'Quickboard ersetzt das Dashboard für die Rolle "Quickboard".', 'quickboard' );

	add_action( 'plugins_loaded', 'lade_sprachdatei_fuer_quickboard' );

		function lade_sprachdatei_fuer_quickboard() {
			load_plugin_textdomain( 'quickboard', false, dirname( plugin_basename(__FILE__) ) . '/languages' ); 
		} 
	
	### setting constants ###
	define( 'QUICKVERSION', '1.1.0' );
	define( 'QUICKBOARDPLUGIN', 'quickboard' );
	define( 'QUICKBOARDPLUGINNAME', 'Quickboard' ); 
	define( 'QUICKBOARD', basename( __FILE__ ) );
	define( 'QUICKBOARDFOLDER',  plugin_basename(__FILE__) );
	define( 'QUICKBOARDPLUGINURL', plugin_dir_url( __FILE__ ) );

	
	if ( !get_option( 'custom_dash_setting_information' ) ) {
		add_option( 'custom_dash_setting_information', '' );
	}
	
		/** Link on Plugin page **/
		function adddashlinks( array $links ) {
            $url = get_admin_url() . 'options-general.php?page=' . basename( __DIR__ ) . '/' . basename( __FILE__ );
			$teamlinks = '<a href="' . $url . '" style="color:#39b54a;">' .  __( "Einstellungen", "quickboard"  ) . '</a>';
			$links[ 'quick-info' ] = $teamlinks;
				return $links;
		}
		
		add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'adddashlinks', 10, 1 );	
        
		/** settings page **/
		function quickboard_settingmenue() {
		   add_submenu_page( 'options-general.php', 'Quickboard Settings', 'Quickboard Settings', 'manage_options', __FILE__, 'quickboard_settingpage' );
		}
		
		add_action( 'admin_menu', 'quickboard_settingmenue' );

		function quickboard_settingpage() {
			if( 'administrator' == quickboard_get_current_user_roles() ){
                include_once 'admin/settings.php';
					add_action( 'admin_footer', 'quickboard_makequickjavascript' );
			}	
		}	
			
		add_action( 'wp_ajax_quickboardgetquicklinks', 'quickboardgetquicklinks' );

		/** remove elements from Admin Bar **/
		function quickboard_custom_admin_bar_render() {
			global $wp_admin_bar;
			$wp_admin_bar->remove_node( 'wp-logo' );
			$wp_admin_bar->remove_menu('site-name');
			$wp_admin_bar->remove_menu( 'comments' );
			$wp_admin_bar->remove_menu( 'my-blogs' );
			$wp_admin_bar->remove_menu( 'appearance' );
			$wp_admin_bar->remove_menu( 'edit' );
			$wp_admin_bar->remove_menu( 'new-content' );
			$wp_admin_bar->remove_menu( 'updates' );
		}
		
		/** add new link to adminbar **/
		function quickboard_custom_admin_bar_link( $admin_bar ) {
			$admin_bar->add_menu( array(
			'id'    => 'wtf-quick-custom-link',
			'title' => 'Quickboard',
			'href'  => admin_url(),
			'meta'  => array(
					 'title' => __( 'Quickboard' ),
			),
			));
		}
		
		/** read user role **/
        function quickboard_get_current_user_roles() {
			if( is_user_logged_in() ) {
				$user = wp_get_current_user();
				$roles = ( array ) $user->roles;
				return $roles[ 0 ]; 
			} else {
				return array();
			}
		}
	
		/** adding CSS file **/
		function quickboard_additional_custom_styles() {
			wp_enqueue_style( 'wtf-cdash-css', QUICKBOARDPLUGINURL . 'includes/css/wtf-dashboard.css', 'all' );
		}
	
		function quickboard_cdash_css(){
			if( 'quickboard' == quickboard_get_current_user_roles() ){
				add_action( 'admin_head', 'quickboard_additional_custom_styles' );
				add_action( 'wp_before_admin_bar_render', 'quickboard_custom_admin_bar_render' );
				add_action( 'admin_bar_menu', 'quickboard_custom_admin_bar_link', 100 );	
				/* remove Admin Bar at frontend */
				add_filter( 'show_admin_bar', '__return_false' );
			}
		}
	
		add_action( 'admin_init', 'quickboard_cdash_css' );
				
		/** functions to sanitize array values **/
		function quickboard_sanitize_proof( $input ) {	
			$new_input = [];	
				foreach ( $input AS $key => $val ) {
					$new_input[ $key ] = ( isset( $input[ $key ] ) ) ? sanitize_text_field( $val ) : '';
				}	
					return $new_input;	
		}
				
		function quickboard_sanitize_submenu( $input ) {	
			$new_input = [];	
				foreach ( $input AS $key => $val ) {
					foreach( $val AS $secondkey => $secondval ){
						foreach( $secondval AS $thirdkey => $thirdval ){
							$new_input[ $key ][ $secondkey ][ $thirdkey ] = ( isset( $input[ $key ][ $secondkey ][ $thirdkey ] ) ) ? sanitize_text_field( $thirdval ) : '';
						}
					}
					
				}	
					return $new_input;	
		}
		
		/** function to answer AJAX call - returning checkboxes for setting page **/
		function quickboardgetquicklinks(){
			if ( isset( $_POST[ 'search' ] ) ) {
				$mainmenu = '';
				$submenu = quickboard_sanitize_submenu( unserialize( urldecode( $_POST[ 'menu' ] ) ) );
				$subproof = quickboard_sanitize_proof( unserialize( urldecode( $_POST[ 'proof' ] ) ) );
					foreach( $submenu AS $key => $val ){
						if( $key == sanitize_text_field( $_POST[ 'search' ] ) ){
							$addId = 0;
							foreach( $submenu[ $key ] AS $keyb => $value ){
								$addSet = '';
								$pagename = $value[ 2 ];
								if( in_array( $pagename, $subproof ) || $pagename == sanitize_text_field( $_POST[ 'search' ] ) ){
									$addSet = 'checked="checked"';
								}
								$linknamea =  $value[ 0 ];
								$linkname = trim( preg_replace( "/&#[a-z0-9]+;/i", '', str_replace( '&nbsp;', ' ', $linknamea ) ) );
								$menustring = $linkname . '#' . $pagename;
								$mainmenu .= '<label for="dashs' . sanitize_text_field( $_POST[ 'itemid' ] ) . $addId  . '" class="clabel" data-id="' . str_replace( ' ', '', sanitize_text_field( $_POST[ 'link' ] ) ) . '"><input type="checkbox" name="dashs[]" id="dashs' . sanitize_text_field( $_POST[ 'itemid' ] ) . $addId  . '" value="' . sanitize_text_field( $_POST[ 'link' ] ) . '#' . $menustring . '" form="dashform" autocomplete="off" ' . esc_html( $addSet ) . '>' . sanitize_text_field( $_POST[ 'link' ] ) . ' &rarr; ' . trim( $linkname ) . 
								'<span class="checkmark"></span></label>';
								$addId++;
							}
						}
					}   
					echo $mainmenu;
			}
			wp_die();
		}	


		/** adding javascript with AJAX function **/
		function quickboard_makequickjavascript(){ ?>
			<script type="text/javascript" id="quickboardjs">
			jQuery( document ).ready( function(){
			<?php echo "var ajaxurl = '" . admin_url( 'admin-ajax.php' ) . "';"; ?>
				jQuery( 'input[name^=mdash]' ).on( 'change', function(){
					if( jQuery( this ).is( ':checked' ) ){
						var data = {
							'action' : 'quickboardgetquicklinks',
							'link' : jQuery( this ).val().split( '#' )[ 0 ],
							'search' :  jQuery( this ).val().split( '#' )[ 1 ],
							'itemid' : jQuery( this ).attr( 'data-id' ),
							'menu' : jQuery( '#submenue' ).val(),
							'proof' : jQuery( '#subproof' ).val()
						};
						jQuery.post( ajaxurl, data, function( response ){
							var dataParts = response.split( '<label' );
							var checkstr = '';
								if( jQuery( '.untermenu' ).html() != '' ){
									checkstr = jQuery( '.untermenu' ).html().replace( '→', '&rarr;' ); 
								}
								for( var i = 1; i < dataParts.length; i++ ){
									var dataParta = dataParts[ i ].split( 'value="' )[ 1 ].split( '"' )[ 0 ]; 
									if( checkstr.includes( dataParta ) === false ){
										jQuery( '.untermenu' ).append( '<label' + dataParts[ i ] );
									}
								}
						});
					}
					else{
						jQuery( '[data-id=' + jQuery( this ).val().split( '#' )[ 0 ].replace( /\s/g, '' ) + ']' ).remove();
					}
				});
			});
		</script>				
<?php		
		}

	/** adding CSS rules and diabling functions on certain capabilities **/
	add_action( 'admin_head', 'makequickcss' );

	$activuserroles = get_role( 'quickboard' )->capabilities;
			
	function makequickcss(){ 
		global $activuserroles; 
	?>
		<style id="makequickcss" >
		<?php
			if( 'quickboard' == quickboard_get_current_user_roles() ){
				echo '#adminmenumain{display:none}';
				echo '#wpcontent{margin-left:0px}';
			
				if( array_key_exists( 'quick_noedit_files', $activuserroles ) ){
					echo 'a.page-title-action.aria-button-if-js{
							display:none;
						}
						#wp-media-grid h1::after, .attachment-info .actions a.view-attachment::after{
							content: "' . __( ' - File editing not available!', 'quickboard' ) .'";
							font-size:16px;
							color:red;
						}
						.uploader-window,.wp-editor-wrap .uploader-editor.droppable{
							display:none !important;
						}';
				}
				
				if( array_key_exists( 'quick_nopublish_pages', $activuserroles ) ){
					echo 'a[href$="post-new.php?post_type=page"]{
						display:none;
					}';
				}
				
				if( array_key_exists( 'quick_noplugin_install', $activuserroles ) ){
					echo 'a[href$="plugin-install.php"]{
						display:none;
					}';
				}
				
				if( array_key_exists( 'quick_nocreate_user', $activuserroles ) ){
					echo 'a[href$="user-new.php"]{
						display:none;
					}';
				}
			}
		?>
		</style>
	<?php 
	}
		
	
	if( array_key_exists( 'quick_noedit_files', $activuserroles ) ){
		add_filter( 'wp_handle_upload_prefilter', 'quickboard_disable_upload' );
	}

	function quickboard_disable_upload( $file ){
		$file[ 'error' ] = __( 'Upload von Dateien ist nicht erlaubt!', 'quickboard' );
		return $file; 
	}
	
final class Quickboardmaker{

	const VERSION = '1.0.0';

	const MINIMUM_PHP_VERSION = '7.0';

	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'admin_menu', array( $this,'quickboard_custom_register_menu') );
		add_action( 'plugins_loaded', array( $this,'quickboard_adding_personal_role') );
		add_action( 'current_screen', array( $this,'quickboard_custom_redirect_dashboard') );
	}

	public function quickboard_custom_register_menu() {
		add_dashboard_page( '', 'Custom Dashboard', 'read', 'customdashboard', array( $this, 'quickboard_custom_create_dashboard' ) );
	}

	public function quickboard_custom_create_dashboard() {
		include_once( 'public/quick-back.php'  );
	}
	
	/** redirect the dashboard to Quickboard **/
	public function quickboard_custom_redirect_dashboard() {
		static $called = false;
        $screen = get_current_screen();
            if( ( $screen->base == 'dashboard' || $screen->base == 'profile' ) && ( 'quickboard' == quickboard_get_current_user_roles() ) && ( !$called ) ){
                wp_redirect( admin_url( 'index.php?page=customdashboard' ) );
				$called = true;
            }
	}
	
	/** new role with basic capabilities **/
	public function quickboard_adding_personal_role(){
		$quickresult = add_role( 'quickboard', __( 'Quickboard' ), array( 
			'manage_options' => true,
			'manage_links' => true,
			'moderate_comments' => true,
			'publish_pages' => true,
			'publish_posts' => true,
			'read' => true,
			'read_private_pages' => true,
			'read_private_posts' => true,
			'unfiltered_html' => true,
		) );
	}

	public function init() {
		if ( version_compare( PHP_VERSION, self::MINIMUM_PHP_VERSION, '<' ) ) {
			add_action( 'admin_notices', array( $this, 'quickboard_admin_notice_minimum_php_version' ) );
			return;
		}
	}
	

	public function quickboard_admin_notice_minimum_php_version() {
		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}

		$message = sprintf(
			esc_html__( '"%1$s" requires "%2$s" version %3$s or greater.', 'quickboard' ),
			'<strong>' . esc_html__( '', 'quickboard' ) . '</strong>',
			'<strong>' . esc_html__( 'PHP', 'quickboard' ) . '</strong>',
			self::MINIMUM_PHP_VERSION
		);

		printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
	}

}
new Quickboardmaker();