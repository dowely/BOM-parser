<?php

$RURY = array(

	"17" => "17,2", 
	"21" => "21,3", 
	"22" => "22", 
	"23" => "23", 
	"25" => "25", 
	"26" => "26,9", 
	"27" => "27,3", 
	"30" => "30", 
	"32" => "32", 
	"33" => "33,7", 
	"35" => "35",
	"38" => "38", 
	"42" => "42,4",
	"44" => "44,5",
	"48" => "48,3",
	"50" => "50,8",
	"51" => "51",
	"52" => "52",
	"54" => "54",
	"70" => "70",
	"76" => "76,1",
	"88" => "88,9"
	
	);

/* ---- Header processor ---- */

function is_header($line){	
	if ( stripos($line, 'Zestawienie') && stripos($line, 'Dla') ) return true;
	return false;
}

function populate_header($line){
	
	global  $bufor_out;
	
	$product_name = rtrim(str_ireplace('Dla ', '', stristr($line, 'Dla')));
	$product_name_quoted = str_pad($product_name, strlen($product_name) + 2, '"', STR_PAD_BOTH); 
	$header_line = "\t\"HEADER\": " . $product_name_quoted . ",\r\n";
	
	array_push($bufor_out, $header_line);
}

/* ---- Rod processor ---- */

function is_rod($line){	
	if ( preg_match( '/^PR/' , $line) && substr_count($line, '-', 0, strpos($line, ' ')) !== 3 ) return true; 
	return false;
}

function populate_rod($line){
	
	global $surowce;
	
	$first_dash = strpos($line, '-');
	$second_dash = strpos($line, '-', $first_dash + 1);
	$first_space = strpos($line, ' ');
	$first_tab = strpos($line, "\t");
	$second_space = strpos($line, ' ', $first_tab + 1);
	
	$DIAMETER = '"' . substr($line, 2, $first_dash - 2) . '"';
	$LENGTH = '"' . substr($line, $first_dash + 1 , $second_dash - ($first_dash + 1) ) . '"';
	$STEEL = '"' . substr($line, $second_dash + 1 , $first_space - ($second_dash + 1) ) . '"';
	$QTY = '"' .substr($line, $first_tab + 1, $second_space - ($first_tab + 1)) . '"';

	$entry_line = "\t\t{\"DIAMETER\": " . $DIAMETER . ", \"LENGTH\": " . $LENGTH . ", \"STEEL\": " . $STEEL . ", \"QTY\": " . $QTY  . "},\r\n";
	
	array_push($surowce['rod'], $entry_line);
	
}

/* ---- Rectangle Rod processor ---- */

function is_rect_rod($line){	
	if ( preg_match( '/^PR\d{1,2}-\d{1,2}-\d+-\w/' , $line) /*&& substr_count($line, '-', 0, strpos($line, ' ')) !== 3 */) return true; 
	return false;
}

function populate_rect_rod($line){
	
	global $surowce;
	
	$first_dash = strpos($line, '-');                     
	$second_dash = strpos($line, '-', $first_dash + 1 );  
	$third_dash = strpos($line, '-', $second_dash + 1 );  
	$first_space = strpos($line, ' ');                    
	$first_tab = strpos($line, "\t");                     
	$second_space = strpos($line, ' ', $first_tab + 1);   
	
	$THICKNESS = '"' . substr($line, 2, $first_dash - 2 ) . '"';
	$WIDTH = '"' . substr($line, $first_dash + 1 , $second_dash - ($first_dash + 1) ) . '"';
	$LENGTH = '"' . substr($line, $second_dash + 1 , $third_dash - ($second_dash + 1) ) . '"';
	$STEEL = '"' . substr($line, $third_dash + 1 , $first_space - ($third_dash + 1) ) . '"';
	$QTY = '"' . substr($line, $first_tab + 1, $second_space - ($first_tab + 1)) . '"';

	$entry_line = "\t\t{\"THICKNESS\": " . $THICKNESS . ", \"WIDTH\": " . $WIDTH . ", \"LENGTH\": " . $LENGTH . ", \"STEEL\": " . $STEEL . ", \"QTY\": " . $QTY  . "},\r\n";
	
	array_push($surowce['rect_rod'], $entry_line);
	
}

/* ---- Flat Bar processor ---- */

function is_flat_bar($line){	
	if ( strpos($line, 'PŁ') !== false ) return true; // alternatywny sposób rozpoznawania surowca ( vs preg_mach())
	return false;
}

function populate_flat_bar($line){
	
	global $surowce;
	
	$first_dash = strpos($line, '-') - 1 ;                     //odjęto 1 dla kompensacji polskiego znaku "Ł"
	$second_dash = strpos($line, '-', $first_dash + 2 ) - 1 ;  //odjęto 1 dla kompensacji polskiego znaku "Ł"
	$third_dash = strpos($line, '-', $second_dash + 2 ) - 1 ;  //odjęto 1 dla kompensacji polskiego znaku "Ł"
	$first_space = strpos($line, ' ') - 1 ;                    //odjęto 1 dla kompensacji polskiego znaku "Ł"
	$first_tab = strpos($line, "\t") - 1 ;                     //odjęto 1 dla kompensacji polskiego znaku "Ł"
	$second_space = strpos($line, ' ', $first_tab + 2) - 1 ;   //odjęto 1 dla kompensacji polskiego znaku "Ł"
	
	$THICKNESS = '"' . substr($line, 2, $first_dash - 1 ) . '"';
	$WIDTH = '"' . substr($line, $first_dash + 2 , ($second_dash + 1) - ($first_dash + 2) ) . '"';
	$LENGTH = '"' . substr($line, $second_dash + 2 , ($third_dash + 1) - ($second_dash + 2) ) . '"';
	$STEEL = '"' . substr($line, $third_dash + 2 , $first_space - ($third_dash + 1) ) . '"';
	$QTY = '"' . substr($line, $first_tab + 2, $second_space - ($first_tab + 1 )) . '"';

	$entry_line = "\t\t{\"THICKNESS\": " . $THICKNESS . ", \"WIDTH\": " . $WIDTH . ", \"LENGTH\": " . $LENGTH . ", \"STEEL\": " . $STEEL . ", \"QTY\": " . $QTY . "},\r\n";
	
	array_push($surowce['flat_bar'], $entry_line);
	
}

/* ---- Metal Sheet processor ---- */

function is_metal_sheet($line){	
	if ( preg_match( '/^BL/' , $line) ) return true;
	return false;
}

function populate_metal_sheet($line){
	
	global $surowce;
	
	$first_space = strpos($line, ' ');  
	$first_dash = strpos($line, '-');
	$second_dash = strpos($line, '-', $first_dash + 1);
	$third_dash = strpos($line, '-', $second_dash + 1);
	$first_tab = strpos($line, "\t");
	$second_space = strpos($line, ' ', $first_tab + 1);
	
	//sprawdzenie czy blacha jest palona laserowo
	$fourth_dash = ( stripos($line, 'Laser') === false ) ? $first_space : strpos($line, '-', $third_dash + 1);
	$LASER = ( stripos($line, 'Laser') === false ) ? '"No"' : '"Yes"' ;
	
	$THICKNESS = '"' . substr($line, 2, $first_dash - 2 ) . '"';
	$WIDTH = '"' . substr($line, $first_dash + 1 , $second_dash - ($first_dash + 1) ) . '"';
	$LENGTH = '"' . substr($line, $second_dash + 1 , $third_dash - ($second_dash + 1) ) . '"';
	$STEEL = '"' . substr($line, $third_dash + 1 , $fourth_dash - ($third_dash + 1) ) . '"';
	$QTY = '"' . substr($line, $first_tab + 1, $second_space - ($first_tab + 1)) . '"';

	$entry_line = "\t\t{\"THICKNESS\": " . $THICKNESS . ", \"WIDTH\": " . $WIDTH . ", \"LENGTH\": " . $LENGTH . ", \"STEEL\": " . $STEEL . ", \"LASER\": " . $LASER . ", \"QTY\": " . $QTY  . "},\r\n";
	
	array_push($surowce['metal_sheet'], $entry_line);
	
}

/* ---- Round Tube processor ---- */

function is_round_tube($line){	
	if ( preg_match( '/^PO\d|K\d+[A-Z]PO\d|[0-9_]+[A-Z]PO\d/' , $line) ) return true; 
	return false;
}

function populate_round_tube($line){
	
	global $surowce, $RURY;
	
	if( preg_match( '/^PO\d/' , $line) ) { // Cięcie klasyczne
		
		$first_dash = strpos($line, '-');                     
		$second_dash = strpos($line, '-', $first_dash + 1 );  
		$third_dash = strpos($line, '-', $second_dash + 1 );  
		$first_space = strpos($line, ' ');
		$first_tab = strpos($line, "\t");
		$second_space = strpos($line, ' ', $first_tab + 1);
		
		$THICKNESS = '"' . substr($line, $first_dash + 1 , $second_dash - ($first_dash + 1) ) . '"';
		$OUTTER_DIAMETER = '"' . substr($line, 2, $first_dash - 2 ) . '"';
		$LENGTH = '"' . substr($line, $second_dash + 1 , $third_dash - ($second_dash + 1) ) . '"';
		$STEEL = '"' . substr($line, $third_dash + 1 , $first_space - ($third_dash + 1) ) . '"';
		$LASER = '"No"';
		$QTY = '"' . substr($line, $first_tab + 1, $second_space - ($first_tab + 1)) . '"';

		$entry_line = "\t\t{\"OUTTER_DIAMETER\": " . $OUTTER_DIAMETER . ", \"THICKNESS\": " . $THICKNESS . ", \"LENGTH\": " . $LENGTH . ", \"STEEL\": " . $STEEL . ", \"LASER\": " . $LASER . ", \"QTY\": " . $QTY  . "},\r\n";
		
	} else { // Cięcie laserowe
		
		$first_dash = strpos($line, '-');
		$first_space = strpos($line, ' ');
		$first_tab = strpos($line, "\t");
		$second_space = strpos($line, ' ', $first_tab + 1);
		
		if( strlen( $RURY[substr($line, strpos($line, 'PO') + 2, 2)] ) === 2 ) {
			$OD_digits = 2;
		} else $OD_digits = strlen( $RURY[substr($line, strpos($line, 'PO') + 2, 2)] ) - 1 ; 
		
		$thickness_digits = strlen(substr($line, strpos($line, 'PO') + 2 + $OD_digits , $first_dash -  (strpos($line, 'PO') + 2 + $OD_digits )));
		
		$THICKNESS = ($thickness_digits === 1) ? '"' . substr($line, strpos($line, 'PO') + 2 + $OD_digits, 1) . '"' : '"' . substr($line, strpos($line, 'PO') + 2 + $OD_digits, 1) . ',' . substr($line, strpos($line, 'PO') + 3 + $OD_digits, 1) . '"';
		
		$OUTTER_DIAMETER = (isset($RURY[substr($line, strpos($line, 'PO') + 2, 2)])) ? '"' . $RURY[substr($line, strpos($line, 'PO') + 2, 2)] . '"' : '"Brak rury"';
		$LENGTH = '"' . rtrim(substr($line, $first_dash + 1 , $first_space - ($first_dash + 1)), 'A..Z') . '"';
		$STEEL = ( preg_match( '/^\wT/' , $line) ) ? '"S355J2H"' : '"S235JR"';
		$LASER = '"Yes"';
		$QTY = '"' . substr($line, $first_tab + 1, $second_space - ($first_tab + 1)) . '"';
		
		$entry_line = "\t\t{\"OUTTER_DIAMETER\": " . $OUTTER_DIAMETER . ", \"THICKNESS\": " . $THICKNESS . ", \"LENGTH\": " . $LENGTH . ", \"STEEL\": " . $STEEL . ", \"LASER\": " . $LASER . ", \"QTY\": " . $QTY  . "},\r\n";
		
	}
	
	array_push($surowce['round_tube'], $entry_line);
	
}

/* ---- Square Tube processor ---- */

function is_square_tube($line){	
	if ( preg_match( '/^PK\d|K\d+[A-Z]PK\d|[0-9_]+[A-Z]PK\d/' , $line) ) return true; 
	return false;
}

function populate_square_tube($line){
	
	global $surowce;
	
	if( preg_match( '/^PK\d/' ,$line) ) {
		
		$first_dash = strpos($line, '-');                     
		$second_dash = strpos($line, '-', $first_dash + 1 );  
		$third_dash = strpos($line, '-', $second_dash + 1 );  
		$first_space = strpos($line, ' ');
		$first_tab = strpos($line, "\t");
		$second_space = strpos($line, ' ', $first_tab + 1);
		
		$THICKNESS = '"' . substr($line, $first_dash + 1 , $second_dash - ($first_dash + 1) ) . '"';
		$DIM_A = '"' . substr($line, 2, $first_dash - 2 ) . '"';
		$LENGTH = '"' . substr($line, $second_dash + 1 , $third_dash - ($second_dash + 1) ) . '"';
		$STEEL = '"' . substr($line, $third_dash + 1 , $first_space - ($third_dash + 1) ) . '"';
		$LASER = '"No"';
		$QTY = '"' . substr($line, $first_tab + 1, $second_space - ($first_tab + 1)) . '"';

		$entry_line = "\t\t{\"THICKNESS\": " . $THICKNESS . ", \"DIM_A\": " . $DIM_A . ", \"LENGTH\": " . $LENGTH . ", \"STEEL\": " . $STEEL . ", \"LASER\": " . $LASER . ", \"QTY\": " . $QTY  . "},\r\n";
		
	} else {
		
		$first_tab = strpos($line, "\t");
		$second_space = strpos($line, ' ', $first_tab + 1);
		
		preg_match( '/(?<=.PK[1-9]\d)[1-9]+/' , $line , $THICKNESS_arr );
		preg_match( '/(?<=.PK)[1-9]\d/' , $line , $DIM_A_arr );
		preg_match( '/((?<=.PK\d{3}-)|(?<=.PK\d{4}-))\d+/' , $line , $LENGTH_arr );
		
		$THICKNESS = ( strlen($THICKNESS_arr[0]) > 1 ) ? substr($THICKNESS_arr[0],0,1) . "," . substr($THICKNESS_arr[0],1,1) : $THICKNESS_arr[0];
		
		$STEEL = ( preg_match( '/\d+T/' , $line) ) ? '"S355J2H"' : '"S235JR"';
		$LASER = '"Yes"';
		$QTY = '"' . substr($line, $first_tab + 1, $second_space - ($first_tab + 1)) . '"';
		
		$entry_line = "\t\t{\"THICKNESS\": " . "\"" . $THICKNESS . "\"" . ", \"DIM_A\": " . "\"" . $DIM_A_arr[0] . "\"" . ", \"LENGTH\": " . "\"" . $LENGTH_arr[0] . "\"" . ", \"STEEL\": " . $STEEL . ", \"LASER\": " . $LASER . ", \"QTY\": " . $QTY  . "},\r\n";
		
	}
	
	array_push($surowce['square_tube'], $entry_line);
	
}

/* ---- Rectangular Tube processor ---- */

function is_rect_tube($line){	
	if ( preg_match( '/^PP\d|K\d+[A-Z]PP\d|[0-9_]+[A-Z]PP\d/' , $line) ) return true; 
	return false;
}

function populate_rect_tube($line){
	
	global $surowce;
	
	if( preg_match( '/^PP\d/' ,$line) ) {
		
		$first_dash = strpos($line, '-');                     
		$second_dash = strpos($line, '-', $first_dash + 1 );  
		$third_dash = strpos($line, '-', $second_dash + 1 );
		$fourth_dash = strpos($line, '-', $third_dash + 1 );		
		$first_space = strpos($line, ' ');
		$first_tab = strpos($line, "\t");
		$second_space = strpos($line, ' ', $first_tab + 1);
		
		$THICKNESS = '"' . substr($line, $second_dash + 1 , $third_dash - ($second_dash + 1) ) . '"';
		$DIM_A = '"' . substr($line, 2, $first_dash - 2 ) . '"';
		$DIM_B = '"' . substr($line, $first_dash + 1 , $second_dash - ($first_dash + 1) ) . '"';
		$LENGTH = '"' . substr($line, $third_dash + 1 , $fourth_dash - ($third_dash + 1) ) . '"';
		$STEEL = '"' . substr($line, $fourth_dash + 1 , $first_space - ($fourth_dash + 1) ) . '"';
		$LASER = '"No"';
		$QTY = '"' . substr($line, $first_tab + 1, $second_space - ($first_tab + 1)) . '"';

		$entry_line = "\t\t{\"THICKNESS\": " . $THICKNESS . ", \"DIM_A\": " . $DIM_A . ", \"DIM_B\": " . $DIM_B . ", \"LENGTH\": " . $LENGTH . ", \"STEEL\": " . $STEEL . ", \"LASER\": " . $LASER . ", \"QTY\": " . $QTY  . "},\r\n";
		
	} else {
		
		$first_tab = strpos($line, "\t");
		$second_space = strpos($line, ' ', $first_tab + 1);
		
		preg_match( '/(?<=.PP\d{4})[1-9]+/' , $line , $THICKNESS_arr );
		preg_match( '/(?<=.PP)[1-9]\d/' , $line , $DIM_A_arr );
		preg_match( '/(?<=.PP[1-9]\d)[1-9]\d/' , $line , $DIM_B_arr );
		preg_match( '/((?<=.PP\d{5}-)|(?<=.PP\d{6}-))\d+/' , $line , $LENGTH_arr );
		
		$THICKNESS = ( strlen($THICKNESS_arr[0]) > 1 ) ? substr($THICKNESS_arr[0],0,1) . "," . substr($THICKNESS_arr[0],1,1) : $THICKNESS_arr[0];
		
		$STEEL = ( preg_match( '/\d+T/' , $line) ) ? '"S355J2H"' : '"S235JR"';
		$LASER = '"Yes"';
		$QTY = '"' . substr($line, $first_tab + 1, $second_space - ($first_tab + 1)) . '"';
		
		$entry_line = "\t\t{\"THICKNESS\": " . "\"" . $THICKNESS . "\"" . ", \"DIM_A\": " . "\"" . $DIM_A_arr[0] . "\"" . ", \"DIM_B\": " . "\"" . $DIM_B_arr[0] . "\"" . ", \"LENGTH\": " . "\"" . $LENGTH_arr[0] . "\"" . ", \"STEEL\": " . $STEEL . ", \"LASER\": " . $LASER . ", \"QTY\": " . $QTY  . "},\r\n";
		
	}
	
	array_push($surowce['rect_tube'], $entry_line);
	
}

/* ---- Cold Angle processor ---- */

function is_cold_angle($line){	
	if ( preg_match( '/^KTZ\d|K\d+[A-Z]KTZ\d|[0-9_]+[A-Z]KTZ\d/' , $line) ) return true; 
	return false;
}

function populate_cold_angle($line){
	
	global $surowce;
	
	if( preg_match( '/^KTZ\d/' ,$line) ) {
		
		$first_dash = strpos($line, '-');                     
		$second_dash = strpos($line, '-', $first_dash + 1 );  
		$third_dash = strpos($line, '-', $second_dash + 1 );
		$fourth_dash = strpos($line, '-', $third_dash + 1 );		
		$first_space = strpos($line, ' ');
		$first_tab = strpos($line, "\t");
		$second_space = strpos($line, ' ', $first_tab + 1);
		
		$THICKNESS = '"' . substr($line, $second_dash + 1 , $third_dash - ($second_dash + 1) ) . '"';
		$DIM_A = '"' . substr($line, 3, $first_dash - 3 ) . '"';
		$DIM_B = '"' . substr($line, $first_dash + 1 , $second_dash - ($first_dash + 1) ) . '"';
		$LENGTH = '"' . substr($line, $third_dash + 1 , $fourth_dash - ($third_dash + 1) ) . '"';
		$STEEL = '"' . substr($line, $fourth_dash + 1 , $first_space - ($fourth_dash + 1) ) . '"';
		$LASER = '"No"';
		$QTY = '"' . substr($line, $first_tab + 1, $second_space - ($first_tab + 1)) . '"';

		$entry_line = "\t\t{\"THICKNESS\": " . $THICKNESS . ", \"DIM_A\": " . $DIM_A . ", \"DIM_B\": " . $DIM_B . ", \"LENGTH\": " . $LENGTH . ", \"STEEL\": " . $STEEL . ", \"LASER\": " . $LASER . ", \"QTY\": " . $QTY  . "},\r\n";
		
	} else {
		
		$first_tab = strpos($line, "\t");
		$second_space = strpos($line, ' ', $first_tab + 1);
		
		preg_match( '/(?<=.KTZ\d{4})[1-9]+/' , $line , $THICKNESS_arr );
		preg_match( '/(?<=.KTZ)[1-9]\d/' , $line , $DIM_A_arr );
		preg_match( '/(?<=.KTZ[1-9]\d)[1-9]\d/' , $line , $DIM_B_arr );
		preg_match( '/((?<=.KTZ\d{5}-)|(?<=.KTZ\d{6}-))\d+/' , $line , $LENGTH_arr );
		
		$THICKNESS = ( strlen($THICKNESS_arr[0]) > 1 ) ? substr($THICKNESS_arr[0],0,1) . "," . substr($THICKNESS_arr[0],1,1) : $THICKNESS_arr[0];
		
		$STEEL = ( preg_match( '/\d+T/' , $line) ) ? '"S355J2H"' : '"S235JR"';
		$LASER = '"Yes"';
		$QTY = '"' . substr($line, $first_tab + 1, $second_space - ($first_tab + 1)) . '"';
		
		$entry_line = "\t\t{\"THICKNESS\": " . "\"" . $THICKNESS . "\"" . ", \"DIM_A\": " . "\"" . $DIM_A_arr[0] . "\"" . ", \"DIM_B\": " . "\"" . $DIM_B_arr[0] . "\"" . ", \"LENGTH\": " . "\"" . $LENGTH_arr[0] . "\"" . ", \"STEEL\": " . $STEEL . ", \"LASER\": " . $LASER . ", \"QTY\": " . $QTY  . "},\r\n";
		
	}
	
	array_push($surowce['cold_angle'], $entry_line);
	
}

/* ---- Hot Angle processor ---- */

function is_hot_angle($line){	
	if ( preg_match( '/^KTG\d|K\d+[A-Z]KTG\d|[0-9_]+[A-Z]KTG\d/' , $line) ) return true; 
	return false;
}

function populate_hot_angle($line){
	
	global $surowce;
	
	if( preg_match( '/^KTG\d/' ,$line) ) {
		
		$first_dash = strpos($line, '-');                     
		$second_dash = strpos($line, '-', $first_dash + 1 );  
		$third_dash = strpos($line, '-', $second_dash + 1 );
		$fourth_dash = strpos($line, '-', $third_dash + 1 );		
		$first_space = strpos($line, ' ');
		$first_tab = strpos($line, "\t");
		$second_space = strpos($line, ' ', $first_tab + 1);
		
		$THICKNESS = '"' . substr($line, $second_dash + 1 , $third_dash - ($second_dash + 1) ) . '"';
		$DIM_A = '"' . substr($line, 3, $first_dash - 3 ) . '"';
		$DIM_B = '"' . substr($line, $first_dash + 1 , $second_dash - ($first_dash + 1) ) . '"';
		$LENGTH = '"' . substr($line, $third_dash + 1 , $fourth_dash - ($third_dash + 1) ) . '"';
		$STEEL = '"' . substr($line, $fourth_dash + 1 , $first_space - ($fourth_dash + 1) ) . '"';
		$LASER = '"No"';
		$QTY = '"' . substr($line, $first_tab + 1, $second_space - ($first_tab + 1)) . '"';

		$entry_line = "\t\t{\"THICKNESS\": " . $THICKNESS . ", \"DIM_A\": " . $DIM_A . ", \"DIM_B\": " . $DIM_B . ", \"LENGTH\": " . $LENGTH . ", \"STEEL\": " . $STEEL . ", \"LASER\": " . $LASER . ", \"QTY\": " . $QTY  . "},\r\n";
		
	} else {
		
		$first_tab = strpos($line, "\t");
		$second_space = strpos($line, ' ', $first_tab + 1);
		
		preg_match( '/(?<=.KTG\d{4})[1-9]+/' , $line , $THICKNESS_arr );
		preg_match( '/(?<=.KTG)[1-9]\d/' , $line , $DIM_A_arr );
		preg_match( '/(?<=.KTG[1-9]\d)[1-9]\d/' , $line , $DIM_B_arr );
		preg_match( '/((?<=.KTG\d{5}-)|(?<=.KTG\d{6}-))\d+/' , $line , $LENGTH_arr );
		
		$THICKNESS = ( strlen($THICKNESS_arr[0]) > 1 ) ? substr($THICKNESS_arr[0],0,1) . "," . substr($THICKNESS_arr[0],1,1) : $THICKNESS_arr[0];
		
		$STEEL = ( preg_match( '/\d+T/' , $line) ) ? '"S355J2H"' : '"S235JR"';
		$LASER = '"Yes"';
		$QTY = '"' . substr($line, $first_tab + 1, $second_space - ($first_tab + 1)) . '"';
		
		$entry_line = "\t\t{\"THICKNESS\": " . "\"" . $THICKNESS . "\"" . ", \"DIM_A\": " . "\"" . $DIM_A_arr[0] . "\"" . ", \"DIM_B\": " . "\"" . $DIM_B_arr[0] . "\"" . ", \"LENGTH\": " . "\"" . $LENGTH_arr[0] . "\"" . ", \"STEEL\": " . $STEEL . ", \"LASER\": " . $LASER . ", \"QTY\": " . $QTY  . "},\r\n";
		
	}
	
	array_push($surowce['hot_angle'], $entry_line);
	
}

/* ---- Bolt procesor ---- */

function is_bolt($line){	
	if ( stripos($line, 'Śruba') !== false ) return true; 
	return false;
}

function populate_bolt($line){
	
	global $surowce;
	
	preg_match('/ M\d+/', $line, $TYPE); 
	preg_match('/(?<=[xX])\d{2,3}/', $line, $LENGTH);
	
		if( stripos($line, 'zamkowa' ) !== false ){
			
				$HEAD = "Śruba zamkowa";
			
		} elseif ( stripos($line, 'walcowym') !== false ){
			
				$HEAD = "Śruba z łbem walcowym z gniazdem sześciokątnym";
					 
		} else $HEAD = "Śruba z łbem sześciokątnym";
			
	preg_match('/DIN [0-9-]+|ISO [0-9-]+/', $line, $STANDARD);
	
	
	$entry_line = "\t\t{\"TYPE\": " . "\"" . $TYPE[0] . "\"" . ", \"LENGTH\": " . "\"" . $LENGTH[0] . "\"" . ", \"HEAD\": " . "\"" . $HEAD . "\"" . ", \"STANDARD\": " . "\"" . $STANDARD[0] . "},\r\n";
	
	array_push($surowce['bolt'], $entry_line);
	
}

/* ---- Nut procesor ----  : "Hex"  "Nylon" "Cap" */

	

