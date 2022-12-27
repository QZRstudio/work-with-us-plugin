<?php
/*
	Plugin Name: IlPost Plugin
	Plugin URI: https://qzrstudio.com
	Description: Il plugin realizzato sulle specifiche indicate nella repository di GitHub
	Version: 1.0
	Author: QZR studio
	Author URI: https://qzrstudio.com
	License: GPL2
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* 
	RICHIESTA
	La divisione marketing del Post richiede che immerso all'interno di un singolo articolo sia 
	presente una call to action editabile in formato html direttamente su wp-admin solo dagli utenti 
	di ruolo administrator tramite pagina creata ad-hoc presente nel menù sotto la sezione Impostazioni
	La call to action dovrà essere presente dopo 4 paragrafi e solo per gli articoli che sono stati taggati con il tag governo

	SOLUZIONE
	Per risolvere il problema abbiamo creato un plugin che permette di creare una call to action per ogni tag, non solo per il tag governo.
	In questo modo in futuro sarà possibile creare una call to action per ogni tag che si desidera, ma non solo!
	E' possibile anche dichiarare diverse regole di injection a seconda del tag, per esempio:

	- dopo il primo paragrafo
	- dopo il secondo paragrafo
	- dopo il terzo h2
	- dopo l'elemento .classe
	- ...

	Quando abbiamo una richiesta ci poniamo prima sempre delle domande che aiutano sia lo sviluppo,
	sia la manutenzione del codice, sia la sua evoluzione nel tempo... e poi è molto più divertente no?

	ps: i commenti sono stati inseriti in inglese, poiché spesso l'utilizzo di termini tecnici non sono facilmente
	traducibili in italiano o peggio ancora possono risultare ambigui.
*/

// How to inject the CTA to the content? Here are all the rules callbacks
require_once plugin_dir_path( __FILE__ ) . 'includes/rules.php';

// All the function to get the CTA data and manipulate it
require_once plugin_dir_path( __FILE__ ) . 'includes/functions.php';

// The admin page
require_once plugin_dir_path( __FILE__ ) . 'includes/admin/settings-cta-editor.php';

// The frontend filter that inject the CTA
require_once plugin_dir_path( __FILE__ ) . 'includes/frontend/filter.php';

// Add the admin menu
function ilpost_plugin_enqueue_admin_style() {
	
	// Enqueue the admin style only if we are in the plugin page
	// This is a good practice to avoid loading the style in all the admin pages
	// and to avoid conflicts with other plugins or core page styles

	if (isset($_GET['page']) && $_GET['page'] == 'ilpost-plugin') {
		wp_enqueue_style( 'ilpost-plugin-admin-style', plugin_dir_url( __FILE__ ) . 'assets/css/admin.css' );
		wp_enqueue_script( 'ilpost-plugin-admin-script', plugin_dir_url( __FILE__ ) . 'assets/js/admin.js', array('jquery'), '1.0.0', true );
	}
}
add_action( 'admin_enqueue_scripts', 'ilpost_plugin_enqueue_admin_style' );