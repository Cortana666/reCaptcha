<?php
    require_once 'Recaptcha.php';
    $aConfig['g-recaptcha-response'] = $_POST['g-recaptcha-response'];
    $aConfig['secret'] = "xxxxxxxxxx";
    $oRecaptcha = new Recaptcha($aConfig);
    $res = $oRecaptcha->check();
    echo json_encode($res);
    exit;
?>