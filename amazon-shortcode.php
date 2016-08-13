<?php
/**
 * Plugin Name: Amazon Shortcode
 * Plugin URI:  https://github.com/philipp-r/amazon-shortcode
 * Description: Ein [amazon] Shortcode in WordPress um Amazon Produkte zu verlinken (Für Amazon Deutschland). Beispielnutzung: [amazon]B00NGOCP64[/amazon] [amazon num=2]iphone[/amazon]
 * Author:      Philipp
 * Author URI:  http://wpblog.eu/
 * Version:     0.1
 */

/*
SHORTCODE USAGE
[amazon]iphone[/amazon]
WITH NUMBER OF PRODUCTS TO DISPLAY
[amazon num=1]B00NGOCP64[/amazon]
*/



// Define the Layout for $boxContent
function amazonLayout($i, Amazon $amazon) {
    //Get your amazon attributes here
	$image = $amazon->get()->image("LargeImage");
	$priceLowNew = $amazon->get()->price("lowestNew");
	$priceUsed = $amazon->get()->price("lowestUsed");
	$offersNew = $amazon->get()->numOffers("TotalNew");
	$offersUsed = $amazon->get()->numOffers("TotalUsed");
	$url = $amazon->get()->url();
	$title = $amazon->get()->title();
	$desc = $amazon->get()->description();

	return '<strong>'.$title[$i].'</strong><br>Neupreis bei Amazon ab '.$priceLowNew[$i].'<br><p align="center"><a href="'.$url[$i].'" target="_blank">Auf Amazon</a></p>';
}




function amazonShortcode($atts, $content) {

	// extract attributes
	extract( shortcode_atts( array( 'num' => '' ), $atts ) );
	// We get $num variable
	if( empty( $num ) ){
		$num = 5;
	}
	

	
	// define HTML prefix and suffix for displayed App Store Box
	$preBox = '<hr style="border: 0; height: 0; border-top: 1px solid rgba(0, 0, 0, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.3);">';
	$sufBox = '<hr style="border: 0; height: 0; border-top: 1px solid rgba(0, 0, 0, 0.1); border-bottom: 1px solid rgba(255, 255, 255, 0.3);">';
	



	// Require the class file
	// From https://github.com/chopin2256/Amazon
	require_once('Amazon.php');
	
	//Run Amazon
    $amazon = new Amazon();  //Instantiate Amazon object
    $kw = $content;  //Set keyword
    $cnt = $num;  //Set amazon max results, up to 10
    
    //Set config options
    $amazon->config()
            ->API_KEY(get_option('amazon_shortc_apikey'))
            ->SECRET_KEY(get_option('amazon_shortc_secretkey'))
            ->associate_tag(get_option('amazon_shortc_associatetag'))
            ->locale(get_option('amazon_shortc_locale'))
            ->maxResults($cnt);

    //Search for keyword
    $amazon->search($kw);

    //Loop through array in for loop to save your Amazon results
    for ($i = 0; $i < $cnt; $i++) {
        $result .= amazonLayout($i, $amazon).$sufBox;
    }

    //Clear amazon object
    $amazon->clear();

    //Set and return results, in this case, 5 product titles
    return $preBox.$result;


}
add_shortcode("amazon", "amazonShortcode");









/**
 * Adds the settings for this plugin to WordPress
 *
 * Settings: amazon_shortc_
 * 	apikey, secretkey, associatetag, locale 
 */
 
function amazon_shortc_settings_api_init() {
	// Add the new section to writing settings so we can add our fields to it
	add_settings_section(
		'amazon_shortc_section', 'Amazon Shortcode', 'amazon_shortc_section_callback', 'writing'
	);
	
 	// Add amazon_shortc_apikey
 	add_settings_field(
		'amazon_shortc_apikey', 'Amazon API Key', 'amazon_shortc_apikey_callback', 'writing', 'amazon_shortc_section'
	);
 	// Add amazon_shortc_secretkey
 	add_settings_field(
		'amazon_shortc_secretkey', 'Amazon Secret Key', 'amazon_shortc_secretkey_callback', 'writing', 'amazon_shortc_section'
	);
 	// Add amazon_shortc_associatetag
 	add_settings_field(
		'amazon_shortc_associatetag', 'Associate Tag', 'amazon_shortc_associatetag_callback', 'writing', 'amazon_shortc_section'
	);
 	// Add amazon_shortc_locale
 	add_settings_field(
		'amazon_shortc_locale', 'Locale', 'amazon_shortc_locale_callback', 'writing', 'amazon_shortc_section'
	);
 	
 	// Register the settings
 	register_setting( 'writing', 'amazon_shortc_apikey' );
 	register_setting( 'writing', 'amazon_shortc_secretkey' );
 	register_setting( 'writing', 'amazon_shortc_associatetag' );
 	register_setting( 'writing', 'amazon_shortc_locale' );
 } 
 
 add_action( 'admin_init', 'amazon_shortc_settings_api_init' );
 
  
// Adds a info text to the section mcc_settings_section 
// This function will be run at the start of our section
function amazon_shortc_section_callback() {
	echo '<p>This plugin adds a shortcode to WordPress to include Amazon products into posts.</p>';
}
 
// Create a text input field for amazon_shortc_apikey
function amazon_shortc_apikey_callback() {
	echo '<input name="amazon_shortc_apikey" id="amazon_shortc_apikey" type="input" value="' . get_option( 'amazon_shortc_apikey' ) . '" class="code" /> <br /> Your API key';
}
// Create a text input field for amazon_shortc_secretkey
function amazon_shortc_secretkey_callback() {
	echo '<input name="amazon_shortc_secretkey" id="amazon_shortc_secretkey" type="input" value="' . get_option( 'amazon_shortc_secretkey' ) . '" class="code" /> <br /> Your Secret Key';
}
// Create a text input field for amazon_shortc_associatetag
function amazon_shortc_associatetag_callback() {
	echo '<input name="amazon_shortc_associatetag" id="amazon_shortc_associatetag" type="input" value="' . get_option( 'amazon_shortc_associatetag' ) . '" class="code" /> <br /> Amazon associate tag.';
}
// Create a text input field for amazon_shortc_locale
function amazon_shortc_locale_callback() {
	echo '<input name="amazon_shortc_locale" id="amazon_shortc_locale" type="input" value="' . get_option( 'amazon_shortc_locale' ) . '" class="code" /> <br /> Country code (de, ...)';
}


