<?php
function GetMetaTags($type)
{
	
     //Parametro globale
     global $mysqli;
       
     switch ($type) {
	 	  
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////      
      default:

      if ($GLOBALS['head'] != 1) { 
      
        //Modifico la variabile uri
        $uri = str_replace("/", "", $_SERVER['REQUEST_URI']); 
        
        //Query
        $q = $mysqli->query("SELECT * FROM _htaccess WHERE htaccess='$uri'");
        
        //Se trovo qualcosa
        if ($q->num_rows > 0) { 
        
        //Prendo i dati
        $det = $q->fetch_array(); 
        
        //Cancello tutto dalla pagina
        $htacc = str_replace("-", " ", $det['htaccess']); 
        
        //Sostituisco il .html
        $htacc = str_replace(".html", "", $htacc);	
        
        //Scrivo il titolo
        echo "\t".'<title>'.ucwords($htacc).' - '.SEO_TITLE.'</title>'."\n\n";        
        
        } else {

        //Scrivo il titolo normale
        echo "\t".'<title>'.SEO_SLOGAN.' - '.SEO_TITLE.'</title>'."\n\n";        

        }

        //Keyword e description
        echo "\t".'<meta name="keywords" content="'.SEO_KEYWORDS.'">'."\n";         
    	  echo "\t".'<meta name="description" content="'.SEO_DESCRIPTION.'">'."\n"; 	      
        
        
        }	else {
    
        //Metto il titolo globale
        echo "\t".'<title>'.$GLOBALS['head_title'].' - '.SEO_TITLE.'</title>'."\n\n";      
      
        //Keyword e description
        echo "\t".'<meta name="keywords" content="'.$GLOBALS['head_tag'].'">'."\n";       
	      echo "\t".'<meta name="description" content="'.$GLOBALS['head_description'].'">'."\n"; 


        }

        }	
	      
        //Tutto il resto
        echo "\t".'<meta name="robots" content="INDEX,FOLLOW">'."\n";
      	echo "\t".'<meta http-equiv="expires" content="0">'."\n";
      	echo "\t".'<meta name="author" content="Andrea Bernardi">'."\n";
      	echo "\t".'<meta name="copyright" content="Copyright (c) '.date("Y").' by Andrea Bernardi">'."\n";
      	echo "\t".'<meta name="revisit-after" content="5 days">'."\n\n";

      //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////     


} 

?>