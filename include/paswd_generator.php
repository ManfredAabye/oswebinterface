<?php
$title = "Password Service";
include_once 'header.php';
// Passwortgenerator-Funktion
function generatePassword($length = 16) {
    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    $charactersLength = strlen($characters);
    $password = '';
    for ($i = 0; $i < $length; $i++) {
        $password .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $password;
}

// Generiere Passwörter für das Register-Array
$registration_passwords_register = [generatePassword(), generatePassword(), generatePassword(), generatePassword(), generatePassword()];
$registration_passwords_reset = [generatePassword(), generatePassword(), generatePassword(), generatePassword(), generatePassword()];
$registration_passwords_partner = [generatePassword(), generatePassword(), generatePassword(), generatePassword(), generatePassword()];
$registration_passwords_inventory = [generatePassword(), generatePassword(), generatePassword(), generatePassword(), generatePassword()];
$registration_passwords_datatable = [generatePassword(), generatePassword(), generatePassword(), generatePassword(), generatePassword()];
$registration_passwords_listinventar = [generatePassword(), generatePassword(), generatePassword(), generatePassword(), generatePassword()];
$registration_passwords_picreader = [generatePassword(), generatePassword(), generatePassword(), generatePassword(), generatePassword()];
$registration_passwords_mutelist = [generatePassword(), generatePassword(), generatePassword(), generatePassword(), generatePassword()];
$registration_passwords_avatarpicker = [generatePassword(), generatePassword(), generatePassword(), generatePassword(), generatePassword()];
$registration_passwords_economy = [generatePassword(), generatePassword(), generatePassword(), generatePassword(), generatePassword()];
?>


<html>
<style>
   .container {
      max-width: 1000px;
      margin: 0 auto;
      padding: 20px;
      Color:rgb(31, 31, 31);
      background-color:rgb(238, 241, 241);
      border: 1px solid #ddd;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
   }
</style>

<main class="container">
    <h1>Generierte Passwörter</h1>
    <pre>
<?php
    echo '$registration_passwords_register = ["' . implode('", "', $registration_passwords_register) . '"];' . PHP_EOL;
    echo '$registration_passwords_reset = ["' . implode('", "', $registration_passwords_reset) . '"];' . PHP_EOL;
    echo '$registration_passwords_partner = ["' . implode('", "', $registration_passwords_partner) . '"];' . PHP_EOL;
    echo '$registration_passwords_inventory = ["' . implode('", "', $registration_passwords_inventory) . '"];' . PHP_EOL;
    echo '$registration_passwords_datatable = ["' . implode('", "', $registration_passwords_datatable) . '"];' . PHP_EOL;
    echo '$registration_passwords_listinventar = ["' . implode('", "', $registration_passwords_listinventar) . '"];' . PHP_EOL;
    echo '$registration_passwords_picreader = ["' . implode('", "', $registration_passwords_picreader) . '"];' . PHP_EOL;
    echo '$registration_passwords_mutelist = ["' . implode('", "', $registration_passwords_mutelist) . '"];' . PHP_EOL;
    echo '$registration_passwords_avatarpicker = ["' . implode('", "', $registration_passwords_avatarpicker) . '"];' . PHP_EOL;
    echo '$registration_passwords_economy = ["' . implode('", "', $registration_passwords_economy) . '"];' . PHP_EOL;
?>
    </pre>
</main>
</html>

