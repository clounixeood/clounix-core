<?php
////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////

// Classe principale MDHTML
class pnHTML {
    
    
    var $pageheader;
    var $output;

    function pnHTML()
    {
        
        $this->pageheader = array ();
        $this->output = '';
    }

    //////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////  
    function GetOutput()
    {
        return $this->output;
    }
    //////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////  
    function PrintPage()
    {
        echo $this->output;
    }
    //////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////         
    function StartPage()
    {
        
    //Parametro globale
    global $mysqli;        
        
        ob_start();
                
        include_once('header.php');
        
        $content = ob_get_contents();
        

        ob_end_clean();

        $this->output .= $content;
    }
    //////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////  
    function BodyPage($body = '')
    {
        
        ob_start();
                
        require_once('libs/smarty/Smarty.class.php');
  
      	$smarty = new Smarty();
        
        $smarty->template_dir = $_SERVER["DOCUMENT_ROOT"].'/libs/smarty/templates/';
        $smarty->compile_dir  = $_SERVER["DOCUMENT_ROOT"].'/libs/smarty/templates_c/';
        $smarty->config_dir   = $_SERVER["DOCUMENT_ROOT"].'/libs/smarty/configs/';
        $smarty->cache_dir    = $_SERVER["DOCUMENT_ROOT"].'/libs/smarty/cache/';        

        //Includo tutti i file dei blocchi
        foreach (glob("blocks/*.php") as $filename) { 
        
          $bname = explode("/", $filename);
          $bname = explode(".", $bname[1]);
          $bname = $bname[0];
          
          $smarty->assign($bname.'block', pnBlockShow($bname)); 
        
        }


        $smarty->assign('random', rand(1,3));        
        $smarty->assign('imagepath', 'themes/'.PnSessionGetVar('theme').'/images/');
        $smarty->assign('modules', $body);
      
        //Setto il tema momentaneamente se impostato
        if ($GLOBALS['theme']) { $th = $GLOBALS['theme']; } else { $th = PnSessionGetVar('theme'); } 
        
        //Visualizzo
        $smarty->display('themes/'.$th.'/'.($GLOBALS['themepage'] ? ''.$GLOBALS['themepage'].'' : ''.(($_GET['type'] == 'admin') ? 'admin.html' : 'theme.html').'').'');  
        
        $content = ob_get_contents();
        

        //se trovo che l'amministratore ha assunto l'identità di un utente visualizzo la barra
        //per tornare amministratore in un solo click
        if ((PnSessionGetVar('uidfake')) && ($_GET['theme'] != 'Modal')) {
        
            
            $this->DivStart('background-color: #AE0004; width: 100%; height: 40px; padding-top: 8px; position: fixed; bottom:0; z-index:999;');
            
            //Statusmsg
            $this->DivGridStart('container');   
            $this->DivGridRowOpen(array('class'=>'col-md-6'));        
        
            $this->Paragraph(array('class'=>'pull-left', 'style'=>'color: #FFFFFF;'), 'Identit&agrave; attuale - <strong>'.PnUserGetVar('email').'</strong>');
            
            $this->DivGridRowClose();
            $this->DivGridRowOpen(array('class'=>'col-md-6 pull-right'));        
        
            $this->Button(array('class'=>'pull-right btn btn-xs btn-danger'), 'Lascia identit&agrave;', pnModUrl('Users', 'user', 'IdentityRevert')); 
                        
            
            $this->DivGridRowClose();
            $this->DivGridEnd();     
            //
    
            $this->DivEnd();
            
        }


        //QUI POSSO INSERIRE UNA BARRA CHE VOGLIO VISUALIZZARE IN OGNI PAGINA DEL SITO
        
        
        ob_end_clean();

        $this->output .= $content;
    }
    //////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////  
    function EndPage()
    {
        
        //Parametro globale
        global $mysqli;
        
        //Inizio
        ob_start();

        //Includo il footer
        include_once('footer.php');

        //Assegno content
        $content = ob_get_contents();
        
        //Pulisco
        ob_end_clean();

        //Chiudo le connessioni al db
        //$mysqli->close();
        
        
        //Assegno la variabile
        $this->output .= $content;
    }
    ////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////   
    // Testo HTML V.1.0
    ////////////////////////////////////////////////////////////////////////////     
    function TextHTML($text = '', $help = '')
    {
        
        $content = '';
        
        //Inserisco l'help
        if ($help != '')   { 
        
        $content .= "\t".'<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip() }) </script>'."\n\t";
        $content .= "\t".'<i class="material-icons dd-middle" style="color:#8BC3E0; cursor: pointer;" data-toggle="tooltip" data-placement="right" title="'.$help.'">info</i> '."\n\n"; 
        
        }        
        
        
        //Inserisco il testo
        $content .= "\t\t".$text."\n";

   
        $this->output .= $content;
    }
    
    ////////////////////////////////////////////////////////////////////////////    
    // Spazio libero V.1.0
    ////////////////////////////////////////////////////////////////////////////       
    function Linebreak($num = 1)
    {
        //Inserisco interruzione
        $this->output .= str_repeat("\t<br />\n", $num);
    }
    
    
    ////////////////////////////////////////////////////////////////////////////
    // Linea divisoria V.1.0
    ////////////////////////////////////////////////////////////////////////////      
    function HorizontalRule($attribute = array())
    {
        
        //Apro
        $content = "\t<hr";
        
        //Inserisco tutti i vari attributi
        foreach ($attribute as $key => $value)  { $content .= ' '.$key.'="'.$value.'"'; }        
        
        //Chiudo
        $content .= ">\n";
        
        $this->output .= $content;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Inizio tabella ADMIN V.1.0
    ////////////////////////////////////////////////////////////////////////////
    function TableStart($attribute = array())
    {  
        //Inizializza la variabile
        $content = '';
        
        // Wrap the user table in our own invisible table to make the title fit properly
        if ($attribute['noattr_javaorder'] == 1) {
        
        //Aggiungo lo script per inserire javascript datatables
        $content .= '<script type="text/javascript"> $(document).ready( function () { $("#'.$attribute['id'].'").DataTable(); } ); </script>';
    
        }
        
        
        $content .= "\r<!-- TableStart -->\r\n";
        $content .= "\t\t".'<table';
        
        foreach ($attribute as $key => $value)  { if (substr($key, 0, 7) != 'noattr_') { $content .= ' '.$key.'="'.$value.'"'; } }
       
        $content .= ">\n";
        
        $this->output .= $content;
    }    
    ////////////////////////////////////////////////////////////////////////////
     // Apre una riga V.1.0
    ////////////////////////////////////////////////////////////////////////////
    function TableRowStart($attribute = array())
    {
        //Inizializzo la variabile
        $content = '';

        $content .= "\t\t\t<!-- TableRowStart -->\n";
        
        //Se devo aggiungere anche un thead
        if ($attribute['noattr_thead'] == 1) { $content .= "\t\t\t".'<thead>'."\n"; }        
        
        //Content
        $content .= "\t\t\t\t".'<tr';
        
        foreach ($attribute as $key => $value)  { if (substr($key, 0, 7) != 'noattr_') { $content .= ' '.$key.'="'.$value.'"'; } }        
        
        //Chiudo
        $content .= ">\n";
        
        $this->output .= $content;
    }    
    ////////////////////////////////////////////////////////////////////////////
    // Apre una cella V.1.0
    ////////////////////////////////////////////////////////////////////////////
    function TableColStart($attribute = array())
    {
 
        $content = "\r\t\t\t<!-- TableColStart -->\n";
        $content .= "\t\t\t\t\t".'<td';
        
        foreach ($attribute as $key => $value)  { if (substr($key, 0, 7) != 'noattr_') { $content .= ' '.$key.'="'.$value.'"'; } }
        
        $content .= ">\n";
        
        
        $this->output .= $content;
    }
    
    ////////////////////////////////////////////////////////////////////////////
     // Chiude una cella V.1.0
    ////////////////////////////////////////////////////////////////////////////    
    function TableColEnd()
    {
        $this->output .= "\t\t\t\t<!-- TableColEnd -->\n</td>\n";
    }

    ////////////////////////////////////////////////////////////////////////////
    // Apre una cella V.1.0
    ////////////////////////////////////////////////////////////////////////////
    function TableHeadStart($attribute)
    {
 
        $content = "\r\t\t\t<!-- TableHeadStart -->\n";
        $content .= "\t\t\t\t".'<th';
        
        foreach ($attribute as $key => $value)  { if (substr($key, 0, 7) != 'noattr_') { $content .= ' '.$key.'="'.$value.'"'; } }
        
        $content .= ">\n";
        
        
        $this->output .= $content;
    }
    
    ////////////////////////////////////////////////////////////////////////////
     // Chiude una cella V.1.0
    ////////////////////////////////////////////////////////////////////////////    
    function TableHeadEnd()
    {
        $this->output .= "\t\t\t\t<!-- TableHeadEnd -->\n</th>\n";
    }
    
    ////////////////////////////////////////////////////////////////////////////
     // Chiude una riga V.1.0
    ////////////////////////////////////////////////////////////////////////////  
    function TableRowEnd($attribute = array())
    {
        //Inizializzo la variabile
        $content = '';

        $content .= "\t\t\t<!-- TableRowEnd --></tr>\n";
        
        //Se devo aggiungere anche un thead
        if ($attribute['noattr_thead'] == 1) { $content .= "\t\t\t".'</thead>'."\n"; }        
        
        //Se devo aggiungere anche un thead
        if ($attribute['noattr_tbody'] == 1) { $content .= "\t\t\t".'<tbody>'."\n"; }
        
        $this->output .= $content;    


    }
    
    ////////////////////////////////////////////////////////////////////////////
     // Chiude una tabella V.1.0
    ////////////////////////////////////////////////////////////////////////////     
    function TableEnd($attribute = array())
    {
        //Inizializzo la variabile
        $content = '';

        //Se devo aggiungere anche un thead
        if ($attribute['noattr_tbody'] == 1) { $content .= "\t\t\t".'</tbody>'."\n"; }
        
        //content
        $content .= "<!-- TableEnd -->\n</table>\n";
        
        $this->output .= $content;    

    }
    //////////////////////////////////////////////////////////////////////////// 
    // Genera un campo con pulsante sfoglia V.1.0
    ////////////////////////////////////////////////////////////////////////////  
    function ScriptInclude($scriptname)
    {
        if (empty($scriptname)) return;
        
        $content = '<script src="'.$scriptname.'"></script>';
        
        $this->output .= $content;
    }
    //////////////////////////////////////////////////////////////////////////// 
    // Apre un livello V.1.0
    ////////////////////////////////////////////////////////////////////////////    
    function DivStart($style = '', $id = '', $class = '', $more = '')
    {
        $content = "\r<div " . (!empty($id) ? " id=\"$id\"" : "") . (!empty($class) ? " class=\"$class\"" : "") . (!empty($style) ? " style=\"$style\"" : "") ."".(!empty($more) ? " $more" : "") .">\n";
        
        $this->output .= $content;
    }
    
    //////////////////////////////////////////////////////////////////////////// 
    // Chiude un livello V.1.0
    ////////////////////////////////////////////////////////////////////////////      
    function DivEnd ()
    {
        $this->output .= "\n</div>\n";
    }
    
    ////////////////////////////////////////////////////////////////////////////   
    // Apre un livello V.1.0
    ////////////////////////////////////////////////////////////////////////////    
    function DivBoxStart ($title = '', $class = 'divbox', $image = '')
    {
        $content  = "\r<div " . (!empty($class) ? " class=\"$class\"" : "") . ">";
        $content .= "<fieldset><legend>". (!empty($image) ? " <img align=\"absmiddle\" src=\"$image\">" : "") ." $title</legend>\n";
        
        $this->output .= $content;
    }
    
    //////////////////////////////////////////////////////////////////////////// 
    // Chiude un livello V.1.0
    ////////////////////////////////////////////////////////////////////////////      
    function DivBoxEnd ()
    {
        $this->output .= "<!-- DivEnd -->\n</fieldset></div>\n";
        
    }
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function DivGridStart($type = "container-fluid", $attribute = array()) {
    
      $content  = '<div class="'.$type.'"';
      
      foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }      
      
      $content .= ">\n"; 
       
      $content .= "\t".'<div class="row">'."\n";    

      $this->output .= $content;

    }
      
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function DivGridNestedStart($attribute = array()) {
    
      $content = "\t\t".'<div class="row"';
      
      foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }      
      
      $content .= ">\n";    

      $this->output .= $content;

    }    
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap    
    function DivGridAdd($attribute = array(), $text = '') {
    
    
    $content = "\t\t".'<div';
    
    foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }  
    
    $content .= '>'."\n";
    $content .= "\t\t".$text."\n";
    $content .= "\t\t".'</div>'."\n";
    
    $this->output .= $content;
    
    }   

////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap    
    function DivGridRowOpen($attribute = array()) {
    
    
    $content = "\t\t".'<div';
    
    foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }  
    
    $content .= '>'."\n";
    $this->output .= $content;
    
    }   
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap    
    function DivGridRowClose() {
    
    $content .= "\t\t".'</div>'."\n";
    
    $this->output .= $content;
    } 
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function DivGridEnd() {

    $content = "\t".'</div>'."\n".'</div>'."\n\n";
    
    $this->output .= $content;
    
    }
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function DivGridNestedEnd() {

    $content = "\t".'</div>'."\n\n";
    
    $this->output .= $content;
    
    }

////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function Paragraph($attribute = array(), $text = '') {

    //Definisco la variabile
    $attr = '';
    
    foreach ($attribute as $key => $value)  { $attr .= ''.$key.'="'.$value.'"'; }

    $content = "\t".'<p '.$attr.'>'.$text.'</p>'."\n";
    
    $this->output .= $content;
    
    }

////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function Button($attribute = array(), $text = '', $link = '') {


    $content  = "\t".'<button type="button"';
    
    foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }    
    
    //Blocco interfaccia se necessario
    if ($attribute['blockUI'] == 1) { $blockui = '$.blockUI();'; } else { $blockui = ''; }
    
    $content .= ''.(($link != '') ? ' onclick="'.$blockui.' javascript:location=\''.$link.'\';"' : '').'>'.$text.'</button>'."\n";
    
    $this->output .= $content;
    
    }
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function ButtonAjax($attribute = array(), $text = '', $link = '') {


    $content  = "\t".'<button type="button"';
    
    foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }    
    
    $content .= ''.(($link != '') ? ' onclick="'.$link.'"' : '').'>'.$text.'</button>'."\n";
    
    $this->output .= $content;
    
    }

////////////////////////////////////////////////////////////////////////////
/////////////// Radiogroup
    function StatusMsg()
    {
        
         //Definisco content
         $content = '';
        
        
        if (pnSessionGetVar('statusmsg') != "") { 
        
          $content  = "\t".'<div class="alert alert-success alert-dismissible" role="alert">'."\n";
          $content .= "\t\t".'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'."\n";        
          $content .= "\t\t".'<span class="glyphicon glyphicon-ok-sign" style="color:#2F992F;" aria-hidden="true"></span> &nbsp; '.pnSessionGetVar('statusmsg').''."\n";  
          $content .= "\t".'</div>'."\n"; 
          
          pnSessionDelVar('statusmsg');
        
       }
       
        if (pnSessionGetVar('warningmsg') != "") { 
        
          
          
          $content  = "\t".'<div class="alert alert-warning alert-dismissible" role="alert">'."\n";
          $content .= "\t\t".'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'."\n";        
          $content .= "\t\t".'<span class="glyphicon glyphicon-info-sign" style="color:#F0B800;" aria-hidden="true"></span> &nbsp; '.pnSessionGetVar('warningmsg').''."\n";  
          $content .= "\t".'</div>'."\n"; 
          
          pnSessionDelVar('warningmsg');
        
       }       
       
        if (pnSessionGetVar('errormsg') != "") { 
        
          $content  = "\t".'<div class="alert alert-danger alert-dismissible" role="alert">'."\n";
          $content .= "\t\t".'<button type="button" class="close" data-dismiss="alert"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'."\n";        
          $content .= "\t\t".'<span class="glyphicon glyphicon-exclamation-sign" style="color:red;" aria-hidden="true"></span> &nbsp; '.pnSessionGetVar('errormsg').''."\n";  
          $content .= "\t".'</div>'."\n"; 
          
          pnSessionDelVar('errormsg');
        
       }           
       

        $this->output .= $content;
    }
    
    
    //////////////////////////////////////////////////////////////////////////// 
     // Genero il pulsante submit V.1.0
    //////////////////////////////////////////////////////////////////////////// 
    function FormOpen($action = '', $method = 'post', $name = '', $attribute = array())
    {
    
        $randform = rand(10, 99);
    
        $content = "\t<!-- FormStart -->\n<form"
                    .' role="form" action="'.$action.'" method="'.$method.'" name="'.$name.'" id="'.$name.(($attribute['noattr_single'] != 1) ? ''.$randform.'' : '').'"';
                           
        foreach ($attribute as $key => $value)  { if (substr($key, 0, 7) != 'noattr_') {$content .= ''.(($value == '') ? ' '.$key.'' : ' '.$key.'="'.$value.'"'."\n").''; }  }      
        
        $content .= ' enctype="multipart/form-data">'."\n";
        
        //Genero un campo hidden casuale per la sicureza del form
        $hashkey = rand(1000000, 99999999); 
        
        if($attribute['noattr_validation'] == '1') { $content .= "\t".'<script>$(document).ready(function() { $(\'#'.$name.(($attribute['noattr_single'] != 1) ? ''.$randform.'' : '').'\').bootstrapValidator(); });</script>'."\n"; }
        
        //Imposto la variabile hashkey - IMPORTANTE
        PnSessionSetVar('form_hashkey', $hashkey);
      
        //Vai col contenuto del server                                       
        $content .= "\t".'<input type="hidden" name="form_hashkey" value="'.$hashkey.'">'."\n";        
        
        
        $this->output .= $content;
    }

    ////////////////////////////////////////////////////////////////////////////
    // Chiudo il form V.1.0
    ////////////////////////////////////////////////////////////////////////////      
    function FormClose()
    {
    
    $this->output .= "\n<!-- FormEnd -->\n</form>\n";
    
    }
    
    ///////////////////////////////////////////////////////////////// 
    // Genera un input di tipo nascosto V.1.0
    ////////////////////////////////////////////////////////////////////////////     
    function FormHidden($fieldname = '', $value = '')
    {
        if (empty ($fieldname)) return;
        
        //Se è un array
        if (is_array($fieldname)) {
            
            //Inserisco tutti i campi nascosti)
            foreach ($fieldname as $n => $v) { $content .= "".'<input type="hidden" name="' . $n . '" id="' . $n . '" value="' . $v . '" />'."\n"; }
        
        //altrimenti
        } else {
            
            //Metto l'unico campo
            $content = "".'<input type="hidden" name="' . $fieldname . '" id="' . $fieldname . '" value="' . $value . '" />'."\n";
        }
        
        
        $this->output .= $content;
    }     
    
    ////////////////////////////////////////////////////////////////////////////  
     // Genera un input di tipo test V.1.0
    ////////////////////////////////////////////////////////////////////////////     
    function Text($attribute = array(), $type = 'text', $name = '', $label = '')
    {

        $content  = "\t".'<div class="form-group '.(($attribute['color'] != '') ? ''.$attribute['color'].'' : '').'">'."\n";
        
        //Inserisco lo script
        if ($attribute['help'] != '')   { $content .= "\t\t\t".'<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip() }) </script>'."\n";  }
        
        //Controllo etichetta
        if ($label != '')               { $content .= "\t\t".'<label style="padding-left: 2px;" for="'.$name.'">'.$label.''.""; }

        if ($attribute['help'] != '')   { $content .= "\t\t\t".'<i class="material-icons dd-middle" style="color:#8BC3E0; cursor: pointer;" data-toggle="tooltip" data-placement="right" title="'.$attribute['help'].'">info</i> '."\n\n"; }
        if ($label != '')               { $content .= "\t\t".'</label>'."\n"; }        
        if (($attribute['addon'] != '') || ($attribute['addon-btn'] != ''))   { $content .= "\t\t".'<div class="input-group">'."\n"; }
        if ( $attribute['addon'] != '') { $content .= "\t\t".'<span class="input-group-addon">'.$attribute['addon'].'</span>'."\n";  }
        
        $content .= "\t\t\t".'<input type="'.$type.'" name="'.$name.'" id="'.$name.'"';
        
        foreach ($attribute as $key => $value)  { if (!preg_match('/addon|info/', $key)) { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; } }         
        
        $content .= "/>\n";        
        
        //Se c'è un pulsante a destra
        if ($attribute['addon-btn'] != '')   { $content .= "\t\t".'<span class="input-group-btn">'."\n";  }
        if ($attribute['addon-btn'] != '')   { $content .= "\t\t\t".''.$attribute['addon-btn'].''."\n";   }
        if ($attribute['addon-btn'] != '')   { $content .= "\t\t".'</span>'."\n";                         }
                
        
        if ($attribute['addon'] != '')  { $content .= "\t\t".'</div>'."\n"; }        
            
        $content .= "\t".'</div>'."\n\n";
       
        if ($attribute['info'] != '')   { $content .= "\t\t\t".'<span id="helpBlock" class="small help-block" style="padding-left: 2px;">'.$attribute['info'].'</span>'."\n"; }
        
                    
        $this->output .= $content;
    }
    ////////////////////////////////////////////////////////////////////////////  
     // Genera un input di tipo test V.1.0
    ////////////////////////////////////////////////////////////////////////////   
    function TextAutocomplete($attribute = array(), $type = 'text', $name = '', $label = '') {

        $content .= "<script> $(function () {";
        
        //sblocco
        if (($attribute['value'] != '') && ($attribute['value'] != 0)) {  
        
        $content .= "document.getElementById('" . $name . "-input').value = '".$attribute['valuetxt']."';";
        $content .= "document.getElementById('" . $name . "-input').disabled = true;";
        $content .= "document.getElementById('" . $name . "-btn').disabled = false;"; 
        $content .= "document.getElementById('" . $name . "').value = '".$attribute['value']."';";

        } else {
        
        $content .= "document.getElementById('" . $name . "-input').value = '';";        
        $content .= "document.getElementById('" . $name . "-input').disabled = false;";
        $content .= "document.getElementById('" . $name . "-btn').disabled = true;";
        $content .= "document.getElementById('" . $name . "').value = '0';";
        
                
        }


        
        //typeahead
        $content .= "$('#" . $name . "-input').typeahead({onSelect: function(item) {
                                                          document.getElementById('" . $name . "').value = item.value;
                                                          document.getElementById('" . $name . "-input').disabled = true;
                                                          document.getElementById('" . $name . "-btn').disabled = false;";
        

        
        
        
        $content .= "}, items: 10,
                        ajax: { url: '" . $attribute['url'] . "', method: 'POST', triggerLength: 1 }
                     });
                     });

                    function AutocompleteUnlock(){  document.getElementById('$name-input').disabled = false;
                                                    document.getElementById('$name-input').value = '';
                                                    document.getElementById('$name').value = 0;
                                                    document.getElementById('$name-btn').disabled = true;
                                                    }
                    
                    </script>";


        //Content
        $content .= "\t" . '<div class="form-group ' . (($attribute['color'] != '') ? '' . $attribute['color'] . '' : '') . '">' . "\n";

        //Inserisco lo script
        if ($attribute['help'] != '') { $content .= "\t\t\t" . '<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip() }) </script>' . "\n"; }
        
        //Controllo etichetta
        if ($label != '') { $content .= "\t\t" . '<label style="padding-left: 2px;" for="' . $name . '">' . $label . '' . ""; }
        
        if ($attribute['help'] != '') { $content .= "\t\t\t" . '<i class="material-icons dd-middle" style="color:#8BC3E0; cursor: pointer;" data-toggle="tooltip" data-placement="right" title="' . $attribute['help'] . '">info</i> ' . "\n\n"; }
        
        if ($label != '') { $content .= "\t\t" . '</label>' . "\n"; }

        $content .= "\t\t" . '<div class="input-group">' . "\n";

        $content .= "\t\t\t" . '<input data-provide="typeahead" type="' . $type . '" name="' . $name . '-input" id="' . $name . '-input"';

        foreach ($attribute as $key => $value) { if (!preg_match('/addon|value|url/', $key)) { $content .= '' . (($key == 'singletag') ? ' ' . $value . '' : ' ' . $key . '="' . $value . '"') . ''; } }
        
        $content .= "/>\n";

        //pulsante a destra
        $content .= "\t\t"   . '<span class="input-group-btn">' . "\n";
        $content .= "\t\t\t" . '<button id="'.$name.'-btn" class="btn btn-default btn-md" type="button" onClick="AutocompleteUnlock()"><i class="material-icons dd-17 dd-middle">touch_app</i></button>' . "\n";
        $content .= "\t\t"   . '</span>' . "\n";

        $content .= "\t\t"   . '</div>' . "\n";

        $content .= "\t\t"   . '<input type="hidden" id="' . $name . '" name="' . $name . '" value="">' . "\n";

        $content .= "\t"     . '</div>' . "\n\n";

        if ($attribute['info'] != '') { $content .= "\t\t\t" . '<span id="helpBlock" class="small help-block" style="padding-left: 2px;">' . $attribute['info'] . '</span>' . "\n"; }

        $this->output .= $content;
    }
    ////////////////////////////////////////////////////////////////////////////  
     // Genera un input di tipo test V.1.0
    ////////////////////////////////////////////////////////////////////////////     
    function ActionDropDown($attribute = array(), $label = 'Dropdown', $items = array(), $hidden = '20')
    {

        $rand = rand(100000, 999999);
        
        $content  = "\t".'<div class="dropdown">'."\n";        
        $content .= "\t\t".'<button type="button" id="dropdown'.$rand.'" data-toggle="dropdown" aria-expanded="true"'; 
        
        //Metto tutti gli attributi
        foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }         
        
        $content .= "/>\n";    

        $content .= "\t\t\t".''.$label.''."\n";
        $content .= "\t\t\t".'<span class="caret"></span>'."\n";
        $content .= "\t\t".'</button>'."\n";        

        $content .= "\t\t".'<ul class="dropdown-menu dropdown-menu-right" role="menu" aria-labelledby="dropdown'.$rand.'">'."\n";   

        foreach ($items as $arrayitem) { 
        
        //Separo url da xajax
        //Questi sono tutti i valori
        //name - nome link
        //url - indirizzo
        //system - qualcosa tipo un divider o comunque codice html
        //ajax - Quello che devo eseguire con ajax
        //icon - eventuale icona
        
        if ($arrayitem['system'] != '') {
        
        //Messaggio di sistema
        $content .= "\t\t\t".''.$arrayitem['system'].''."\n";
        
        } else {
        
        //Link //Controllo se all'interno c'è una parola per eliminare, allora chiedo conferma prima di procedere
        $content .= "\t\t\t".'<li role="presentation">'."\n";
        
        //Inserisco il link
        $content .= "\t\t\t\t".'<a role="menuitem" tabindex="-1" '.(($arrayitem['url'] != '') ? 'href="'.$arrayitem['url'].'"' : 'href="javascript:void(0)"').'" '.((preg_match("/Delete|Remove|Elimina|Cancella/i", $arrayitem['url'])) ? 'onclick="'.$arrayitem['ajax'].' return confirm(\'Confermi questa operazione ?\');"' : 'onclick="'.$arrayitem['ajax'].'"').'>'."\n";        

        //Controllo se ci sono immagini da inserire
        if ($arrayitem['icon'] != '') { $content .= "\t\t\t\t".'<span class="'.$arrayitem['iconposition'].'">'.$arrayitem['icon'].'</span> '."\n"; }
        
        //Link //Controllo se all'interno c'è una parola per eliminare, allora chiedo conferma prima di procedere
        $content .= "\t\t\t\t".''.$arrayitem['name'].''."\n"; 
        
        //Link //Controllo se all'interno c'è una parola per eliminare, allora chiedo conferma prima di procedere
        $content .= "\t\t\t\t".'</a>'."\n";         
        
        //Link //Controllo se all'interno c'è una parola per eliminare, allora chiedo conferma prima di procedere
        $content .= "\t\t\t".'</li>'."\n";  

        }
        
        }

        $content .= "\t\t".'</ul>'."\n";                
        $content .= "\t".'</div>'."\n";
  
        $this->output .= $content;
    }
    
    ////////////////////////////////////////////////////////////////////////////  
    // Genera un input di tipo test V.1.0
    ////////////////////////////////////////////////////////////////////////////
    /////////////// Bootstrap
    function SelectOpen($attribute = array(), $name = '', $label = '') {


       $content  = "\t".'<div class="form-group">'."\n";
       
       if ($label != '')               { $content .= "\t\t".'<label style="padding-left: 2px;" for="'.$name.'">'.$label.''.""; }
       if ($attribute['help'] != '')   { $content .= " ".'<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip() }) </script><span class="glyphicon glyphicon-question-sign" style="color:#8BC3E0;" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="'.$attribute['help'].'"></span>'.""; }
       if ($label != '')               { $content .= "".'</label>'."\n"; }  
       
       $content .= "\t\t".'<select name="'.$name.'" id="'.$name.'"';
       
       foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }

       $content .= ">\n";

       $this->output .= $content;
}

////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function SelectClose($help = '') {

       $content  = "\t\t".'</select>'."\n";
       //help basso
       if ($help != '')  { $content .= "\t\t\t".'<span id="helpBlock" class="help-block">'.$help.'</span>'."\n"; }
       
       $content .= "\t".'</div>'."\n";      
       

       $this->output .= $content;
}

////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function SelectOption($attribute = array(), $label = '', $selected = '') {

       $content = "\t\t\t".'<option';
       
       foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }

       $content .= "".(($selected == $attribute['value']) ? ' selected' : '').">".$label."</option>\n";
       $this->output .= $content;

        }
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function CheckBox($attribute = array(), $name = '', $label = '', $checked = 0) {

       $content  = "\t".'<div class="form-group">'."\n";
       $content .= "\t\t".'<div class="checkbox">'."\n";
       $content .= "\t\t\t\t".'<input type="checkbox" id="'.$name.'" name="'.$name.'" '.(($checked) ? 'checked' : '').'';       
       
       foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }

       $content .= ">\n";
       $content .= "\t\t\t".'<label for="'.$name.'">'.$label.'</label>'."\n";
       $content .= "\t\t</div>\n\t</div>\n";
       
       $this->output .= $content;

      }
    //////////////////////////////////////////////////////////////////////////// 
     // Genero il pulsante submit V.1.0
    ////////////////////////////////////////////////////////////////////////////       
    function Submit($attribute = array(), $label = _SUBMIT)
    {

        
        $content = "\t<!-- FormSubmit -->".'<button type="submit" ';

        foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }               


        $content .= ">\n";  
        $content .= "$label\n";  
        $content .= "</button>\n";         

        $this->output .= $content;
    
    }
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function TextArea($attribute = array(), $name = '', $label = '', $text = '', $help = '') {

        $content  = "\t".'<div class="form-group">'."\n";
        
        if ($label != '')               { $content .= "\t\t".'<label style="padding-left: 2px;" for="'.$name.'">'.$label.''.""; }
        if ($attribute['help'] != '')   { $content .= " ".'<script> $(function () { $(\'[data-toggle="tooltip"]\').tooltip() }) </script><span class="glyphicon glyphicon-question-sign" style="color:#8BC3E0;" aria-hidden="true" data-toggle="tooltip" data-placement="right" title="'.$attribute['help'].'"></span>'.""; }
        if ($label != '')               { $content .= "".'</label>'."\n"; } 
        
           
        $content .= "\t\t\t".'<textarea name="'.$name.'" id="'.$name.'"';
        
        foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }         
        
        $content .= "/>$text</textarea>\n";        
          
        if ($help != '') { $content .= "\t\t\t".'<span id="helpBlock" class="small help-block" style="padding-left: 2px;">'.$help.'</span>'."\n"; }
        
        $content .= "\t".'</div>'."\n\n";
                
        $this->output .= $content;

      }
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function PanelOpen($attribute = array(), $head = '') {

       $content  = "\t".'<div';
       
       foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }
       
       $content .= ">\n";
       if ($head != '') { $content .= "\t\t".'<div class="panel-heading">'.$head.'</div>'."\n"; }       
       
       $content .= "\t\t\t".'<div class="panel-body">'."\n";
       
       $this->output .= $content;

      }
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function PanelClose() {

       $content   = "\t\t\t".'</div>'."\n";
       $content  .= "\t".'</div>'."\n";       

       $this->output .= $content;

      }
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function WellOpen($attribute = array()) {

       $content  = "\t".'<div';
       
       foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }
       
       $content .= ">\n";

       $this->output .= $content;

      }
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function WellClose() {

       $content  .= "\t".'</div>'."\n";       

       $this->output .= $content;

      }

////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function ModalShow($id = '', $title = 'Operazione richiesta', $reload = 1) {
    
      
       $content   = "\t".'<!-- Modal -->'."\n"; 
          
       $content  .= "\t".'<div class="modal fade" tabindex="-1" id="'.$id.'">'."\n";
       $content  .= "\t\t".'<div class="modal-dialog">'."\n";
       $content  .= "\t\t\t".'<div class="modal-content">'."\n";      
       $content  .= "\t\t\t\t".'<div class="modal-header text-left">'."\n";                                
       $content  .= "\t\t\t\t\t".'<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'."\n";                              
       $content  .= "\t\t\t\t\t".'<h4 class="modal-title">'.$title.'</h4>'."\n";                                      
       $content  .= "\t\t\t\t".'</div>'."\n";
       $content  .= "\t\t\t\t".'<div class="modal-body">'."\n";
       $content  .= "\t\t\t\t".'<iframe frameborder="0" width="100%" height="450"></iframe>'."\n";
       $content  .= "\t\t\t\t".'</div>'."\n";
       $content  .= "\t\t\t".'</div>'."\n";
       $content  .= "\t\t".'</div>'."\n";
       $content  .= "\t".'</div>'."\n\n";   
       
       $content  .= "\t".'<!-- Scripts -->'."\n"; 
       
       if ($reload == 1) { $content  .= "\t <script type=\"text/javascript\"> $('#".$id."').on('hidden.bs.modal', function(e) {  location.reload(); }); </script>\n"; }
       
       $content  .= "\t".'<script>$(".modal-open").on("click", function(e) { var src = $(this).attr("data-src"); $("#'.$id.' iframe").attr({"src": src}); }); </script>'."\n\n";
    
    
       $this->output .= $content;
    
    }


////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function ModalConfirm($attribute = array(), $id = '', $title = '', $text = '', $closetext = 'Close', $exectext = 'Submit', $execlink = '') {

       $content   = "\t".'<!-- Modal -->'."\n";
       $content  .= "\t".'<div class="modal fade text-left" id="'.$id.'" tabindex="-1" role="dialog" aria-labelledby="ModalLabel'.$id.'" aria-hidden="true">'."\n";       
       $content  .= "\t\t".'<div class="modal-dialog">'."\n";
       $content  .= "\t\t\t".'<div class="modal-content">'."\n";       
       $content  .= "\t\t\t\t".'<div class="modal-header">'."\n";       
       $content  .= "\t\t\t\t\t".'<button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>'."\n";       
       $content  .= "\t\t\t\t\t".'<p class="modal-title" id="ModalLabel'.$id.'" style="font-size: 1.6em;">'.$title.'</p>'."\n";
       $content  .= "\t\t\t\t".'</div>'."\n";       
       $content  .= "\t\t\t\t".'<div class="modal-body">'."\n";        
       $content  .= "\t\t\t\t".''.$text.''."\n";       
       $content  .= "\t\t\t\t".'</div>'."\n";       
       $content  .= "\t\t\t\t".'<div class="modal-footer">'."\n";       
       $content  .= "\t\t\t\t\t".'<button type="button" class="btn btn-default" data-dismiss="modal">'.$closetext.'</button>'."\n";       
       $content  .= "\t\t\t\t\t".'<button type="button" class="btn btn-success" data-dismiss="modal" '.(($execlink != '') ? ' onclick="$.blockUI(); javascript:location=\''.$execlink.'\';"' : '').'>'.$exectext.'</button>'."\n";       
       $content  .= "\t\t\t\t".'</div>'."\n";
       $content  .= "\t\t\t".'</div>'."\n";       
       $content  .= "\t\t".'</div>'."\n";
       $content  .= "\t".'</div>'."\n";
       
       
       $this->output .= $content;
 
      }

    //////////////////////////////////////////////////////////////////////////// 
    // Impagina una serie di dati V.1.0
    ////////////////////////////////////////////////////////////////////////////
    function Pagination($attribute = array(), $from = 0, $total, $urlt, $perpage = 10)
    {
        
        if ($total <= $perpage) { return; }
        
        $content  = "\t\t".'<nav';

        foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }         
        
        $content .= ">\n";        

        $content .= "\t\t\t".'<ul class="pagination">'."\n";
        
        if ($from == 0) { $class = 'disabled'; } else { $class = ""; }
            
            $url = preg_replace('/%%/', ($from - $perpage), $urlt);
            $content .= "\t\t\t\t".'<li class="'.$class.'"><a href="'.$url.'"><span aria-hidden="true">&laquo;</span><span class="sr-only">Previous</span></a></li>'."\n";

        
        $totalpages = intval($total / $perpage);

        if (($total % $perpage) > 0) { $totalpages++; }
        
        $ii = 0;
        
        if (($from / $perpage) <= 10) { $startindicator = 0; } 
        if (($from / $perpage) > 10)  { $startindicator = ($from / $perpage) - 8; }
        
        for ($curnum = $startindicator; $curnum < $totalpages; $curnum++) {
          
                
                if ($ii <= 15) {
                
                $dove    = $from / $perpage;
                $curpage = $curnum * $perpage;
                
                //Imposto la classe currrent
                if ($dove == $curnum) { $classnested = "active"; } else { $classnested = ""; }
                
                $url = preg_replace('/%%/', $curpage, $urlt);
                $content .= "\t\t\t\t".'<li class="'.$classnested.'"><a href="'.$url.'">'.($curnum + 1).'</a></li>'."\n";

                }
        
        
        $ii++;
        
        }        

           //echo $totalpages;
           
        if ($totalpages == (($from + $perpage) / $perpage)) { $class = 'disabled'; } else { $class = ""; }
            
            $url = preg_replace('/%%/', ($from + $perpage), $urlt);
            $content .= "\t\t\t\t".'<li class="'.$class.'"><a href="'.$url.'"><span aria-hidden="true">&raquo;</span><span class="sr-only">Next</span></a></li>'."\n";
        

        $content .= "\t\t\t".'</ul>'."\n";
        $content .= "\t\t".'</nav>'."\n";     
        
        
        $this->output .= $content;
    }
    ////////////////////////////////////////////////////////////////////////////
    // Impagina una serie di dati V.1.0
    ////////////////////////////////////////////////////////////////////////////
    function MediaObject($attribute = array(), $img, $title, $description)
    {
        
        $content  = "\t\t".'<div class="media">'."\n";
        $content .= "\t\t\t".'<a';

        foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }         
        
        if ($attribute['titlesize'] == '') { $attribute['titlesize'] = "h4"; }
        if ($attribute['bodysize'] == '')  { $attribute['bodysize'] = "0.9em"; }
        
        
        $content .= ">\n";   
        $content .= "\t\t\t\t".'<img src="'.$img.'" data-src="'.$img.'" '.(($attribute['imgwidth'] != '') ? 'width="'.$attribute['imgwidth'].'"' : '').'>'."\n";        
        $content .= "\t\t\t".'</a>'."\n"; 
        $content .= "\t\t\t".'<div class="media-body">'."\n";
        $content .= "\t\t\t".'<'.$attribute['titlesize'].' class="media-heading">'.$title.'</'.$attribute['titlesize'].'>'."\n";        
        $content .= "\t\t\t".'<font style="font-size: '.$attribute['bodysize'].'; color: #999999;">'.$description.'</font>'."\n";  
        $content .= "\t\t\t".'</div>'."\n";         
        $content .= "\t\t".'</div>'."\n";                        
        
        
        $this->output .= $content;
    }
    ////////////////////////////////////////////////////////////////////////////
    // Impagina una serie di dati V.1.0
    ////////////////////////////////////////////////////////////////////////////
    function MediaObjectMaterial($attribute = array(), $name, $title, $description)
    {
        
        $content  = "\t\t".'<div class="media">'."\n";
        $content .= "\t\t\t".'<a';

        foreach ($attribute as $key => $value) { if (substr($key, 0, 7) != 'noattr_') {$content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').'';} }         
                       
        if ($attribute['noattr_titlesize'] == '') { $attribute['noattr_titlesize']    = "h4"; }
        if ($attribute['noattr_bodysize'] == '')  { $attribute['noattr_bodysize'] = "0.9em"; }
        
        
        $content .= ">\n";   
        $content .= "\t\t\t\t".'<i '.(($attribute['noattr_imgcolor'] != '') ? 'style="color:'.$attribute['noattr_imgcolor'].'";' : '').' class="material-icons '.(($attribute['noattr_imgalign'] != '') ? $attribute['noattr_imgalign'] : '').' '.(($attribute['noattr_imgsize'] != '') ? $attribute['noattr_imgsize'] : '').' ">'.$name.'</i>'."\n";        
        $content .= "\t\t\t".'</a>'."\n"; 
        $content .= "\t\t\t".'<div class="media-body">'."\n";
        $content .= "\t\t\t".'<'.$attribute['noattr_titlesize'].' class="media-heading">'.$title.'</'.$attribute['noattr_titlesize'].'>'."\n";        
        $content .= "\t\t\t".'<font style="font-size: '.$attribute['noattr_bodysize'].'; color: #999999;">'.$description.'</font>'."\n";  
        $content .= "\t\t\t".'</div>'."\n";         
        $content .= "\t\t".'</div>'."\n";                        
        
        
        $this->output .= $content;
    }
    ////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap    
    function ListOpen($attribute = array()) {
    
    
    $content = "\t\t".'<ul';
    
    foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }  
    
    $content .= '>'."\n";
    $this->output .= $content;
    
    }   
    ////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap    
    function ListOpenItem($attribute = array(), $text) {
    
    
    $content = "\t\t\t".'<li';
    
    foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }  
    
    $content .= '>'."\n";
    $content .= $text."\n";
    $this->output .= $content;
    
    }   
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap    
    function ListCloseItem() {
    
    $content .= "\t\t\t".'</li>'."\n";
    
    $this->output .= $content;
    }
////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap    
    function ListClose() {
    
    $content .= "\t\t".'</ul>'."\n";
    
    $this->output .= $content;
    }

////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function WizardStart() {

        $content = '<div class="row bs-wizard" style="border-bottom:0;">'."\n";

        $this->output .= $content;
    }

////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function WizardItem($class = 'col-xs-3', $title, $text, $status = 'disabled', $link = '#') {

        //status: complete - active - disabled
        $content = '<div class="' . $class . ' bs-wizard-step ' . $status . '">'."\n";
        $content .= "\t".'<div class="text-center bs-wizard-stepnum">' . $title . '</div>'."\n";
        $content .= "\t".'<div class="progress">'."\n";
        $content .= "\t\t".'<div class="progress-bar">'."\n";
        $content .= "\t\t".'</div>'."\n";
        $content .= "\t".'</div>'."\n";
        $content .= "\t".'<a href = "' . $link . '" class="bs-wizard-dot"></a>'."\n";
        $content .= "\t".'<div class="bs-wizard-info text-center">' . $text . '</div>'."\n";
        $content .= '</div>'."\n";

        $this->output .= $content;
    }

////////////////////////////////////////////////////////////////////////////
/////////////// Bootstrap
    function WizardEnd() {

        $content = '</div>'."\n";

        $this->output .= $content;
    }
    
////////////////////////////////////////////////////////////////////////////

    function SwitchOnOff($attribute = array(), $name = '', $val = '')
    {
    
        $content .= "\t\t\t".'<script> $(function () { $("[name=\''.$name.'\']").bootstrapSwitch(); }) </script>'."\n";       
        $content .= "\t\t\t".'<input name="'.$name.'" value="'.$val.'"';
        
        foreach ($attribute as $key => $value)  { $content .= ''.(($key == 'singletag') ? ' '.$value.'' : ' '.$key.'="'.$value.'"').''; }         
        
        $content .= "/>\n";     
    
        $this->output .= $content;    
    
    }
}
?>