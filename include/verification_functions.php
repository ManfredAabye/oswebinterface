<?php
// include/verification_functions.php

function generateActivationCode() {
    return bin2hex(random_bytes(16));
}

function sendVerificationEmail($email, $vorname, $nachname, $activationCode) {
    $subject = "Ihr Freischaltcode für " . SITE_NAME;
    $message = "Hallo $vorname $nachname,\n\n";
    $message .= "Ihr Freischaltcode lautet: $activationCode\n\n";
    $message .= "Bitte verwenden Sie diesen Code, um Ihre Registrierung abzuschließen.\n\n";
    $message .= "Mit freundlichen Grüßen,\n";
    $message .= SITE_NAME;

    $headers = "From: noreply@" . parse_url(BASE_URL, PHP_URL_HOST) . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    return mail($email, $subject, $message, $headers);
}

function generateUUID() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}
?>
