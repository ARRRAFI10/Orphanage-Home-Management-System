<?php
session_start();

// Check if the donor is logged in, otherwise redirect to the login page
if (!isset($_SESSION['loggedIn'])) {
    header("Location: loginPageDonor.php");
    exit;
}

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

// Retrieve the logged-in donor's information from the database
$donorId = $_SESSION['loggedIn'];
$query = "SELECT d.Donor_ID, d.Donor_Name, d.Address.House_NO AS HouseNo, d.Address.Road_NO AS RoadNo,
          d.Address.Area AS Area, d.Address.District AS District
          FROM Donor d
          WHERE d.Donor_ID = :donorId";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ":donorId", $donorId);
$result = oci_execute($stmt);

if (!$result) {
    $error = oci_error($stmt);
    die("Query execution failed: " . $error['message']);
}

// Fetch the donor information
$row = oci_fetch_assoc($stmt);
if (!$row) {
    echo "No donor information found.";
    exit;
}

$donorId = $row['DONOR_ID'];
$donorName = $row['DONOR_NAME'];
$houseNo = $row['HOUSENO'];
$roadNo = $row['ROADNO'];
$area = $row['AREA'];
$district = $row['DISTRICT'];

// Close the database connection
oci_free_statement($stmt);

// Check if the donation form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the transection details from the form
    $transectionType = $_POST['transectionType'];
    $transectionDate = $_POST['transectionDate'];
    $transectionAmount = $_POST['transectionAmount'];

    // Get the orphanage ID associated with the donation
    $orphanageId = "A1"; // Replace with the actual orphanage ID

    $generatetransectionIdQuery = "BEGIN :result := generatetransectionId; END;";
    $stmtGenerateId = oci_parse($conn, $generatetransectionIdQuery);
    oci_bind_by_name($stmtGenerateId, ":result", $transectionId, 100);
    oci_execute($stmtGenerateId);

    if (empty($transectionAmount) || empty($transectionDate) || empty($transectionType)) {
        // Display an error message and redirect back to the previous page
        echo "<script>alert('Please fill in all the transection information.');</script>";
        echo "<script>window.location.href = 'donorPage.php';</script>";
    } 

    // Prepare the insert statement for transection table
    $insertQuery = "INSERT INTO transection (transection_ID, transection_Type, transection_Date, transection_Amount, Orphanage_ID)
                   VALUES (:transectionId, :transectionType, TO_DATE(:transectionDate, 'YYYY-MM-DD'), :transectionAmount, :orphanageId)";
    $insertStmt = oci_parse($conn, $insertQuery);
    oci_bind_by_name($insertStmt, ":transectionId", $transectionId);
    oci_bind_by_name($insertStmt, ":transectionType", $transectionType);
    oci_bind_by_name($insertStmt, ":transectionDate", $transectionDate);
    oci_bind_by_name($insertStmt, ":transectionAmount", $transectionAmount);
    oci_bind_by_name($insertStmt, ":orphanageId", $orphanageId);

    // Execute the insert statement
    $result = oci_execute($insertStmt);
    $insertd ="INSERT INTO DONATES(TRANSECTION_ID,DONOR_ID)VALUES(:transectionId,:donorId)";
    $stmts = oci_parse($conn,$insertd);
    oci_bind_by_name($stmts, ":transectionId", $transectionId);
    oci_bind_by_name($stmts, ":donorId", $donorId);
    $result = oci_execute($stmts);


    if ($result) {
        // Redirect to the donor page to display the updated transection history
        header("Location: donorPage.php");
        exit;
    } else {
        $error = oci_error($insertStmt);
        die("transection insertion failed: " . $error['message']);
    }
}

// Retrieve the donor's transection history from the database
$query = "SELECT t.transection_ID, t.transection_Type, t.transection_Date, t.transection_Amount
          FROM transection t
          JOIN Donates D ON t.transection_ID = D.transection_ID
          WHERE D.Donor_ID = :donorId
          ORDER BY t.transection_Date DESC";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ":donorId", $donorId);
$result = oci_execute($stmt);

if (!$result) {
    $error = oci_error($stmt);
    die("Query execution failed: " . $error['message']);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Donor Page</title>




    <style>
        /* Body styles */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f7f7f7;
        }

        /* Header styles */
        h2 {
            color: #333;
        }

        /* Table styles */
        table {
            border-collapse: collapse;
            width: 100%;
            background-color: #fff;
            margin-bottom: 20px;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
        }

        /* Form styles */
        .form-container {
            max-width: 400px;
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: 0 auto; /* Center align horizontally */
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
            box-sizing: border-box;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Error message styles */
        .error {
            color: #ff0000;
            margin-bottom: 10px;
        }
    </style>





</head>
<body>
    <h1>Welcome, <?php echo $donorName; ?>!</h1>
    <h3>Your Donor ID: <?php echo $donorId; ?></h3>
    <h3>Your Address: <?php echo "$houseNo, $roadNo, $area, $district"; ?></h3>

    <h2>Add transection</h2>
    <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
        <label for="transectionType">transection Type:</label>
        <select name="transectionType" id="transectionType">
            <option value="Donation">Donation</option>
            <option value="Expenses">Expenses</option>
        </select><br><br>

        <label for="transectionDate">transection Date:</label>
        <input type="date" name="transectionDate" id="transectionDate" required><br><br>

        <label for="transectionAmount">transection Amount:</label>
        <input type="number" name="transectionAmount" id="transectionAmount" required><br><br>

        <input type="submit" value="Add transection">
    </form>

    <h2>transection History</h2>
    <table border="1">
        <tr>
            <th style='background-color: #4CAF50; color: #333;'>transection ID</th>
            <th style='background-color: #4CAF50; color: #333;'>transection Type</th>
            <th style='background-color: #4CAF50; color: #333;'>transection Date</th>
            <th style='background-color: #4CAF50; color: #333;'>transection Amount</th>
        </tr>
        <div class='form-container'>
        <?php
        while ($row = oci_fetch_assoc($stmt)) {
            echo "<tr>";
            echo "<td>" . $row['TRANSECTION_ID'] . "</td>";
            echo "<td>" . $row['TRANSECTION_TYPE'] . "</td>";
            echo "<td>" . $row['TRANSECTION_DATE'] . "</td>";
            echo "<td>" . $row['TRANSECTION_AMOUNT'] . "</td>";

            echo "</tr>";
        }
        echo "</form>";
        oci_free_statement($stmt);
        oci_close($conn);
        ?>
    </table>
</body>
</html>
