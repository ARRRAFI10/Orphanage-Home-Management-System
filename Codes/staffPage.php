<?php
session_start();

// Check if the staff is logged in, otherwise redirect to the login page
if (!isset($_SESSION['loggedIn'])) {
    header("Location: loginPageStaff.php");
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

// Retrieve the logged-in staff's information from the database
$staffId = $_SESSION['loggedIn'];
$query = "SELECT s.Staff_ID, s.Staff_Name, s.Staff_Salary, s.Joining_Date, s.Designation,
          s.Address.House_No AS HouseNo, s.Address.Road_No AS RoadNo,
          s.Address.Area AS Area, s.Address.District AS District
          FROM Staff s
          WHERE s.Staff_ID = :staffId";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ":staffId", $staffId);
$result = oci_execute($stmt);

if (!$result) {
    $error = oci_error($stmt);
    die("Query execution failed: " . $error['message']);
}

// Fetch the staff information
if ($row = oci_fetch_assoc($stmt)) {
    $staffId = $row['STAFF_ID'];
    $staffName = $row['STAFF_NAME'];
    $salary = $row['STAFF_SALARY'];
    $joiningDate = $row['JOINING_DATE'];
    $designation = $row['DESIGNATION'];
    $houseNo = $row['HOUSENO'];
    $roadNo = $row['ROADNO'];
    $area = $row['AREA'];
    $district = $row['DISTRICT'];
} else {
    echo "No staff information found.";
    exit;
}

// Close the database connection
oci_free_statement($stmt);

// Retrieve the transaction history of the staff from the database
$query = "SELECT t.Transection_ID, t.Transection_Amount, t.Transection_Date, t.Transection_Type
          FROM Transection t
          INNER JOIN Done_By db ON t.Transection_ID = db.Transection_ID
          INNER JOIN Staff s ON db.Staff_ID = s.Staff_ID
          WHERE s.Staff_ID = :staffId";
$stmt = oci_parse($conn, $query);
oci_bind_by_name($stmt, ":staffId", $staffId);
$result = oci_execute($stmt);

if (!$result) {
    $error = oci_error($stmt);
    die("Query execution failed: " . $error['message']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Page</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
        }

        p {
            margin-top: 5px;
            margin-bottom: 5px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="date"],
        input[type="number"],
        input[type="submit"] {
            padding: 5px;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #f2f2f2;
        }

        .error-message {
            color: red;
            margin-top: 5px;
        }
        
        .container {
            max-width: 600px;
            margin: 0 auto;
        }
        
        .info-section {
            margin-bottom: 20px;
        }
        
        .transaction-form {
            margin-bottom: 20px;
        }
        
        .transaction-history {
            margin-bottom: 20px;
        }
        
        .search-form {
            margin-bottom: 10px;
        }
        
        .search-button {
            margin-left: 10px;
        }
        
        .error-message {
            color: red;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 style='color: #4CAF50;'> Staff Information</h2>
        <div class="info-section">
            <p><strong>Staff ID:</strong> <?php echo $staffId; ?></p>
            <p><strong>Name:</strong> <?php echo $staffName; ?></p>
            <p><strong>Salary:</strong> <?php echo $salary; ?></p>
            <p><strong>Joining Date:</strong> <?php echo $joiningDate; ?></p>
            <p><strong>Designation:</strong> <?php echo $designation; ?></p>
            <p><strong>Address:</strong> <?php echo $houseNo . ', ' . $roadNo . ', ' . $area . ', ' . $district; ?></p>
        </div>

        <h2 style='color: #4CAF50;'>Add Transaction</h2>
        <?php
        // Check if an error message is set
        if (isset($_SESSION['errorMessage'])) {
            echo "<p class='error-message'>" . $_SESSION['errorMessage'] . "</p>";
            unset($_SESSION['errorMessage']);
        }
        ?>
        <div class="transaction-form">
            <form action="processTransection.php" method="post">
                <label for="transactionType">Transaction Type:</label>
                <input type="text" name="transactionType" id="transactionType" required><br>

                <label for="transactionDate">Transaction Date:</label>
                <input type="date" name="transactionDate" id="transactionDate" required><br>

                <label for="amount">Amount:</label>
                <input type="number" name="amount" id="amount" required><br>

                <input type="submit" value="Submit">
            </form>
        </div>

        <h2 style='color: #4CAF50;'>Transaction History</h2>
        <div class="transaction-history">
            <form action="" method="get" class="search-form">
                <label for="searchDate">Search by Date:</label>
                <input type="date" name="searchDate" id="searchDate">
                <input type="submit" value="Search" class="search-button">
            </form>
            <table>
                <tr>
                    <th style='background-color: #4CAF50; color: #333;'>Transaction ID</th>
                    <th style='background-color: #4CAF50; color: #333;'>Transaction Amount</th>
                    <th style='background-color: #4CAF50; color: #333;'>Transaction Date</th>
                    <th style='background-color: #4CAF50; color: #333;'>Transaction Type</th>
                </tr>
                <?php
                // Check if a search date is provided
                $searchDate = isset($_GET['searchDate']) ? $_GET['searchDate'] : null;

                // Retrieve the transaction history based on search date if provided
                $query = "SELECT t.Transection_ID, t.Transection_Amount, t.Transection_Date, t.Transection_Type
                          FROM Transection t
                          INNER JOIN Done_By db ON t.Transection_ID = db.Transection_ID
                          INNER JOIN Staff s ON db.Staff_ID = s.Staff_ID
                          WHERE s.Staff_ID = :staffId";

                if (!empty($searchDate)) {
                    $query .= " AND t.Transection_Date = TO_DATE(:searchDate, 'YYYY-MM-DD')";
                }

                $stmt = oci_parse($conn, $query);
                oci_bind_by_name($stmt, ":staffId", $staffId);

                if (!empty($searchDate)) {
                    oci_bind_by_name($stmt, ":searchDate", $searchDate);
                }

                $result = oci_execute($stmt);

                if (!$result) {
                    $error = oci_error($stmt);
                    die("Query execution failed: " . $error['message']);
                }

                // Fetch the transaction history
                while ($row = oci_fetch_assoc($stmt)) {
                    echo "<tr>";
                    echo "<td >" . $row['TRANSECTION_ID'] . "</td>";
                    echo "<td>" . $row['TRANSECTION_AMOUNT'] . "</td>";
                    echo "<td>" . $row['TRANSECTION_DATE'] . "</td>";
                    echo "<td>" . $row['TRANSECTION_TYPE'] . "</td>";
                    echo "</tr>";
                }

                // Close the database connection
                oci_free_statement($stmt);
                oci_close($conn);
                ?>
            </table>
        </div>
    </div>
</body>
</html>