<?php # Script 10.3 - edit_user.php
// This page is for editing a user record.
// This page is accessed through view_users.php

$page_title = "Edit a User";
include('includes/header.html');
echo '<h1>Edit a User</h1>';

// Check for a valid user ID, through GET or POST:
if ( (isset($_GET['id'])) && (is_numeric($_GET['id'])) ) {
    $id = $_GET['id'];
} elseif ( (isset($_POST['id'])) && (is_numeric($_POST['id'])) ) {
    $id = $_POST['id'];
} else { // No valid ID, kill the script
    echo '<p class="error">This page has been accessed in error.</p>';
    include('includes/footer.html');
    exit();
}

require('../mysqli_connect.php');

// Check if the form has been submitted:
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $errors = [];

    // Check for a first name:
    if (empty($_POST['first_name'])) {
        $errors[] = "You forgot to enter a your first name.";
    } else {
        $fn = mysqli_real_escape_string($dbc, trim($_POST['first_name']));
    }

    // Check for a last name:
    if (empty($_POST['last_name'])) {
        $errors[] = "You forgot to enter a password.";
    } else {
        $ln = mysqli_real_escape_string($dbc, trim($_POST['last_name']));
    }

    // Check for an email address:
    if (empty($_POST['email'])) {
        $errors[] = "You forgot to enter an email";
    } else {
        $e = mysqli_real_escape_string($dbc, trim($_POST['email']));
    }

    // Check for a password:
    if (!empty($_POST['pass1'])) {
        if($_POST['pass1'] != $_POST['pass2']) {
            $errors[] = 'Your new password did not match the confirmed password.';
        } else {
            $np  = mysqli_real_escape_string($dbc, trim($_POST['pass1']));
            $update_pass = TRUE;
        }
    }

    if (empty($errors)) { // If everything's OK
        
        // Test for unique email address:
        $q = "SELECT user_id FROM users WHERE email='$e' AND user_id != $id";
        $r = @mysqli_query($dbc, $q);
        if (mysqli_num_rows($r) == 0) {

            // Make the query:
            if ($update_pass) { // If we are updating the password too
                $q = "UPDATE users SET first_name='$fn', last_name='$ln', email='$e', pass=SHA2('$np', 512)
                WHERE user_id=$id LIMIT 1";
            } else {
                $q = "UPDATE users SET first_name='$fn', last_name='$ln', email='$e'
                WHERE user_id=$id LIMIT 1";
            }

            $r = @mysqli_query($dbc, $q);
            if (mysqli_affected_rows($dbc) == 1) { // If it ran OK.

            // Print a message:
            echo '<p>The user has been edited</p>';

            } else { // If it did not run OK.
                // Public message:
                echo '<p class="error">The user could not be edited due to a system error.
                We apologize for any inconvenience.</p>';
                // Debugging message:
                echo '<p>' . mysqli_error($dbc) . '<br>Query: ' . $q . '</p>';
            } 

        } else { // Already registered.
            echo '<p class="error">The email address has already been registered.</p>';
            echo '<p>Rows: ' . mysqli_num_rows($r) . '</p>';
            
        }
    } else { // Report the errors

        echo '<p class="error>The following error(s) occurred:<br>';
        foreach ($errors as $msg) {
            echo " - $msg<br>\n";
        }
        echo '</p><p>Please try again.</p>';
    } // End of if (empty($errors)) IF..

} // End of submit conditional

// Always show the form...

// Retrieve the user's information
$q = "SELECT first_name, last_name, email FROM users WHERE user_id=$id";
$r = @mysqli_query($dbc, $q);

if (mysqli_num_rows($r) == 1) { // Valid user ID, show the form.
    
    // Get the user's information
    $row = mysqli_fetch_array($r, MYSQLI_NUM);
    
    // Create the form:
    echo '<form action="edit_user.php" method="post">
            <p>
                First Name: <input type="text" name="first_name" size="15" maxlength="15" value="' . $row[0] . '">
            </p>
            <p>
                Last Name: <input type="text" name="last_name" size="15" maxlength="30" value="' . $row[1] . '">
            </p>
            <p>
                Email: <input type="email" name="email" size="20" maxlength="60" value="' . $row[2] . '">
            </p>
            <p>
                New Password: <input type="password" name="pass1" size="10" maxlength="20">
            </p>
            <p>
                Confirm New Password: <input type="password" name="pass2" size="10" maxlength="20">
            </p>
            <p>
                <input type="submit" name="submit" value="Submit">
            </p>
            <input type="hidden" name="id" value="' . $id . '">
        </form>';

} else { // Not a valid user ID.
    echo '<p class="error">This page has been accessed in error.</p>';
}

mysqli_close($dbc);

include('includes/footer.html');
?>