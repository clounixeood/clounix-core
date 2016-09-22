<?php
require_once "../src/facebook.php";
 
$app_id = "100532026777857";
$app_secret = "27ed1806e48cd6605fa0848923bc8239";
 
// Init facebook api.
$facebook = new Facebook(array(
        'appId' => $app_id,
        'secret' => $app_secret,
        'cookie' => true
));
 
// Get the url to redirect for login to facebook
// and request permission to write on the user's wall.
$login_url = $facebook->getLoginUrl(
    array('scope' => 'publish_stream')
);
 
// If not authenticated, redirect to the facebook login dialog.
// The $login_url will take care of redirecting back to us
// after successful login.
if (! $facebook->getUser()) {
    echo <<< EOT
<script type="text/javascript">
top.location.href = "$login_url";
</script>;
EOT;
 
    exit;
}
 
// Do the wall post.
$facebook->api("/me/feed", "post", array(
    message => "Sto utilizzando l'applicazione Voicee per chiamare tutti i cellulari italiani a 5 cent. al minuto senza scatto alla risposta.",
    link => "http://www.voicee.net",
    name => "Voicee",
    caption => "La rivoluzione della telefonia Voip"
));
?>