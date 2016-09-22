<?php
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
//
//  > Portale CMS T.FAST 
//  > Scritto da Andrea Bernardi
//  > E' vietata la riproduzione anche parziale
//  > Per supporto andrea@terastudio.it o www.terastudio.it
//
//  > MDHtml versione 1.0
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////
function pnLangLoad()
{
    
    global $pnconfig;
    
    //Prendo la lingua
    $lang = $_GET['lang'];
   
    //Se la lingua non è vuota
    if (!empty($lang)) {
        
        //E se è presente nell'array di config imposto la lingua
        if (in_array($lang, $pnconfig['languages'])) {  pnSessionSetVar('lang', $lang); }
    

    //Se la lingua su GET è vuota e session lang non è impostato
    } else if ((empty($lang)) && (!PnSessionGetVar('lang'))) {
        
        //Prendo la lingua dal browser
        $lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
        
        //Imposto la lingua solo se presente in array
        if (in_array($lang, $pnconfig['languages'])) { pnSessionSetVar('lang', $lang);  } else { pnSessionSetVar('lang', 'en'); }
        
    } 

    
    if ($GLOBALS['LangNoLoad'] != 1) {
    
        //definisco la lingua
        $define = pnSessionGetVar('lang');
        
        include('languages/'.pnSessionGetVar('lang').'.php');
    
    }

return true;
}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function T($m, $text)
{
    

      //Parametro globale
      global $mysqli, $pnconfig;
      
      //Una definizione è composta da __MODULO_LUNGHEZZA,PRIME3LETTERE,ULTIMETRELETTERE,NUMERODISPAZI
      $l  = strlen($text);
      $pt = substr(preg_replace("/[^A-Za-z0-9]/","",$text), 0, 2);
      $ut = substr(preg_replace("/[^A-Za-z0-9]/","",$text), -2);
      
      $def = strtoupper("__".$m."_".$l."".$pt."".$ut."");
      
      //Se questa definizione non esiste
      if (!defined($def)) { 
      
      //API KEY
      $apiKey = GOOGLE_API_KEY;
      
      //Se non è definita la inserisco dentro mysql
      $q = $mysqli->query("INSERT INTO _languages (define, en, it, needhuman, datareg, lastupdate, type, found) VALUES ('$def', '', '".addslashes($text)."', '1', '".time()."', '".time()."','1', '1')");
    
      return $text;
      
      } else {
      
      return constant($def);
      
      
      } 

}
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>