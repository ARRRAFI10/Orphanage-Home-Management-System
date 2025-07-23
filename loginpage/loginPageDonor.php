<?php
session_start();

// Database connection details
$host = "localhost/XE";
$username = "DBMS_TEST";
$password = "1234";

// Establish database connection
$conn = oci_connect($username, $password, $host);
if (!$conn) {
    $error = oci_error();
    die("Database connection failed: " . $error['message']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve username and password from the login form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the username starts with 'D' (donor ID)
    if (strpos($username, 'Dn') === 0) {
        // Prepare SQL query
        $query = "SELECT * FROM Login WHERE Login_ID = :username";
        $stmt = oci_parse($conn, $query);
        oci_bind_by_name($stmt, ":username", $username);

        // Execute the query
        $result = oci_execute($stmt);
        if (!$result) {
            $error = oci_error($stmt);
            die("Query execution failed: " . $error['message']);
        }

        // Check if a matching record was found
        if (oci_fetch($stmt)) {
            // Compare the password from the database with the entered password
            $dbPassword = oci_result($stmt, 'LOGIN_PASSWORD');
            if ($password == $dbPassword) {
                // Passwords match, store the donor ID in session and redirect to donorPage.php
                $_SESSION['loggedIn'] = $username;
                header("Location: donorPage.php");
                exit;
            }
        }

        // If no matching record found or password doesn't match, display an error message
        $errorMsg = "Invalid username or password.";
        echo "<script>alert('$errorMsg'); window.location.href = 'loginPageDonor.html';</script>";
    } else {
        // User is not authorized, display an error message and redirect to home page
        $errorMsg = "You are not authorized to access this page.";
        echo "<script>alert('$errorMsg'); window.location.href = 'loginPageDonor.html';</script>";
    }
}

// Close the database connection
oci_free_statement($stmt);
oci_close($conn);
?>
