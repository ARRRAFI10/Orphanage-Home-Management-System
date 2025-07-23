<!DOCTYPE html>
<html>
<head>
    <title>Registration Successful</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .popup {
            max-width: 400px;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h2 {
            color: #333333;
        }

        .success-message {
            margin-bottom: 20px;
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }

        .home-button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
        }

        .home-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<?php
// Database configuration
$host = "localhost/XE";
$username = "DBMS_TEST"; // Replace with your database username
$password = "1234"; // Replace with your database password

try {
    // Establish a connection to Oracle
    $conn = oci_connect($username, $password, $host);

    // Check the connection
    if (!$conn) {
        $e = oci_error();
        throw new Exception("Connection failed: " . $e['message']);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Retrieve orphan form data
        $orphanName = $_POST['orphan_firstname'];
        $orphanGender = $_POST['orphan_gender'];
        $orphanDOB = $_POST['orphan_event-time'];
        $orphanBloodGroup = $_POST['orphan_group'];
        $orphanDescription = $_POST['orphan_description'];

        // Retrieve guardian form data
        $guardianName = $_POST['guardian_firstname'];
        $guardianHouseNo = $_POST['guardian_houseno'];
        $guardianRoadNo = $_POST['guardian_roadno'];
        $guardianArea = $_POST['guardian_area'];
        $guardianDistrict = $_POST['guardian_district'];
        $guardianRelation = $_POST['guardian_relation'];

        // Retrieve additional data
        $adoptiveParent = $_POST['adoptive_parent'];
        $adoptionDate = isset($_POST['adoption_date']) ? $_POST['adoption_date'] : null;

        // Prepare the SQL queries
        $orphanIDQuery = "BEGIN :orphanID := GenerateOrphanID(); END;";
        $guardianIDQuery = "BEGIN :guardianID := GenerateGuardianID(); END;";
        $orphanQuery = "INSERT INTO Orphans (Orphan_ID, Orphan_Name, Date_of_Birth, Blood_Group, Gender, Age, Description, Orphanage_ID) VALUES (:orphanID, :orphanName, TO_DATE(:orphanDOB, 'YYYY-MM-DD\"T\"HH24:MI'), :orphanBloodGroup, :orphanGender, :orphanAge, :orphanDescription, 'A1')";
        $guardianQuery = "INSERT INTO Guardians (Guardians_ID, Guardians_Name, House_NO, Road_No, Area, District, Relation) VALUES (:guardianID, :guardianName, :guardianHouseNo, :guardianRoadNo, :guardianArea, :guardianDistrict, :guardianRelation)";
        $hasGuardiansQuery = "INSERT INTO Has_Guardians (Adoptive_Parent, Adaption_Date, Orphan_ID, Guardians_ID) VALUES (:adoptiveParent, TO_DATE(:adoptionDate, 'YYYY-MM-DD'), :orphanID, :guardianID)";

        // Create orphan ID statement
        $orphanIDStmt = oci_parse($conn, $orphanIDQuery);
        oci_bind_by_name($orphanIDStmt, ":orphanID", $orphanID, 100);

        // Execute orphan ID statement
        oci_execute($orphanIDStmt);

        // Create guardian ID statement
        $guardianIDStmt = oci_parse($conn, $guardianIDQuery);
        oci_bind_by_name($guardianIDStmt, ":guardianID", $guardianID, 100);

        // Execute guardian ID statement
        oci_execute($guardianIDStmt);

        // Create orphan statement
        $orphanStmt = oci_parse($conn, $orphanQuery);
        oci_bind_by_name($orphanStmt, ":orphanID", $orphanID);
        oci_bind_by_name($orphanStmt, ":orphanName", $orphanName);
        oci_bind_by_name($orphanStmt, ":orphanBloodGroup", $orphanBloodGroup);
        oci_bind_by_name($orphanStmt, ":orphanGender", $orphanGender);
        oci_bind_by_name($orphanStmt, ":orphanDOB", $orphanDOB);
        oci_bind_by_name($orphanStmt, ":orphanDescription", $orphanDescription);

        // Create guardian statement
        $guardianStmt = oci_parse($conn, $guardianQuery);
        oci_bind_by_name($guardianStmt, ":guardianID", $guardianID);
        oci_bind_by_name($guardianStmt, ":guardianName", $guardianName);
        oci_bind_by_name($guardianStmt, ":guardianHouseNo", $guardianHouseNo);
        oci_bind_by_name($guardianStmt, ":guardianRoadNo", $guardianRoadNo);
        oci_bind_by_name($guardianStmt, ":guardianArea", $guardianArea);
        oci_bind_by_name($guardianStmt, ":guardianDistrict", $guardianDistrict);
        oci_bind_by_name($guardianStmt, ":guardianRelation", $guardianRelation);

        // Calculate orphan age
        $orphanDOBDateTime = new DateTime($orphanDOB);
        $currentDateTime = new DateTime();
        $orphanAgeInterval = $currentDateTime->diff($orphanDOBDateTime);
        $orphanAge = $orphanAgeInterval->y;
        oci_bind_by_name($orphanStmt, ":orphanAge", $orphanAge);

        // Create hasGuardians statement
        $hasGuardiansStmt = oci_parse($conn, $hasGuardiansQuery);
        oci_bind_by_name($hasGuardiansStmt, ":adoptiveParent", $adoptiveParent);
        
        // Handle the case when "Adoptive Parent" is selected as "Yes" but no adoption date is provided
        if ($adoptiveParent === 'Yes' && !$adoptionDate) {
            throw new Exception("Please provide the adoption date.");
        }
        
        oci_bind_by_name($hasGuardiansStmt, ":adoptionDate", $adoptionDate);
        oci_bind_by_name($hasGuardiansStmt, ":orphanID", $orphanID);
        oci_bind_by_name($hasGuardiansStmt, ":guardianID", $guardianID);

        // Execute orphan statement
        oci_execute($orphanStmt);

        // Execute guardian statement
        oci_execute($guardianStmt);

        // Execute hasGuardians statement
        oci_execute($hasGuardiansStmt);

        // Close the statements
        oci_free_statement($orphanIDStmt);
        oci_free_statement($guardianIDStmt);
        oci_free_statement($orphanStmt);
        oci_free_statement($guardianStmt);
        oci_free_statement($hasGuardiansStmt);
    }

    // Close the Oracle connection
    oci_close($conn);
} catch (Exception $e) {
    // Handle exceptions
    $error = $e->getMessage();
}
?>

<div class="container">
    <div class="popup">
        <?php if (isset($error)) { ?>
            <h2>Error</h2>
            <p><?php echo $error; ?></p>
        <?php } else { ?>
            <h2>Registration Successful</h2>
            <p class="success-message">Thank you for registering!</p>
        <?php } ?>
        <a href="adminPage.html" class="home-button">Admin Page</a>
    </div>
</div>

</body>
</html>