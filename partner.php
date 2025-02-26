<?php
$title = "Partner";
include_once 'include/header.php';
?>

<style>
body { font-family: Arial, sans-serif; background-color: <?= SECONDARY_COLOR ?>; padding: 10px; color: <?= PRIMARY_COLOR ?>; }

main {width: 50%; margin: 2em auto; padding: 2em; background-color: #ffffff; border: 1px solid #ccc; box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); border-radius: 5px;} 
h2 {color: #333;} 
form label {display: block; margin-bottom: 0.5em; color: #333;} 
form input[type="text"], form input[type="password"] {width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px;} 
form input[type="submit"] {padding: 5px 20px; background-color: #007BFF; color: #ffffff; border: none; border-radius: 4px; cursor: pointer;} 
form input[type="submit"]:hover {background-color: #0056b3;}
</style>
<body>
<main>
    <h2><?php echo SITE_NAME; ?> Partner Overview</h2>
    <p>All information related to the Partner can be found here.</p>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Validate and sanitize user input
        $vorname1 = filter_input(INPUT_POST, 'vorname1', FILTER_SANITIZE_STRING);
        $nachname1 = filter_input(INPUT_POST, 'nachname1', FILTER_SANITIZE_STRING);
        $vorname2 = filter_input(INPUT_POST, 'vorname2', FILTER_SANITIZE_STRING);
        $nachname2 = filter_input(INPUT_POST, 'nachname2', FILTER_SANITIZE_STRING);
        $reg_pass = filter_input(INPUT_POST, 'reg_pass', FILTER_SANITIZE_STRING);

        if ($vorname1 && $nachname1 && $vorname2 && $nachname2 && $reg_pass) {
            if (in_array($reg_pass, $registration_passwords_partner)) {
                $con = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

                // Get PrincipalID of person 1
                $stmt1 = $con->prepare("SELECT PrincipalID FROM UserAccounts WHERE FirstName = ? AND LastName = ?");
                $stmt1->bind_param("ss", $vorname1, $nachname1);
                $stmt1->execute();
                $stmt1->bind_result($principalID1);
                $stmt1->fetch();
                $stmt1->close();

                // Get PrincipalID of person 2
                $stmt2 = $con->prepare("SELECT PrincipalID FROM UserAccounts WHERE FirstName = ? AND LastName = ?");
                $stmt2->bind_param("ss", $vorname2, $nachname2);
                $stmt2->execute();
                $stmt2->bind_result($principalID2);
                $stmt2->fetch();
                $stmt2->close();

                // Check if both PrincipalIDs were found
                if ($principalID1 && $principalID2) {
                    // Update userprofile for person 1
                    $stmt3 = $con->prepare("UPDATE userprofile SET profilePartner = ? WHERE useruuid = ?");
                    $stmt3->bind_param("ss", $principalID2, $principalID1);
                    $stmt3->execute();
                    $stmt3->close();

                    // Update userprofile for person 2
                    $stmt4 = $con->prepare("UPDATE userprofile SET profilePartner = ? WHERE useruuid = ?");
                    $stmt4->bind_param("ss", $principalID1, $principalID2);
                    $stmt4->execute();
                    $stmt4->close();

                    echo "<p>Partner information updated successfully!</p>";
                } else {
                    echo "<p>Could not find both users in the database.</p>";
                }

                mysqli_close($con);
            } else {
                echo "<p>Invalid registration password.</p>";
            }
        } else {
            echo "<p>Please fill in all fields correctly.</p>";
        }
    }
    ?>

    <form method="post" action="">
        <label for="vorname1">Vorname Person 1:</label>
        <input type="text" id="vorname1" name="vorname1" required><br>
        <label for="nachname1">Nachname Person 1:</label>
        <input type="text" id="nachname1" name="nachname1" required><br>
        <label for="vorname2">Vorname Person 2:</label>
        <input type="text" id="vorname2" name="vorname2" required><br>
        <label for="nachname2">Nachname Person 2:</label>
        <input type="text" id="nachname2" name="nachname2" required><br>
        <label for="reg_pass">Registrierungspasswort: (Dies muss beim Admin beantragt werden.)</label>
        <input type="password" id="reg_pass" name="reg_pass" required><br>
        <input type="submit" value="Update Partner">
    </form>
</main>
</body>
