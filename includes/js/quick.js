    var $ = jQuery;
    $( document ).ready( function(){
        /** path to plugin **/
		var pdpfad = quick_strings.pluginpfad;
        
		/** tabs **/
		$( function() {
			$( "#tabs" ).tabs();
		} );
		setTimeout( function(){ $( '#tabs' ).css( { 'display' : 'block' } ); }, 500 );
		
		/** hide / show preview field **/
        $( '#tabeins' ).on( 'click', function(){
            $( '#quickvorschau' ).fadeIn( 200 ).css( 'display', 'grid' );
        });
        
        $( '#tabzwei, #tabdrei' ).on( 'click', function(){
            $( '#quickvorschau' ).fadeOut( 400);
        });

    } );