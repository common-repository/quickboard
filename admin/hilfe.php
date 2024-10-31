<?php _e( 'Dashboard to Quickboard legt eine neue Benutzerrolle "Quickboard" an.', 'quickboard' ); ?><br>
<?php _e( 'Nur Benutzer mit dieser Rolle bekommen das Quickboard zu sehen.', 'quickboard' ); ?><br>
<p>
<?php 
_e( 'Unter "Menü wählen" wählen Sie zuerst die Hauptnavigation und dann die Unternavigation aus, die der Rolle "Quickboard" zugänglich sein soll.', 'quickboard' );
echo '<br>';
_e( 'Wenn Sie weitere Rechte nach dem Abspeichern für eine Unternavigation setzen wollen, klicken Sie zweimal langsam nacheinander auf die Hauptnavigation Option. Dadurch wird in der Unternavigation alle Navigationspunkte nachgeladen und können aktiviert werden.', 'quickboard' );
echo '<br><strong>';
_e( 'Hinweis:', 'quickboard' );  
echo '</strong><br>';
_e( 'Wir empfehlen vor dem Setzen neuer Navigationselemente die bisher gespeicherten zu löschen.', 'quickboard' );  
echo '<hr>';
_e( 'Folgende Befugnisse sind der Rolle "Quickboard" nach speichern der Optionsseiten zugeordnet:', 'quickboard' );  
?>
<ol>
<?php 
$alluserroles = get_role( 'quickboard' )->capabilities;
foreach( $alluserroles AS $userkey => $uservalue ){
	echo '<li>' . $userkey . '</li>';
}
?>
</ol>
<hr>
<h3>
<?php 
_e( 'Bekannte Probleme', 'quickboard' );
echo '</h3>';
_e( 'Einige Plugins bringen eigene Rechte mit oder basieren darauf, dass ein Administrator angemeldet ist. Mit Quickboard kann dann zwar ein Link im Custom-Dashboard erstellt werden, die entsprechende Seite wird aber nicht angezeigt und es erfolgt eine Fehlermeldung. Daher ist es sinnvoll, das Quickboard mit einem Nutzer mit der Rolle "Quickboard" zu testen.', 'quickboard' ); 
echo '<br><strong>';
_e( 'Meist tritt dies bei SEO Plugins auf!', 'quickboard' );
?>
</strong>
