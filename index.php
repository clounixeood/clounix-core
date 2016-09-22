<?php
date_default_timezone_set('Europe/Rome');
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
include_once 'includes/api.php';

    $GLOBALS['LangNoLoad'] = 0;

    pnInit();
    
    //carico la lingua
    pnLangLoad();
                       
    $module = $_GET['module'];
    $func   = $_GET['func'];
    $type   = $_GET['type'];
    
    
    // Check requested module and set to start module if not present
    if (empty($module)) { $module = MODULE_DEFAULT; }
    if (empty($func))   { $func   = 'Main';    }
    if (empty($type))   { $type   = 'user';    }    
    
    PnSessionSetVar('module', $module);
    PnSessionSetVar('func',   $func);    
    PnSessionSetVar('type',   $type);    
    
    //if ($_SERVER['REMOTE_ADDR'] == '46.26.77.236') { exit; }
    ####################################################
    ####################################################
    include ('custom/getcountry.php');
    ####################################################
    ####################################################
  
        
    //faccio il redirect
    if ($_SERVER['REQUEST_URI'] == "/") { 

        //Se l'url è vuoto faccio il redirect verso una lingua
        PnRedirect('http://'.DOMINIO_WWW.'/'.strtolower(PnSessionGetVar('lang')).''); 

    } else {

        //Se l'url è vuoto faccio il redirect verso una lingua
        if (empty($_GET['lang'])) { Pnredirect(''.strtolower(PnSessionGetVar('lang')).''.$_SERVER['REQUEST_URI'].''); }    
   
    }


    //Se l'url non è corretto restituisco errore
    //if (!preg_match("/^\/(it|en)\/index\.php|^\/[a-z-\.]{3,}|^\/$|^\/(it|en)$/", $_SERVER['REQUEST_URI'])) { PnRedirect('http://'.DOMINIO_WWW.'/404.html'); }


    //Imposto l'url per errore
    $urlerrore = 'index.php?module='.$module.'&type='.$type.'&func='.$func.'';
    
        //Se il tipo esisto vado ad eseguire la funzione
        if (pnModLoad($module, $type)) {  
          
          //assegno il valore return
          $return = pnModFunc($module, $type, $func); 
        
        } else { 
          
          //Se non esiste do errore.
          pnRedirect('/404.html?url='.urlencode($urlerrore).''); 
        
        } 
    
    //se la funzione ritorna errore
    if (empty($return) || $return == false) {  pnRedirect('/404.html?url='.urlencode($urlerrore).''); exit;
    
    
    } elseif (strlen($return) > 1) {
            
            $output = new pnHTML();
            $output->StartPage();
            
            if ($type == 'admin') { CheckAdminAuth(); }
                        
            $output->BodyPage($return);
            
            $output->EndPage();
            $output->PrintPage();
                
            exit;         	      
    }
    
exit;
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>
