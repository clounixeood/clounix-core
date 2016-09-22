<?php

    global $PHP_SELF;

    //Controllo che il file non venga chiamato in modo diretto
    if (preg_match("/^header\.php$/", $PHP_SELF)) {
        echo "You can't access this file directly...";
    }

    //Setto variabili globali
    global $pnconfig, $artpage;

    //Includo xajax
    require_once( "javascript/xajax/xajax_core/xajax.inc.php");

    //Se esiste un file ajax personalizzato lo includo
    if (file_exists('modules/' . pnModGetName() . '/xajax.php')) {
        include('modules/' . pnModGetName() . '/xajax.php');
    }


    //imposto il tema
    if ($_GET['theme'] != '') {
        PnSessionSetVar('theme', $_GET['theme']);
    } else {
        PnSessionSetVar('theme', 'Default');
    }


    //Inizio lo standard output
    echo '<!doctype html>' . "\n";
    echo '<html lang="' . PnSessionGetVar('lang') . '">' . "\n";
    echo '<head>' . "\n";

    //Scrivo ajax se la variabile è impostata
    echo $GLOBALS['xajax'];


    echo "\t" . '<meta charset="utf-8" />' . "\n";
    echo "\t" . '<meta name="viewport" content="width=device-width, initial-scale=1.0" />' . "\n";
    echo "\t" . '<meta property="og:image" content="http://' . DOMINIO_WWW . '/themes/' . PnSessionGetVar('theme') . '/images/logo.png" />' . "\n";

    //Prendo i metatags dinamici
    GetMetaTags($artpage);

    //Se non è impostata nessuna variabile headless
    if ($_GET['headless'] != 1) {

        //Google Language 
        foreach ($pnconfig['languages'] as $key => $value) {

            if ($value != PnSessionGetVar('lang')) {

                //Specifico la funzione nel caso sia vuota
                if (empty($_GET['func'])) { $func = 'Main'; } else { $func = $_GET['func']; }
                if (empty($_GET['type'])) { $type = 'user'; } else { $type = $_GET['type']; }

                //Scrivo il link rel per indicizzazione Google in base alle lingue
                echo "\t" . '<link rel="alternate" href="//' . DOMINIO_WWW . '/' . $value . '/index.php?module=' . pnModGetname() . '' . (($type == 'admin') ? '&type=' . $type . '' : '') . '&func=' . $func . '" hreflang="' . $value . '" />' . "\n";
            }
        }

        //Favicon
        echo "\t" . '<link rel="icon" href="/favicon.ico" type="image/x-icon">' . "\n";

        //Includo le icone di google
        echo "\t" . '<link rel="stylesheet" href="//fonts.googleapis.com/icon?family=Material+Icons">' . "\n";


        //Css principale     
        echo "\t" . '<link rel="stylesheet" href="/javascript/bootstrap/css/bootstrap.css">' . "\n";
        echo "\t" . '<link rel="stylesheet" href="/javascript/bootstrap-select/css/bootstrap-select.css">' . "\n";
        echo "\t" . '<link rel="stylesheet" href="/javascript/bootstrap-datepicker/css/bootstrap-datepicker.css">' . "\n";
        echo "\t" . '<link rel="stylesheet" href="/javascript/bootstrap-switch/css/bootstrap-switch.css">' . "\n";
        echo "\t" . '<link rel="stylesheet" href="/javascript/bootstrap-checkbox/css/bootstrap-checkbox.css">' . "\n";
        echo "\t" . '<link rel="stylesheet" href="/javascript/bootstrap/css/bootstrap-custom.css">' . "\n";
        echo "\t" . '<link rel="stylesheet" href="/javascript/bootstrap/css/bootstrap-validator.css"/>' . "\n";
        echo "\t" . '<link rel="stylesheet" href="/javascript/cookiebar/jquery.cookiebar.css">' . "\n";
        echo "\t" . '<link rel="stylesheet" href="/javascript/bootstrap-datatables/bootstrap-datatables.css">' . "\n";

        //file per CSS personalizzato
        $filecssmodule = 'modules/' . pnModGetName() . '/style.css';
        //file per CSS personalizzato
        $filecssglobal = 'modules/' . pnSessionGetVar('theme') . '/custom.css';



        //controllo se esiste il file personalizzato per il modulo per includerlo
        if (file_exists($filecss)) {

            //Includo il file
            echo "\t" . '<link rel="stylesheet" href="/' . $filecss . '">' . "\n";
        }

        //controllo se esiste un file personalizzato globale per includerlo
        if (file_exists($filecssglobal)) {

            //Includo il file
            echo "\t" . '<link rel="stylesheet" href="/' . $filecssglobal . '">' . "\n";
        }


        //Modifiche per Internet explorer 9 o minore
        echo "\t" . '<!--[if lt IE 9]>' . "\n";
        echo "\t" . '<script src="//oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>' . "\n";
        echo "\t" . '<script src="//oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>' . "\n";
        echo "\t" . '<![endif]-->' . "\n";


        //Seleziono il font da google
        echo "\t" . '<link href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700" rel="stylesheet" type="text/css">' . "\n";

        //Jquery and fastclick
        echo "\t" . '<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>' . "\n";

        //Barra dei cookie
        echo "\t" . '<script src="/javascript/cookiebar/jquery.cookiebar.js"></script>' . "\n";
        //Inizializzo
        echo "\t" . '<script> $(document).ready(function(){ $.cookieBar(); }); </script>' . "\n";

        //JS Principale
        echo "\t" . '<script src="/javascript/bootstrap/js/bootstrap.js"></script>' . "\n";
        echo "\t" . '<script src="/javascript/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>' . "\n";
        echo "\t" . '<script src="/javascript/bootstrap-select/js/bootstrap-select.js"></script>' . "\n";
        echo "\t" . '<script src="/javascript/bootstrap-switch/js/bootstrap-switch.js"></script>' . "\n";
        echo "\t" . '<script src="/javascript/bootstrap-datatables/datatables.js"></script>' . "\n";
        echo "\t" . '<script src="/javascript/bootstrap-datatables/moment.js"></script>' . "\n";
        echo "\t" . '<script src="/javascript/bootstrap-datatables/bootstrap-datatables.js"></script>' . "\n";

        //Bootstrap validator
        echo "\t" . '<script src="/javascript/bootstrap/js/bootstrap-validator.js"></script>' . "\n";

        //BlockUI e Rechapta
        echo "\t" . '<script src="/javascript/blockUI/blockui.js"></script>' . "\n";
        echo "\t" . '<script src="//www.google.com/recaptcha/api.js"></script>' . "\n";


        ####################################################
        //Header personalizzato
        include('custom/header.php');
        ####################################################
    }

    //Chiudo head
    echo "\n" . '</head>' . "\n";
    //apro body
    echo '<body>' . "\n";

    ?>