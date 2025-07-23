<!DOCTYPE html>
<html>
<head>
    <title>PHP-Oracle Form</title>
</head>
<body>
    <?php
    // Oracle database credentials
    $host = 'LAPTOP-SE9D6OLF';
    $port = '1521';
    $dbname = 'DBMS_TEST';
    $user = 'DBMS_TEST';
    $password = '1234';

    // Establish the database connection
    try {
        $conn = new PDO("oci:dbname=(DESCRIPTION =
        (ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))
        (CONNECT_DATA =
          (SERVER = DEDICATED)
          (SERVICE_NAME = XE)
        )
      )", $user, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        echo "Connected to Oracle database successfully!";
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $name = $_POST["name"];
        $email = $_POST["email"];

        // Perform database operations
        try {
            // Prepare the SQL statement
            $stmt = $conn->prepare("INSERT INTO persons(name, email) VALUES (:name, :email)");

            // Bind the parameters
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':email', $email);

            // Execute the statement
            $stmt->execute();

            echo "Data inserted successfully!";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    ?>

    <h2>PHP-Oracle Form</h2>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required><br><br>
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        <input type="submit" value="Submit">
    </form>
</body>
</html>
