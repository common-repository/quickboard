<?php

	wp_enqueue_script( 'jquery-ui-core' );
	wp_enqueue_script( 'jquery-ui-tabs' );

		/** own scripts **/
		wp_enqueue_style( 'quickboard-css', QUICKBOARDPLUGINURL . 'includes/css/quickboard.css' , false, time(), 'all' );	
		wp_enqueue_script( 'quick-js', QUICKBOARDPLUGINURL . '/includes/js/quick.js', array( 'jquery' ), null, true );	
		wp_localize_script( 'quick-js', 'quick_strings', array( 'pluginpfad' => plugin_dir_url( __FILE__ ) ) );	
        global $menu;
        global $submenu;
		wp_enqueue_media();
		
		/** re-arrange arrays so they start with 0 counting up 1 **/
		$menu = array_values( $menu );

	function quickboard_settingsuserrole(){
		if( is_user_logged_in() ) {
			$user = wp_get_current_user();
			$roles = ( array ) $user->roles;
			return $roles[ 0 ]; 
		}
		else{
			return '';
		}
	}
	
?>

<div class="wrap">
<form id="dashform" method="post" action="">
<?php wp_nonce_field( 'dashsubmit', 'dashproofone' ); ?>
</form>
<?php

	/** delete and set capabilities **/
	function quickboard_rem_personal_caps() {
		$role = get_role( 'quickboard' );
		$alladmincap = get_role( 'administrator' )->capabilities;
		foreach( $alladmincap AS $key => $value ){ 
			$role->remove_cap( $key );
		}
		$role->remove_cap( 'quick_noedit_files' );
		$role->remove_cap( 'quick_nopublish_posts', true );
		$role->remove_cap( 'quick_nopublish_pages' );
		$role->remove_cap( 'quick_noplugin_install' );
		$role->remove_cap( 'quick_nocreate_users' );
	}
			
		/** sanitize function for POST-data **/	
		function quickboard_sanitize_postdata( $input ) {	
			$new_input = [];	
				foreach ( $input AS $key => $val ) {
					$new_input[ $key ] = ( isset( $input[ $key ] ) ) ? sanitize_text_field( $val ) : '';
				}	
					return $new_input;	
		}
		
	/** save **/
    if ( isset( $_POST[ 'dashsubmit' ] ) && wp_verify_nonce( $_POST[ 'dashproofone' ], 'dashsubmit' ) && 'administrator' == quickboard_settingsuserrole() ) {
		quickboard_rem_personal_caps();
        $newarr = [];
		/* save which pages / options allowed */
        if ( isset( $_POST[ 'mdash' ] ) ) {
            foreach( quickboard_sanitize_postdata( $_POST[ 'mdash' ] ) AS $key => $val ){
                $mainarr = explode( '#',  sanitize_text_field( $val ) );
                if( $mainarr[ 2 ] != '' ){
                    for( $i = 0; $i < count( $mainarr ); $i++ ){
                        $newarr[ $key ][] =  urldecode( str_replace( ' ', '-', strtolower( $mainarr[ $i ] ) ) );
                    }
                }
                    if ( isset( $_POST[ 'dashs' ] ) ) { 
                        foreach( quickboard_sanitize_postdata( $_POST[ 'dashs' ] ) AS $keyb => $valb ){
                            $subarr = explode( '#', $valb );
                            if( $mainarr[ 0 ] == $subarr[ 0 ] ){ 
                                for( $i = 0; $i < count( $subarr ); $i++ ){
                                    $newarr[ $key ][] = urldecode( $subarr[ $i ] );
                                }
                            }
                        }
                    }
            }
			$new_arr = [];
			foreach( $newarr AS $key => $value ){
				$new_arr[] = $value;
			}
            update_option( 'custom_dash_setting_information', $new_arr );
				
	}

		/* add new capapbilities to role "Quickboard" */
		function quickboard_add_personal_caps( $caplink ){
			$role = get_role( 'quickboard' );
			if( stripos( str_replace( '.', ' ', $caplink ), 'elementor' ) !== false ){
				$caplink = 'elementor';
			}
			$role->add_cap( 'manage_options', true );
			$role->add_cap( 'manage_links', true );
			$role->add_cap( 'manage_categories', true ); 
			$role->add_cap( 'read', true );
			$role->add_cap( 'moderate_comments', true );
			$role->add_cap( 'unfiltered_html', true );

			switch ( $caplink ) {
				case 'edit-tags.php?taxonomy=post_tag' :
					$role->add_cap( 'manage_post_tags', true );
					break;

				case 'upload.php' :
					$role->add_cap( 'upload_files', true );
					$role->add_cap( 'edit_files', true );
					$role->add_cap( 'quick_noedit_files', true );
					break;

				case 'media-new.php' :
					$role->add_cap( 'upload_files', true );
					$role->add_cap( 'edit_files', true );
					$role->add_cap( 'edit_posts', true );
					$role->add_cap( 'edit_others_posts', true ); 
					$role->add_cap( 'edit_private_posts', true ); 
					$role->add_cap( 'edit_published_posts', true ); 
					$role->add_cap( 'delete_others_posts', true ); 
					$role->add_cap( 'delete_posts', true ); 
					$role->add_cap( 'delete_private_posts', true ); 
					$role->add_cap( 'delete_published_posts', true );
					$role->remove_cap( 'quick_noedit_files' );
					break;

				case 'edit.php' :
					$role->add_cap( 'read_private_posts', true );
					$role->add_cap( 'edit_posts', true ); 
					$role->add_cap( 'edit_files', true ); 
					$role->add_cap( 'edit_others_posts', true ); 
					$role->add_cap( 'edit_private_posts', true ); 
					$role->add_cap( 'edit_published_posts', true ); 
					$role->add_cap( 'delete_others_posts', true ); 
					$role->add_cap( 'delete_posts', true ); 
					$role->add_cap( 'delete_private_posts', true ); 
					$role->add_cap( 'delete_published_posts', true );
					$role->add_cap( 'quick_nopublish_posts', true );
					break;
					
				case 'post-new.php' :
					$role->add_cap( 'publish_posts', true );
					$role->remove_cap( 'quick_nopublish_posts', true );
					break;
				
				case 'edit.php?post_type=page' :
					$role->add_cap( 'edit_pages', true );
					$role->add_cap( 'read_private_pages', true );
					$role->add_cap( 'edit_others_pages', true );
					$role->add_cap( 'edit_private_pages', true );
					$role->add_cap( 'edit_published_pages', true );
					$role->add_cap( 'delete_others_pages', true );
					$role->add_cap( 'delete_private_pages', true );
					$role->add_cap( 'delete_published_pages', true );
					$role->add_cap( 'delete_pages', true );
					$role->add_cap( 'quick_nopublish_pages' );
					break;

				case 'post-new.php?post_type=page' :
					$role->add_cap( 'publish_pages', true );
					$role->remove_cap( 'quick_nopublish_pages' );
					break;

				case 'plugins.php' :
					$role->add_cap( 'activate_plugins', true );
					$role->add_cap( 'update_plugins', true );
					$role->add_cap( 'delete_plugins', true );
					$role->add_cap( 'quick_noplugin_install' );
					break;

				case 'plugin-install.php' :
					$role->add_cap( 'install_plugins', true );
					$role->add_cap( 'delete_plugins', true );
					$role->remove_cap( 'quick_noplugin_install' );
					break;

				case 'plugin-editor.php' :
					$role->add_cap( 'edit_plugins', true );
					break;

				case 'users.php' :
					$role->add_cap( 'list_users', true );
					$role->add_cap( 'quick_nocreate_users' );
					break;

				case 'user-new.php' :
					$role->add_cap( 'create_users', true );
					$role->remove_cap( 'quick_nocreate_users' );
					break;

				case 'import.php' :
					$role->add_cap( 'import', true );
					break;

				case 'export.php' :
					$role->add_cap( 'export', true );
					break;

				case 'site-health.php' :
					$role->add_cap( 'view_site_health_checks', true );
					break;

				case 'export-personal-data.php' :
					$role->add_cap( 'export_others_personal_data', true );
					break;

				case 'erase-personal-data.php' :
					$role->add_cap( 'erase_others_personal_data', true );
					break;

			}

		} 	
		
			for( $i = 0; $i < count( $new_arr ); $i++ ){
				for( $y = 0; $y < count( $new_arr[ $i ] ); $y = $y + 3 ){
					$caplink = $new_arr[ $i ][ ( $y + 2 ) ];
					quickboard_add_personal_caps( $caplink );
				}
			}
		
		
    }
    
    /** delete all settings to role "Quickboard" **/
	if ( isset( $_POST[ 'del' ] ) ) {
		update_option( 'custom_dash_setting_information', '' );
		quickboard_rem_personal_caps();
    }   
    
	/** get options from db **/
	$dashsetting = [];
    if ( !empty( get_option( 'custom_dash_setting_information' ) ) ) {
        $dashsetting = get_option( 'custom_dash_setting_information' );
    }

		/** generate checkboxes main menu **/
        $mainmenu = '';
        for( $i = 0; $i < count( $menu ); $i++ ){ 
            $sel = '';
            if( $menu[ $i ][ 0 ] != '' ){
                if( !empty( $dashsetting ) ){
                    for( $x = 0; $x < count( $dashsetting ); $x++ ){ 
                        if( ( preg_replace( '/[^a-zäöü]/i', '',  $dashsetting[ $x ][ 0 ] ) ==  preg_replace( '/[^a-zäöü]/i', '',  explode( '<', $menu[ $i ][ 0 ] )[ 0 ] ) ) || ( preg_replace( '/[^a-zäöü]/i', '',  $dashsetting[ $x ][ 0 ] ) == preg_replace( '/[^a-zäöü]/i', '',  str_replace( ' ', '-', strtolower( explode( '<', $menu[ $i ][ 0 ] )[ 0 ] ) ) ) ) ){
                            $sel = 'checked="checked"';
                        }
                    }
                }
                $menustring = trim( explode( '<', $menu[ $i ][ 0 ] )[ 0 ] ) . '#' . $menu[ $i ][ 2 ] . '#' . $menu[ $i ][ 3 ];
                $mainmenu .= '<label for="mdashc' . $i . '" class="clabel"><input type="checkbox" name="mdash[]" id="mdashc' . $i . '" value="' . $menustring . '" ' . esc_html( $sel ) . ' form="dashform" data-id="'. $i . '" autocomplete="off">' . trim( explode( '<', $menu[ $i ][ 0 ] )[ 0 ] ) . '<span class="checkmark"></span></label>';
            }
        }    

		/** number generator **/
		$GLOBALS[ 'addproof' ] = [];	
		function quickboard_makeuniqueno(){
			$adder = mt_rand( 11, 99 );
				if( !in_array( $adder, $GLOBALS[ 'addproof' ] ) ){
					$GLOBALS[ 'addproof' ][] = $adder;
						return $adder;
				}
				else{
					quickboard_makeuniqueno();
				}
		}

		/** generate checkboxes sub menu **/
        $subMenu = '';
        if( !empty( $dashsetting ) ){
            for( $i = 0; $i < count( $dashsetting ); $i++ ){
                $sel = '';
                if( count( $dashsetting[ $i ] ) > 3 || array_key_exists( $dashsetting[ $i ][ 2 ], $submenu ) ){
                    $sel = 'checked="checked"';
                    for( $y = 0; $y < count( $dashsetting[ $i ] ); $y = $y + 3 ){
						$addme = quickboard_makeuniqueno();
                            $SubMenustring = trim( $dashsetting[ $i ][ $y ] ) . '#' . $dashsetting[ $i ][ ( $y + 1 ) ] . '#' . $dashsetting[ $i ][ ( $y + 2 ) ];
                            $subMenu .= '<label for="dashs' . ( $i + $y + $addme ) . '" class="clabel" data-id="' . str_replace( ' ', '', $dashsetting[ $i ][ $y ] ) . '"><input type="checkbox" name="dashs[]" id="dashs' . ( $i + $y + $addme ) . '" value="' . $SubMenustring . '" ' . esc_html( $sel ) . ' form="dashform" data-id="'. ( $i + $y + $addme ) . '" autocomplete="off">' . $dashsetting[ $i ][ $y ] . ' &rarr; ' . trim( $dashsetting[ $i ][ ( $y + 1 ) ]  ) . '<span class="checkmark"></span></label>';
                    }

                }
            }
        }
        
		/** list of saved pages / options **/
        $proofSub = [];
        if( !empty( $dashsetting ) ){
            for( $i = 0; $i < count( $dashsetting ); $i++ ){
                foreach( $dashsetting[ $i ] AS $dkey => $dval ){
                    if( $dval != '' ){
                        $proofSub[] = $dval;
                    }
                }   
            }
        }
		
	/** remove profile page from submenu **/
	function removeprofile( $array ){
		foreach( $array AS $keya => $vala ){
			foreach( $vala AS $keyb => $valb ){
				if( $keya == 'users.php' && $valb[ 2 ] == 'profile.php' ){
					unset( $array[ $keya ][ $keyb ] );
				}
			}
		}
		return $array;
	}
	$submenu = removeprofile( $submenu );
?>
<h1><?php _e( "Quickboard", "quickboard" ); ?></h1>
<h2><?php _e( "Einstellungen", "quickboard" ); ?></h2>
<input type="hidden" id="submenue" value="<?php if( 'administrator' == quickboard_settingsuserrole() ){ echo urlencode( serialize( $submenu ) ); } ?>" />
<input type="hidden" id="subproof" value='<?php if( 'administrator' == quickboard_settingsuserrole() ){ echo urlencode( serialize( $proofSub ) ); } ?>' />
    <div id="quickvorschau">
        <div class="qlinkwrap"><a href="#" ><?php _e( "BEISPIEL", "quickboard" ); ?></a></div><div class="qlinkwrap"><a href="#" ><?php _e( "BEISPIEL", "quickboard" ); ?></a></div><div class="qlinkwrap"><a href="#" ><?php _e( "BEISPIEL", "quickboard" ); ?><br><?php _e( "ZWEITE ZEILE", "quickboard" ); ?></a></div>
    </div>

			<div id="tabs" class="quick">
				<ul>
					<li><a href="#tabs-01" id="tabeins" ><?php _e( "Menü wählen", "quickboard" );?></a></li>
					<li><a href="#tabs-02" id="tabzwei" ><?php _e( "Hilfe / Workflow", "quickboard" );?></a></li>
					<li><a href="#tabs-03" id="tabdrei" ><?php _e( "PROVERSION", "quickboard" );?></a></li>
				</ul>
			<div id="tabs-01">
                <?php _e( "Menue-Punkte auswählen und abspeichern.", "quickboard" ); ?>
                <table id="dashtable">
					<tr>
						<td>
					<h3><?php _e( "Hauptnavigation", "quickboard" ); ?></h3></td><td ><h3><?php _e( "Subnavigation", "quickboard" ); ?></h3>
						</td>
					</tr>
					<tr>
						<td>
							<?php echo $mainmenu; ?>
						</td>
						<td class="untermenuetd"><div class="untermenu">
							<?php echo $subMenu; ?></div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							&nbsp;<input type="submit" name="del" class="button button-primary" value="<?php esc_attr_e( "Löschen", "quickboard" ); ?>" form="dashform" >
							<input type="submit" name="dashsubmit" class="button button-primary" value="<?php esc_attr_e( "Speichern", "quickboard" ); ?>" form="dashform" >
						</td>
					</tr>
                </table>
            </div>
			<div id="tabs-02">
				<?php include_once 'hilfe.php'; ?>
            </div>
			<div id="tabs-03">
				<?php include_once 'pro.php'; ?>
            </div>
            
</div>