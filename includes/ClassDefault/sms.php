<?php
//////////////////////////////////////////////////////////////////////////// 
//////////////////////////////////////////////////////////////////////////// 
function SendSms($from, $to, $text)
{

        //Sostituisco il doppio zero con un +
        if (substr($to, 0, 2) == '00') { $to = '+'.substr($to, 2); }
               
        //INizializzo Curl
        $ch = curl_init();
        
        //Passo tutte le varie variabili a curl
        curl_setopt($ch, CURLOPT_URL, "https://www.voicetrading.com/myaccount/sendsms.php?username=".SMS_USERNAME."&password=".SMS_PASSWORD."&from=".$from."&to=".$to."&text=".urlencode($text)."");
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_HEADER, "Content-Type:application/xml");
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        //Eseguo
        $exec = curl_exec($ch);                      
        
        //CHiudo
        curl_close($ch);
    

    //Preparo il messaggio
    $message = 'HOST  : '.$_SERVER['HTTP_HOST'].'
                URL   : '.$_SERVER['REQUEST_URI'].'
                FROM  : '.$from.'
                TO    : '.$to.'
                TEXT  : '.urlencode($text).'
                LOGIN : '.PnSessionGetVar('uid').'
                
                RETURN : '.$exec.'';
                
                
    
    //Invio una mail di controllo
    pnMail(''.MAIL_SERVICE.'', 'Invio SMS '.INTESTAZIONE_MAIL.'', $message, 'SMS Service', ''.MAIL_SERVICE.'', 1, '', '');    
    
    //XML
    $xml = new SimpleXMLElement($exec);
    
    //Ritorno il risultato
    $ret = array('result'=>$xml->result, 'description'=>$xml->resultstring);     

return $ret;

}
//////////////////////////////////////////////////////////////////////////// 
////////////////////////////////////////////////////////////////////////////     
?>