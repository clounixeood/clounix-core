<?php
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function pnMail($to, $subject, $message, $mailmitt = MAIL_NOREPLY, $nomemitt = INTESTAZIONE_MAIL, $html = 1, $attachment = '', $nlbr = 1, $grafica = 1)
{
 
        //Parametro globale
        global $mysqli;
        
        
        //Converto gli accapo
        if ($nlbr == 1) { $message = nl2br($message); }
        
        //Required
        require_once('includes/ClassMail/PHPMailerAutoload.php');

 
        //Imposto header
        $header = ' <html><head><style>@import url(http://fonts.googleapis.com/css?family=Roboto:300,400,500,700);</style></head>
                    <body style="font-family: Roboto, Arial; font-weight: 400; padding: 30px 10px 30px 10px; font-size: 14px; color:#444444; background-color: #F0F0F0;">
                       
                    <div style="margin: 0 auto; box-shadow: 5px 5px 2px #444444; background-color: #FFFFFF; padding: 0px 0px 0px 0px; width: 80%; border: 1px solid #C0C0C0;">
                    <div style="margin: 0 auto; padding: 13px; background-color: #FFFFFF; height: 35px;"><img src="http://'.DOMINIO_WWW.'/themes/Default/images/logo.png" height="35"></div>
                    <div style="margin: 0 auto; background-color: #F0F0F0; height: 100px; background-image: url(http://'.DOMINIO_WWW.'/themes/Default/images/head-default.jpg); background-repeat: no-repeat;"></div>
                    <div style="margin: 0 auto; background-color: #FFFFFF; height: 180px; padding: 25px;">';
        
        
        $footer = '</div></body></html>'; 


        //Se includo la grafica
        if ($grafica == 1) { $body = $header.$message.$footer; } else { $body = $message; }

        
        //Se gli indirizzi sono in array
        if (is_array($to)) {
        
        //Ciclo e invio
        foreach ($to as $key => $value) {
        
          $mail = new PHPMailer;
          $mail->isSMTP();
          $mail->Host = MAIL_SMTP_HOST;
          $mail->Username = MAIL_SMTP_USERNAME;
          $mail->Password = MAIL_SMTP_PASSWORD;
          $mail->setFrom($mailmitt, $nomemitt);         
          $mail->addAddress($value);
          $mail->addBCC(MAIL_SERVICE);
          $mail->Subject  = $subject;
          $mail->Body     = $body;
          $mail->IsHTML(true);
          
          if ($attachment) { $mail->addAttachment($attachment); }
        
          $mail->send();
                    
          //Inserisco la mail dentro al db
          $q = $mysqli->query("INSERT INTO _emails (datareg, address, subject, message) VALUES ('".time()."', '$to', '$subject', '$message')");

        }
        
        
        //Se è un indirizzo e-mail singolo
        } else {

          $mail = new PHPMailer;
          $mail->isSMTP();
          $mail->Host = MAIL_SMTP_HOST;
          $mail->Username = MAIL_SMTP_USERNAME;
          $mail->Password = MAIL_SMTP_PASSWORD;
          $mail->setFrom($mailmitt, $nomemitt); 
          $mail->addAddress($to);
          $mail->addBCC(MAIL_SERVICE);
          $mail->Subject  = $subject;
          $mail->Body     = $body;
          $mail->IsHTML(true);
 
          if ($attachment) { $mail->AddAttachment($attachment);  }
          
          $mail->Send();           
        
          //Inserisco la mail dentro al db
          $q = $mysqli->query("INSERT INTO _emails (datareg, address, subject, message) VALUES ('".time()."', '$to', '$subject', '$message')");


        }
        

return $mail;
}
////////////////////////////////////////////////////////////////////////////
?>