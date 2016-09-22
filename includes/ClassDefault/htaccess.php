<?php
function UpdateHtAccess() 
{

    //Parametro globale
    global $mysqli;
    
    
    /////////////////////////////////////////////////////////
    $fn = $_SERVER['DOCUMENT_ROOT'].'/.htaccess';
    
    
    $search = array(   ',',   '.',   '/',   ' ',   'à',   'è',   'ù',   'ò',   'ì', '(', ')', '_', "'", '!', '?', '\'', '*', '&');
    $replace = array(  '',   '',   '-',   '-',   'a',   'e',   'u',   'o',   'i', '', '', '-', '', '', '', '-', '', 'e');
    

    if (is_writable($fn)) {
  
    //Apro il file
    $handle = fopen($fn, 'w');
    
    //Scrivo nulla in modo da cancellare tutto quello presente
    fwrite($handle, "");

    
    //Inizio a scrivere il necessario
    $head  = "RewriteEngine On\n";
    $head .= "AddHandler cgi-script .cgi\n";
  
   
    $head .= "## Aggiungo i vari file che fanno parte del deflate\n";
    $head .= "AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css\n";
    $head .= "<FilesMatch \"\\.(js|css|html|htm|php|xml)$\">\n";
    $head .= "SetOutputFilter DEFLATE\n";
    $head .= "</FilesMatch>\n\n";
  
    $head .= "## Stabilisco la pagina di errore 404\n";
    $head .= "ErrorDocument 404 /404.html\n\n\n";
  
    
    $head .= "## Riscrivo url friendly\n";
    $head .= "RewriteCond %{REQUEST_FILENAME} !-f\n";
    $head .= "RewriteCond %{REQUEST_FILENAME} !-d\n";
    $head .= "RewriteCond %{REQUEST_URI} !^.*\.(jpg|css|js|gif|png|pdf)$ [NC]\n";    
    $head .= "RewriteRule ^([a-z]{2})\/([^\/]*)\/([^\/]*)\/([^\/]*)\/? index.php?module=$2&type=$3&func=$4&lang=$1 [QSA]\n\n";          


    $head .= "## Controllo Lingua\n";
    $head .= "RewriteRule ^([a-z]{2})(\/?)$ index.php?lang=$1 [QSA]\n"; 
    $head .= "RewriteRule ^([a-z]{2})\/(.*)$ $2?%{QUERY_STRING}&lang=$1 [L]\n\n";

    $head .= "## Metto il www nel caso non ci fosse\n";
    $head .= "RewriteCond %{HTTP_HOST} ^[^.]+\.[^.]+$\n";
    $head .= "RewriteRule ^(.*)$ http://www.%{HTTP_HOST}/$1 [L,R=301]\n\n";
    

    $q = $mysqli->query("SELECT * FROM _htaccess ORDER BY id ASC");
    
    $head .= "\n\n";
    $head .= "##HTACCESS\n";
    
    //Ciclo tutti i risultati
    while($htaccess = $q->fetch_array()) {
    
    $head .= "RewriteRule ^".$htaccess['htaccess']."$ ".$htaccess['url']." [L]\n";
    
    }
    
      
    //Scrivo il file
    fwrite($handle, $head);
    fclose($handle);
  
  }
  
  
  return true;
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function UpdateSiteMap() 
{

    //Parametro globale
    global $mysqli;

    //File per la sitemap
    $filename = $_SERVER['DOCUMENT_ROOT'].'/sitemap.xml';
    
    //Inizio a scrivere
    $body = " <?xml version=\"1.0\" encoding=\"UTF-8\" ?> 
              <urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\" xmlns:xhtml=\"http://www.w3.org/1999/xhtml\">\n\n";
              ?><?php

  
    //Query per htaccess DB
    $o = $mysqli->query("SELECT * FROM _htaccess ORDER BY id DESC");
  
    //Ciclo
    while ($htaccess = $o->fetch_array()) {
  
    
    $body .=  "<url>\n";
    $body .=  "\t<loc>http://".DOMINIO_WWW."/".$htaccess['htaccess']."</loc>\n";
    $body .=  "\t<lastmod>".date("Y-m-d", time())."</lastmod>\n";
    $body .=  "\t<changefreq>weekly</changefreq>\n";
    $body .=  "</url>\n\n";  

    }    

    $body .= "</urlset>"; 


    //Se il file è scrivibile lo scrivo
    if (is_writable($filename)) {
  
        //Apro il file
        $handle = fopen($filename, 'w');
        
        //Scrivo vuoto per cancellare tutto
        fwrite($handle, "");
        
        //Scrivo tutto il necessario
        fwrite($handle, $body);
        
        //Chiudo il file
        fclose($handle);
    
    }



}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////
?>