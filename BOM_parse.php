<?php
/*
Opis: Skrypt służy do konwersji plików tekstowych z listą części (BOM)
			generowanych przez Solid Edge do formatu JSON
Data: 02/10/2017
Autor: Marcin Ogar
*/

/* ---- Wymogi dla pliku wejściowego ---- */

# Plik tekstowy kodowany w formacie UTF-8
# 1. kolumna to nazwa części 2. kolumna to ilość jej wystąpień w głównym złożeniu
# Rury muszą się znajdować w predefiniowanej tablicy w bibliotece funkcji
# Profile prostokątne nie mogą przekraczać dwucyfrowej szerokości ścianki
# Długość profili palonych musi być poprzedzona '-' (myślnik)
# Oznaczenie twardej stali 'T' w profilach palonych musi następować bezpośrednio pod długości cięcia

require 'BOM_parse_functions.php';

$input_arg = $argv[1];
$output_arg = $argv[2];

$bufor_in = array();
$bufor_out = array("{\r\n");

/* ---- Pojemniki na listy części poszczególnego typu
przechowywane w formacie string (JSON) ---- */

$surowce = [];

$surowce['rod'] = array();
$surowce['rect_rod'] = array();
$surowce['flat_bar'] = array();
$surowce['metal_sheet'] = array();
$surowce['round_tube'] = array();
$surowce['square_tube'] = array();
$surowce['rect_tube'] = array();
$surowce['cold_angle'] = array();
$surowce['hot_angle'] = array();
$surowce['bolt'] = array();
$surowce['nut'] = array();
$surowce['washer'] = array();
$surowce['std_parts'] = array();
$surowce['others'] = array();



/*	---- Odczyt danych wejściowych i załadunek do tablicy $bufor_in ---- */

	$plik_in = fopen($input_arg, 'r');

		do { 
		
			array_push($bufor_in, fgets($plik_in));
		
		} while (! feof($plik_in));
	
	fclose($plik_in);


	
/* ---- Przetwarzanie danych ---- */
	
	for($index = 0 ; $index < count($bufor_in) ; $index++){
		
		if ( is_header( $bufor_in[$index] ) ) {     // Nazwa Produktu
				
				populate_header( $bufor_in[$index] );
				
			}  elseif ( is_rod( $bufor_in[$index] ) ) {     // Pręt Okrągły
				
				populate_rod( $bufor_in[$index] );
				
			}  elseif ( is_rect_rod( $bufor_in[$index] ) ) {     // Pręt Prostokątny
				
				populate_rect_rod( $bufor_in[$index] );
				
			}  elseif ( is_flat_bar( $bufor_in[$index] ) ) {     // Płaskownik
				
				populate_flat_bar( $bufor_in[$index] );
				
			}  elseif ( is_metal_sheet( $bufor_in[$index] ) ) {     // Blacha
				
				populate_metal_sheet( $bufor_in[$index] );
				
			}  elseif ( is_round_tube( $bufor_in[$index] ) ) {     // Profil Okrągły
				
				populate_round_tube( $bufor_in[$index] );
				
			}  elseif ( is_square_tube( $bufor_in[$index] ) ) {     // Profil Kwadratowy
				
				populate_square_tube( $bufor_in[$index] );
				
			}  elseif ( is_rect_tube( $bufor_in[$index] ) ) {     // Profil Prostokątny
				
				populate_rect_tube( $bufor_in[$index] );
				
			}  elseif ( is_cold_angle( $bufor_in[$index] ) ) {     // Kątownik Zimnogięty
				
				populate_cold_angle( $bufor_in[$index] );
				
			}  elseif ( is_hot_angle( $bufor_in[$index] ) ) {     // Kątownik Gorącowalcowany
				
				populate_hot_angle( $bufor_in[$index] );
				
			}  elseif ( is_bolt( $bufor_in[$index] ) ) {     // Śruby
				
				populate_bolt( $bufor_in[$index] );
				
			}  /*  elseif ( is_nut( $bufor_in[$index] ) ) {
				
				populate_nut( $bufor_in[$index] );
				
			}    elseif ( is_washer( $bufor_in[$index] ) ) {
				
				populate_washer( $bufor_in[$index] );
				
			} */ else continue ;
		
	}
	
	
	
/* ---- Przygotowanie odstępów, tabulacji i opisów do końcowego wydruku ---- */

	// $key jest nazwanym indeksem w tablicy $surowce , $value jest 'aliasem' tablicy z pojedynczymi częściami	
	foreach ( $surowce as $key => &$value) {
		
		static $increment = 0;
		$increment++;
		
		// usunięcie przecinka z ostatniego wiersza listy
		if( ! empty($value) )  $value[count($value) - 1] = str_replace( ",\r\n" , "\r\n" , $value[count($value) - 1]);
		
		array_unshift( $value , "\t\r\n" , "\t\"" . $key . "\": [\r\n" , "\t\r\n" );
		if( $increment < count($surowce) ) array_push( $value , "\t\r\n" , "\t],\r\n" );
		else array_push( $value , "\t\r\n" , "\t]\r\n" ); // bez przecinka na końcu BOM
	} 
	unset($value, $key);
	
/* ---- Scalenie segmentów z poszczególnymi surowcami w formacie JSON
do integralnej listy materiałowej (BOM)  ---- */
	
	foreach( $surowce as $value) {
		$bufor_out = array_merge($bufor_out, $value);
	}
	unset($value);
	
	array_push($bufor_out, "}");
	
	
/* ---- Zapis do pliku przetworzonych danych w formacie JSON
zmagazynowanych w tablicy $bufor_out ---- */
	
	file_put_contents($output_arg, $bufor_out);
	
