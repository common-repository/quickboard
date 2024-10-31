<?php
	
	/* remove footer info */
	function quickboard_remove_footer_admin () {
		echo '';
	}
	 
	add_filter( 'admin_footer_text', 'quickboard_remove_footer_admin');
	remove_filter( 'update_footer', 'core_update_footer' ); 
	
	/* add CSS file */
	wp_enqueue_style( 'quickboard-css',  QUICKBOARDPLUGINURL . 'includes/css/quickboard.css' );	

	
		/* generate buttons for menu links */
		function quickboard_makecustomdashbuttons(){
			$dashsetting = [];
			
			if ( !empty( get_option( 'custom_dash_setting_information' ) ) ) {
				$dashsetting = get_option( 'custom_dash_setting_information' );
			}

			function uppercasewords( $words ){
				$textsearch = [ '-', '_' ];
				$singlewords = ucwords( str_replace( $textsearch, ' ', $words ) );
				$newstr = str_replace( ' ', '<br>', $singlewords );
					return $newstr;
			}

            $showlink = '';
            $showlinkarray = [];
			for( $i = 0; $i < count( $dashsetting ); $i++ ){
				$linktarget = '';
				$linkname = '';

                    if( count( $dashsetting[ $i ] ) == 3 ){
						
                        if( stripos( $dashsetting[ $i ][ 2 ], '.php' ) === false  ){
							$linktarget = 'admin.php?page=' . $dashsetting[ $i ][ 1 ];
							$linkname = $dashsetting[ $i ][ 2 ];
						}
						
						elseif( stripos( $dashsetting[ $i ][ 2 ], '.php' ) !== false ){
							$linktarget = $dashsetting[ $i ][ 2 ];
							$linkname = $dashsetting[ $i ][ 0 ] . '<br>' . $dashsetting[ $i ][ 1 ];
                        }
                        $showlink .= '<div class="qlinkwrap"><a href=' .  admin_url() . $linktarget . '>' . uppercasewords( $linkname ) . '</a></div>';
					}
					
                    if( count( $dashsetting[ $i ] ) > 3 ){
                        for( $y = 0; $y < count( $dashsetting[ $i ] ); $y = $y + 3 ){
							if( stripos( $dashsetting[ $i ][ ( $y + 2 ) ], '.php' ) !== false  ){
								$linktarget = $dashsetting[ $i ][ ( $y + 2 ) ];
								$linkname = $dashsetting[ $i ][ $y ] . '<br>' . $dashsetting[ $i ][ ( $y + 1 ) ];
								$showlink .= '<div class="qlinkwrap"><a href=' .  admin_url() . $linktarget . '>' . $linkname . '</a></div>';
							}
							
							if( stripos( $dashsetting[ $i ][ ( $y + 2 ) ], '.php' ) === false  ){
								$linktarget ='admin.php?page=' .  strtolower( $dashsetting[ $i ][ ( $y + 2 ) ] );
								$linkname = $dashsetting[ $i ][ $y  ] . '<br>' . $dashsetting[ $i ][ ( $y + 1 ) ]; 
										$showlinkarray[] = [ $linktarget, $linkname ];
							}
						}
						
							for( $z = 0; $z < count( $showlinkarray ); $z++ ){
								if( @$showlinkarray[ $z ][ 0 ] == @$showlinkarray[ ( $z + 1 ) ][ 0 ] ){
									unset( $showlinkarray[ $z ] );
								}
							}
						
                    }
            }
							foreach( $showlinkarray AS $key => $value ){ 
								$showlink .= '<div class="qlinkwrap"><a href=' .  admin_url() . $value[0] . '>' . $value[ 1 ] . '</a></div>';
							}
			return $showlink;
		}

?>
<div class="wrap about-wrap">
	<div class="wtf-cdash-wrapper">
		<?php  
		if( is_user_logged_in() ) {
				$user = wp_get_current_user();
				$roles = ( array ) $user->roles;
				if( 'quickboard' ==  $roles[ 0 ] ){ 
					flush(); 
					usleep( 300000 ); 
					echo quickboard_makecustomdashbuttons(); 
				}
		}
		?>
	</div>
<div class="devlink">Proudly presented by <a href="https://fab22.com" target="_blank">FAB22.com</a></div>
</div>