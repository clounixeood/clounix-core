<?php
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
function pnModGetInfo($modname)
{
    if ( $modname == '') { return false; }
    
    $array['name']      = $modname;
    $array['directory'] = $modname;
    $array['type'] = 2;    
    
    return $array;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
function pnModLoad($modname, $type = 'user')
{

    static $loaded = array();
    
    if (empty($modname)) { return false; }

    //Url del file
    $file = "modules/".ucwords(strtolower($modname))."/pn".$type.".php";

    //Se il file non esiste errore
    if (!file_exists($file)) { return false; }
    
    //Includo il file
    include $file;
  
    // Return the module name
    return $modname;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
function pnModFunc($modname, $type = 'user', $func = 'Main', $args = array())
{
    //Se modname non esiste
    if ($modname == '')	return false;
    
    //Imposto variabili
    if ($type == '')	$func = 'user';
    if ($func == '')	$func = 'Main';

    //Funzione
    $modfunc = "{$modname}_{$type}_{$func}";       
     
    if (function_exists($modfunc."_Custom")) { 
    
    //Ritorno la funzione personalizzata
    return $modfunc($args); 
    
    } else { 
    
    if (function_exists($modfunc)) { 
    
    //Ritorno la funzione corretta
    return $modfunc($args); 
    
    } else {
    
    return false; 
    
    }

    }

}
//////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////
function pnModURL($modname, $type = 'user', $func = 'Main', $args = array(), $ssl = SSL_ACTIVE)
{
    
    //Parametro global
    global $mysqli;    
    
    //Url
    $url = '';

    //Se modname non è impostata
    if (empty($modname)) { return false; }
    
    //Prendo l'host
    $host = $_SERVER['HTTP_HOST'];
    
    //Se l'host è vuoto
    if (empty($host)) { 
    
    $host = getenv('HTTP_HOST'); if (empty($host)) { return false; }
    
    }
    
    //Aggiungo gli argomaneti
    if (!is_array($args)) { return false; } else { foreach ($args as $k => $v) { $url .= "&$k=$v"; } }


    //Aggiungo gli argomenti all'array
    $urlargs = "".strtolower($modname)."/".strtolower($type)."/".strtolower($func)."/".(($url != '') ? '?' : '')."$url";         
    
    //Aggiorno l'url con la lingua
    $url = ''.PnSessionGetVar('lang').'/'.$urlargs.'';

    //Controllo se l'url esiste nel database urlseo, se esiste lo sostituisco
    $q = $mysqli->query("SELECT * FROM _htaccess WHERE url LIKE '$url' AND htmlurl='1'");
    
    if ($q->num_rows > 0) {
    
    //Prendo i dati
    $seo = $q->fetch_array();
    
    //Sostituisco l'url
    $url = ''.$seo['htaccess'].'';
    
    }
    
    //Aggiungo il dominio
    $url = pnGetBaseHost().$url; 
	   
    //SSSL
    if ($ssl === true) { $url = preg_replace("/^http:/", "https:", $url); } else if ($ssl === false) {  $url = preg_replace("/^https:/", "http:", $url); } 
  
  
  return $url; 
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
function pnModGetName() {
    
    
    //Prendo il nome del modulo
    $modname = ucwords(strtolower($_GET['module']));
    
    //Se è vuoto ritorno il modulo a livello globale
    if (empty($modname)) { global $module; return $module; } 


    return $modname;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
