<!DOCTYPE html>
<html>
<head>
    <title>Doctor Dashboard</title>
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
    <?php
    session_start();

    // Check if the doctor is logged in, otherwise redirect to the login page
    if (!isset($_SESSION['loggedIn'])) {
        header("Location: login.php");
        exit();
    }

    // Retrieve the doctor ID from the session
    $doctorId = $_SESSION['loggedIn'];

    // Database connection details
    $host = "localhost/XE"; // Replace with your Oracle service name or SID
    $username = "DBMS_TEST";
    $dbPassword = "1234";

    // Establish database connection
    $conn = oci_connect($username, $dbPassword, $host);
    if (!$conn) {
        $error = oci_error();
        die("Database connection failed: " . $error['message']);
    }

    // Prepare and execute the query to fetch the doctor's information
    $query = "SELECT Doctor_Name, Specialist, Education_Qualification FROM Doctor WHERE Doctor_ID = :doctorId";
    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ":doctorId", $doctorId);
    oci_execute($stmt);

    // Fetch the doctor information
    if ($row = oci_fetch_assoc($stmt)) {
        $doctorName = $row['DOCTOR_NAME'];
        $doctorSpecialist = $row['SPECIALIST'];
        $doctorEducationQualification = $row['EDUCATION_QUALIFICATION'];
    } else {
        $doctorName = "Unknown";
        $doctorSpecialist = "Unknown";
        $doctorEducationQualification = "Unknown";
    }


    // Close the statement
    oci_free_statement($stmt);

    // Retrieve and display the medical history of orphans associated with the doctor
    $query = "SELECT mh.History_No, mh.Disease, mh.Treatment, mh.Last_Checkup_Date, o.Orphan_Name
              FROM Medical m
              INNER JOIN Medical_History mh ON m.History_No = mh.History_No
              INNER JOIN Orphans o ON m.Orphan_ID = o.Orphan_ID
              WHERE m.Doctor_ID = :doctorId";

    $stmt = oci_parse($conn, $query);
    oci_bind_by_name($stmt, ":doctorId", $doctorId);
    oci_execute($stmt);

    echo "<h2 style='color: #4CAF50;'>Welcome, $doctorName</h2>";
    echo "<p>ID: $doctorId</p>";
    echo "<p>Specialist: $doctorSpecialist</p>";
    echo "<p>Education Qualification: $doctorEducationQualification</p>";


    echo "<h3 style='color: #4CAF50;'>Medical History of Orphans</h3>";
    echo "<table>";
    echo "<tr>
              <th style='background-color: #4CAF50; color: #333;'>History No</th>
              <th style='background-color: #4CAF50; color: #333;'>Orphan Name</th>
              <th style='background-color: #4CAF50; color: #333;'>Disease</th>
              <th style='background-color: #4CAF50; color: #333;'>Treatment</th>
              <th style='background-color: #4CAF50; color: #333;'>Last Checkup Date</th>
          </tr>";

    // Fetch and display the medical history
    while ($row = oci_fetch_assoc($stmt)) {
        echo "<tr>";
        echo "<td>" . $row['HISTORY_NO'] . "</td>";
        echo "<td>" . $row['ORPHAN_NAME'] . "</td>";
        echo "<td>" . $row['DISEASE'] . "</td>";
        echo "<td>" . $row['TREATMENT'] . "</td>";
        echo "<td>" . $row['LAST_CHECKUP_DATE'] . "</td>";
        echo "</tr>";
    }

    echo "</table>";

    // Add functionality to add new entries to the Medical_History table
    echo "<div class='form-container'>";
    echo "<h3 style='color: #4CAF50;'>Add Orphan Medical History</h3>";
    echo "<form action='add_medical_history.php' method='post'>";
    echo "<label for='orphanId'>Orphan ID:</label>";
    echo "<input type='text' name='orphanId' required><br>";
    echo "<label for='disease'>Disease:</label>";
    echo "<input type='text' name='disease' required><br>";
    echo "<label for='treatment'>Treatment:</label>";
    echo "<input type='text' name='treatment' required><br>";
    echo "<label for='lastCheckupDate'>Last Checkup Date:</label>";
    echo "<input type='date' name='lastCheckupDate' required><br>";
    echo "<input type='submit' value='Add'>";
    echo "</form>";

    // Close the statement and the database connection
    oci_free_statement($stmt);
    oci_close($conn);
    ?>
</body>
</html>
