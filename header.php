<?php
global $PHP_SELF;

if (preg_match("/^header\.php$/", $PHP_SELF)) { echo "You can't access this file directly..."; }


     global $pnconfig, $artpage;
     
     require_once( "javascript/xajax/xajax_core/xajax.inc.php");
     
     //Se esiste un file ajax personalizzato lo includo
     if (file_exists('modules/'.pnModGetName().'/xajax.php')) { include('modules/'.pnModGetName().'/xajax.php'); }
         

     //imposto il tema
     if ($_GET['theme'] != '') { PnSessionSetVar('theme', $_GET['theme']); } else { PnSessionSetVar('theme', 'Default'); }  


      //Inizio lo standard output
      echo '<!doctype html>'."\n";
      echo '<html lang="'.PnSessionGetVar('lang').'">'."\n";
      echo '<head>'."\n";

      //Scrivo ajax se la variabile è impostata
      echo $GLOBALS['xajax'];
      
      
      echo "\t".'<meta charset="utf-8" />'."\n";
      echo "\t".'<meta name="viewport" content="width=device-width, initial-scale=1.0" />'."\n";
      echo "\t".'<meta property="og:image" content="http://'.DOMINIO_WWW.'/themes/'.PnSessionGetVar('theme').'/images/logo.png" />'."\n";
    
      GetMetaTags($artpage);     

      
      if ($_GET['headless'] != 1) {
    
       //Google Language 
       foreach ($pnconfig['languages'] as $key => $value) {
       
       if ($value != PnSessionGetVar('lang')) { 
          
          //Specifico la funzione nel caso sia vuota
          if (empty($_GET['func']))   { $func   = 'Main';  } else { $func = $_GET['func']; }
          if (empty($_GET['type']))   { $type   = 'user';  } else { $type = $_GET['type']; }
              
          echo "\t".'<link rel="alternate" href="http://'.DOMINIO_WWW.'/'.$value.'/index.php?module='.pnModGetname().''.(($type == 'admin') ? '&type='.$type.'' : '').'&func='.$func.'" hreflang="'.$value.'" />'."\n"; 
          
          }
       
       }
       //////////////////
     
     //Favicon
     echo "\t".'<link rel="icon" href="/favicon.ico" type="image/x-icon">'."\n";
     
     //Includo le icone di google
     echo "\t".'<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">'."\n";


     //Css principale     
     echo "\t".'<link rel="stylesheet" href="/javascript/bootstrap/css/bootstrap.css">'."\n";     
     echo "\t".'<link rel="stylesheet" href="/javascript/bootstrap-select/css/bootstrap-select.css">'."\n";
     echo "\t".'<link rel="stylesheet" href="/javascript/bootstrap-datepicker/css/bootstrap-datepicker.css">'."\n";  
     echo "\t".'<link rel="stylesheet" href="/javascript/bootstrap-switch/css/bootstrap-switch.min.css">'."\n";
     echo "\t".'<link rel="stylesheet" href="/javascript/bootstrap-checkbox/css/bootstrap-checkbox.css">'."\n";
     echo "\t".'<link rel="stylesheet" href="/javascript/bootstrap/css/bootstrap-custom.css">'."\n"; 
     echo "\t".'<link rel="stylesheet" href="/javascript/bootstrap/css/bootstrap-validator.css"/>'."\n";     
     echo "\t".'<link rel="stylesheet" href="/javascript/cookiebar/jquery.cookiebar.css">'."\n";    
     echo "\t".'<link rel="stylesheet" href="/javascript/datatables/datatables.bootstrap.css">'."\n"; 
     
     //file per CSS personalizzato
     $filecss = 'modules/'.pnModGetName().'/style.css';    
     
     //controllo se esiste per includerlo
     if (file_exists($filecss)) { 
    
     //Includo il file
     echo "\t".'<link rel="stylesheet" href="/'.$filecss.'">'."\n"; 
    
     }
      
     echo "\t".'<!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->'."\n";
     echo "\t".'<!--[if lt IE 9]>'."\n";
     echo "\t".'<script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>'."\n";
     echo "\t".'<script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>'."\n";
     echo "\t".'<![endif]-->'."\n";
          
     
     //Seleziono il font da google
     echo "\t".'<link href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet" type="text/css">'."\n"; 
     
     //Jquery and fastclick
     echo "\t".'<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>'."\n";
     echo "\t".'<script src="/javascript/cookiebar/jquery.cookiebar.js"></script>'."\n";       
     echo "\t".'<script> $(document).ready(function(){ $.cookieBar(); }); </script>'."\n";
     
     //JS Principale
     echo "\t".'<script src="/javascript/bootstrap/js/bootstrap.min.js"></script>'."\n";
     echo "\t".'<script src="/javascript/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>'."\n";     
     echo "\t".'<script src="/javascript/bootstrap-select/js/bootstrap-select.js"></script>'."\n";     
     echo "\t".'<script src="/javascript/bootstrap-switch/js/bootstrap-switch.min.js"></script>'."\n";
     echo "\t".'<script src="/javascript/datatables/datatables.js"></script>'."\n";            
     echo "\t".'<script src="/javascript/datatables/moment.js"></script>'."\n";  
     echo "\t".'<script src="/javascript/datatables/datatables.bootstrap.js"></script>'."\n";              


     //Bootstrap validator
     echo "\t".'<script src="/javascript/bootstrap/js/bootstrap-validator.js"></script>'."\n";
     
     //BlockUI
     echo "\t".'<script src="/javascript/blockUI/blockUI.js"></script>'."\n";       
     echo "\t".'<script src="//www.google.com/recaptcha/api.js"></script>'."\n";
     
                             
     //file Validity per Bootstrap Validity
     //$filev = 'modules/'.pnModGetName().'/validity'.(($_GET['type'] == 'admin') ? '_admin' : '').'.js';    
     
     //controllo se esiste per includerlo
     //if (file_exists($filev)) { 
    
     //Includo il file
     //echo "\t".'<script src="/'.$filev.'"></script>'."\n"; 
    
     //}

    ####################################################
    ####################################################
     //Header personalizzato
     include('custom/header.php');
    ####################################################
    ####################################################
    
    }         
     
    echo "\n".'</head>'."\n";
    echo '<body>'."\n";     
 
?>