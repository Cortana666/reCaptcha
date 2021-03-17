<?php
    require_once 'Recaptcha.php';
    $oRecaptcha = new Recaptcha();
    $oRecaptcha->sGRecaptchaResponse = $_POST['g-recaptcha-response'];
    $oRecaptcha->sSecret = "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx";
    $res = $oRecaptcha->check();
    echo $res;
    exit;
?>