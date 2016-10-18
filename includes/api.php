<?php

    /////////////////////////////////////////////////////////////////////////////////////////////////////
    /////////////////////////////////////////////////////////////////////////////////////////////////////
    //Se non è impostato per registrare le variabili globali
    if (ini_get('register_globals') != 1) {

        //Imposto array delle supervariabili
        $supers = array('_GET', '_POST', '_COOKIE', '_ENV', '_SERVER', '_SESSION', '_FILES');

        //Ciclo le supervariabili
        foreach ($supers as $__s)
            if (isset($$__s) && is_array($$__s)) {
                extract($$__s, EXTR_OVERWRITE);
            }

        //Elimino variabile
        unset($supers);
    }

    //////////////////////////////////////////////////////////////////////////// 
    // Inizializza il CRM
    ////////////////////////////////////////////////////////////////////////////
    function pnInit() {

        global $pnconfig;

        $pnconfig = array();

        //Includo il file di configurazione
        include 'config/md-config.php';

        // Connect to database
        if (!pnDBInit())
            die('Inizializzazione database fallito');

        // Build up old config array
        // Set compression on if desired
        ob_start("ob_gzhandler");

        //Includo tutti i file ClassDefault
        foreach (glob("includes/ClassDefault/*.php") as $filename) {
            include $filename;
        }


        // Start session
        if (!pnSessionSetup())
            die('Setup sessione fallito');
        if (!pnSessionInit())
            die('Inizializzazione sessione fallita');


        return true;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Inizializzo il db
    ////////////////////////////////////////////////////////////////////////////
    function pnDBInit() {

        $dbtype  = $GLOBALS['pnconfig']['dbtype'];
        $dbhost  = $GLOBALS['pnconfig']['dbhost'];
        $dbname  = $GLOBALS['pnconfig']['dbname'];
        $dbuname = $GLOBALS['pnconfig']['dbuname'];
        $dbpass  = $GLOBALS['pnconfig']['dbpass'];


        global $mysqli;

        $mysqli = new mysqli($dbhost, $dbuname, $dbpass, $dbname);

        if ($mysqli->connect_errno) {
            // The connection failed. What do you want to do? 
            // You could contact yourself (email?), log the error, show a nice page, etc.
            // You do not want to reveal sensitive information
            // Let's try this:
            echo "Sorry, there is a problem with our website. Come back later.";

            // Something you should not do on a public site, but this example will show you
            // anyways, is print out MySQL error related information -- you might log this
            echo "Errno: " . $mysqli->connect_errno . "\n";
            echo "Error: " . $mysqli->connect_error . "\n";

            // You might want to show them something nice, but we will simply exit
            exit;
        }

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Prende tutti i dati di un array $_POST $_GET $_REQUEST e li restituisce puliti
    function PnVarCleanFromPost($array, $keycheck = 1) {


        //Controllo se la variabile è esatta
        if (($keycheck == 1) && ($array['form_hashkey'] != PnSessionGetVar('form_hashkey'))) {
            
            //Redirect
            PnRedirect(PnModUrl('Users', 'user', 'FormError'));
        }

        $resarray = array();

        foreach ($array as $key => $var) {

            //Controllo se abilitato magic quotes
            if (!get_magic_quotes_gpc()) {
                
                //Alcuni parametri php
                $var = htmlentities(addslashes($var), ENT_COMPAT, "UTF-8");
            }

            //Pulisco le variabili
            $ourvar = trim($var);

            //Imposto array
            $resarray[$key] = $ourvar;
        }

        return $resarray;
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    function PnStripSlashes($value) {
        
        if (!is_array($value)) {
            
            //tolgo i singoli array
            $value = stripslashes($value);
        } else {
            
            //tolgo agli array
            array_walk($value, 'PnStripSlashes');

        }
            
            return $value;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Restituisce il GetBase Uri che corrisponde ad esempio a /index.php
    function pnGetBaseURI() {

        $path = pnServerGetVar('REQUEST_URI');

        if ((empty($path)) || (substr($path, -1, 1) == '/')) {

            $path = pnServerGetVar('PATH_INFO');

            if (empty($path)) {
                $path = pnServerGetVar('SCRIPT_NAME');
            }
        }

        $path = preg_replace('/[#\?].*/', '', $path);
        $path = dirname($path);

        if (preg_match('!^[/\\\]*$!', $path)) {
            $path = '';
        }

        return $path;
    }

    ////////////////////////////////////////////////////////////////////////////
    //Restituisce l'host esempio http://www.test.it/
    function pnGetBaseHost() {
       
        //Imposto il server
        $server = pnServerGetVar('HTTP_HOST');

        //Controllo se esiste https per reimpostare url corretto
        if (isset($_SERVER['HTTPS'])) {
            $proto = 'https://';
        } else {
            $proto = 'http://';
        }

        return "$proto$server/";
    }

    ////////////////////////////////////////////////////////////////////////////
    // URL di vase http://www.test.it/index.php
    function pnGetBaseURL() {
        
        //Server
        $server = pnServerGetVar('HTTP_HOST');
        $path = pnGetBaseURI();

        //Controllo se è http o https
        if (isset($_SERVER['HTTPS'])) {
            $proto = 'https://';
        } else {
            $proto = 'http://';
        }

        return "$proto$server$path/";
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    function pnRedirect($redirect) {

        if (preg_match("/^http(.*)$/", $redirect)) {

            Header("Location: $redirect");
            echo $redirect;
            exit;
            return true;
        } else {

            $redirect = preg_replace("!^/*!", "", $redirect);
            $baseurl = pnGetBaseURL();

            Header("Location: $baseurl$redirect");
            exit;
            return true;
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    function pnLocalReferer() {

        $server = pnServerGetVar('HTTP_HOST');
        $referer = pnServerGetVar('HTTP_REFERER');

        if (empty($referer) || preg_match("!^http://$server/!", $referer)) {
            return true;
        } else {
            return false;
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    // Restituisce una variabile $_SERVER
    function pnServerGetVar($name) {
        
        if (isset($_SERVER[$name])) {
            return $_SERVER[$name];
        }
        return;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Restituisce il titolo di un host es : test.it
    function pnGetHostTitle() {
        
        $server = pnServerGetVar('HTTP_HOST');
        $server = explode(".", $server);
        $server = ucwords($server[1]) . "." . $server[2];
        
        return $server;
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    function pnGetHost() {
        $server = pnServerGetVar('HTTP_HOST');

        if (empty($server)) {
            $server = pnServerGetVar('SERVER_NAME');
            $port = pnServerGetVar('SERVER_PORT');
            if ($port != '80')
                $server .= ":$port";
        }

        return $server;
    }

    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    function pnGetCurrentURI($args = array()) {
        $request = pnServerGetVar('REQUEST_URI');

        if (empty($request)) {

            $scriptname = pnServerGetVar('SCRIPT_NAME');
            $pathinfo = pnServerGetVar('PATH_INFO');

            if ($pathinfo == $scriptname) {
                $pathinfo = '';
            }
            if (!empty($scriptname)) {
                $request = $scriptname . $pathinfo;
                $querystring = pnServerGetVar('QUERY_STRING');
                if (!empty($querystring))
                    $request .= '?' . $querystring;
            } else {
                $request = '/';
            }
        }

        if (count($args) > 0) {
            if (strpos($request, '?') === false)
                $request .= '?';
            else
                $request .= '&';

            foreach ($args as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $l => $w) {
                        if (!empty($w))
                            $request .= $k . "[$l]=$w&";
                    }
                } else {
                    if (preg_match("/(&|\?)($k=[^&]*)/", $request, $matches)) {
                        $find = $matches[2];
                        if (!empty($v)) {
                            $request = preg_replace("/(&|\?)$find/", "$1$k=$v", $request);
                        } elseif ($matches[1] == '?') {
                            $request = preg_replace("/\?$find(&|)/", '?', $request);
                        } else {
                            $request = preg_replace("/&$find/", '', $request);
                        }
                    } elseif (!empty($v)) {
                        $request .= "$k=$v&";
                    }
                }
            }

            $request = substr($request, 0, -1);
        }

        return $request;
    }

    ////////////////////////////////////////////////////////////////////////////
    ///Prepara una variabile per essere visualizzata in html
    function VarHtmlDecode() {
        
        $resarray = array();

        //Foreach
        foreach (func_get_args() as $ourvar) {

            //Decode html
            $ourvar = html_entity_decode($ourvar, null, "UTF-8");
            $ourvar = str_replace("\n", '<br>', $ourvar);

            // Add to array
            array_push($resarray, $ourvar);
        }

        // Return vars
        if (func_num_args() == 1)
            return $resarray[0];
        else
            return $resarray;
    }

    ////////////////////////////////////////////////////////////////////////////
    //Prepara una variabile per essere transformata da html a db
    function VarHtmlEncode() {
        $resarray = array();
        
        //Foreach
        foreach (func_get_args() as $ourvar) {

            //Encode html
            $ourvar = htmlentities($ourvar, null, "UTF-8");
            $ourvar = htmlspecialchars_decode($ourvar, null, "UTF-8");

            //Add to array
            array_push($resarray, $ourvar);
        }

        //Return vars
        if (func_num_args() == 1)
            return $resarray[0];
        else
            return $resarray;
    }

    ////////////////////////////////////////////////////////////////////////////
    //Controlla le autorizzazioni admin dal database
    function CheckAdminAuth() {


        //Parametro global
        global $mysqli, $func, $module;

        $refer = getenv('HTTP_REFERER');
        $link  = pnGetCurrentURI();
        $uid   = PnUserGetVar('uid');
        $group = PnUserGetMemberOf($uid);

        //Se utente non è loggato
        if (!PnUserLoggedIn()) {
            Pnredirect(pnModUrl('Users', 'user', 'NoAuth'));
            exit;
            return false;
        }

        //Query
        $q = $mysqli->query("SELECT admin FROM _groups WHERE gid='$group' LIMIT 1");


        //Tiro fuori admin o meno
        $admin = $q->fetch_row();

        //Se non è admin esco
        if ($admin[0] != 1) {
            Pnredirect(pnModUrl('Users', 'user', 'NoAuth'));
            exit;
            return false;
        }

        //Se non trovo la funzione allora la imposto a Main
        if ($func == '') {
            $func = 'Main';
        }


        //Se non ci sono le autorizzazioni per fare questo
        if (pnSessionGetVar('permissions_noauth') == 1) {


            PnSessionDelVar('permissions_noauth');
            PnSessionDelVar('permissions_page');  
        } else {

            $q = $mysqli->query("SELECT * FROM _permissions WHERE (gid='$group' OR gid='*') AND (module='$module' OR module='*')");

            //Imposto il permesso = 0
            $permesso = 0;

            if ($q->num_rows > 0) {

                //Ciclo
                while ($det = $q->fetch_array()) {

                    //Imposto variabili 
                    $linkdb = $det['link'];
                    $linkdb = str_replace("\r", "", $linkdb);
                    $linkdb = explode(",", $linkdb);


                    //Controllo i vari array
                    if (in_array($func, $linkdb)) {
                        $permesso = 1;
                    }
                    if (in_array("*", $linkdb)) {
                        $permesso = 1;
                    }
                    if (in_array("!$func", $linkdb)) {
                        $permesso = 0;
                    }
                }
            }


            //se ha il permesso di entrare
            if ($permesso == 1) {
                return true;

                //Se non ha il permesso
            } else {

                if ((!$refer) || stristr($refer, $link)) {
                    $refer = PnModUrl('Administration', 'admin', 'Main');
                }

                //Imposto le variabili
                PnSessionSetVar('permissions_noauth', 1);
                PnSessionSetVar('permissions_page', 'rm=' . $module . '&rt=admin&rf=' . $func . '');

                //Effettuo il redirect
                PnRedirect($refer);

                exit;
                return false;
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////
    //Taglia una stringa in base alla lunghezza data
    function CutString($string, $char) {

        if (strlen($string) > $char) {
            $string = '' . substr($string, 0, $char) . '..';
        } else {
            $string = $string;
        }

        return $string;
    }

    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    //////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function DateForTimeZone($format = "d/m/Y", $time) {

        //Parametro global
        global $mysqli;

        //Query    
        $q = $mysqli->query("SELECT tz FROM _users WHERE uid='" . PnSessionGetVar('uid') . "'");

        //Prendo i dati
        $tz = $q->fetch_row();

        //IMposto la data
        $date = new DateTime(date("Y-m-d H:i:s", $time), new DateTimeZone('Europe/Rome'));

        //IMposto il timezone
        $date->setTimezone(new DateTimeZone($tz[0]));

        //Scrivo il formato
        $data = $date->format($format);


        return $data;
    }

    ////////////////////////////////////////////////////////////////////////////
    //Aggiunge un log alla tabella
    function AddLog($uid, $function, $text, $mail = 0) {


        //Parametro global
        global $mysqli;

        $text = PnUserGetVar('email', $uid) . ' - ' . $text;


        //Inserico la riga del log
        $q = $mysqli->query("INSERT INTO _logs (uid, function, text, email, sms, datareg) VALUES ('$uid', '$function', '$text', '$mail', '0', '" . time() . "')");


        //Se e-mail attiva l ainvio
        if ($mail == 1) {

            $message  = "UID : $uid\n";
            $message .= "Function : $function\n";
            $message .= "Azione : $text\n";

            //Invio la mail
            pnMail(MAIL_SERVICE, $text, $message, SERVICE_NAME, MAIL_NOREPLY, 1, '', '');
        }

        return true;
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    function PutImage($attribute = array(), $func = __FUNCTION__) {


        //Parametro global
        $buf = "\t\t" . '<img src="/modules/' . PnModGetName() . '/images/' . $func . '-' . $attribute['name'] . '" ';

        //Scorro tutti gli altri attributi
        foreach ($attribute as $key => $value) {
            $buf .= '' . (($key == 'singletag') ? ' ' . $value . '' : ' ' . $key . '="' . $value . '"') . '';
        }
        
        //Chiudo il tag 
        $buf .= '>' . "\n";
        
        return $buf;
    }

?>