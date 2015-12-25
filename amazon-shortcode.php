<?php
/**
 * Plugin Name: Amazon Shortcode
 * Plugin URI:  
 * Description: Ein [amazon] Shortcode in WordPress um Amazon Produkte zu verlinken (Für Amazon Deutschland). Beispielnutzung: [amazon]B00NGOCP64[/amazon] [amazon num=2]iphone[/amazon]
 * Author:      Philipp Rackevei
 * Author URI:  
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

	return '<strong>'.$title[$i].'</strong><br>Neupreis bei Amazon.de ab '.$priceLowNew[$i].'<br><p align="center"><a href="'.$url[$i].'" target="_blank">Auf Amazon.de</a></p>';
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
            ->API_KEY('x')
            ->SECRET_KEY('x')
            ->associate_tag('x')
            ->locale('de')
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

