<!DOCTYPE html>
<html>
<head>
  <title>Edit Orphan and Guardian Information</title>
  <link rel="stylesheet" type="text/css" href="RegisterGuardian.css">
</head>
<body>
  <div class="container">
    <h2>Edit Orphan and Guardian Information</h2>

    <?php
    // Check if an ID parameter is provided in the URL
    if (isset($_GET['id'])) {
        $orphanId = $_GET['id'];

        // Connect to the database
        $connection = oci_connect('DBMS_TEST', '1234', 'localhost/XE');

        if (!$connection) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        // Retrieve the orphan and guardian information
        $sqlQuery = "SELECT O.Orphan_ID, O.Orphan_Name, O.Age, O.Date_of_Birth, O.Blood_Group, O.Gender, G.Guardians_Name, G.House_NO, G.Road_No, G.Area, G.District, G.Relation, HG.Adoptive_Parent, HG.Adaption_Date
                        FROM Orphans O
                        JOIN has_guardians HG ON O.Orphan_ID = HG.Orphan_ID
                        JOIN Guardians G ON HG.Guardians_ID = G.Guardians_ID
                        WHERE O.Orphan_ID = :orphan_id";

        $query = oci_parse($connection, $sqlQuery);
        oci_bind_by_name($query, ':orphan_id', $orphanId);
        oci_execute($query);

        $row = oci_fetch_assoc($query);

        // Display the edit form
        if ($row) {
            echo '<form method="POST" action="updateOrphan.php">';
            echo '<input type="hidden" name="orphan_id" value="' . $row['ORPHAN_ID'] . '">';

            // Display the orphan information
            echo '<h3>Orphan Information</h3>';
            echo '<label>Orphan Name:</label>';
            echo '<input type="text" name="orphan_name" value="' . $row['ORPHAN_NAME'] . '">';
            echo '<label>Age:</label>';
            echo '<input type="text" name="age" value="' . $row['AGE'] . '">';
            echo '<label>Date of Birth:</label>';
            echo '<input type="text" name="date_of_birth" value="' . $row['DATE_OF_BIRTH'] . '">';
            echo '<label>Blood Group:</label>';
            echo '<input type="text" name="blood_group" value="' . $row['BLOOD_GROUP'] . '">';
            echo '<label>Gender:</label>';
            echo '<input type="text" name="gender" value="' . $row['GENDER'] . '">';

            // Display the guardian information
            echo '<h3>Guardian Information</h3>';
            echo '<label>Guardian Name:</label>';
            echo '<input type="text" name="guardian_name" value="' . $row['GUARDIANS_NAME'] . '">';
            echo '<label>Relation:</label>';
            echo '<input type="text" name="relation" value="' . $row['RELATION'] . '">';
            echo '<label>House No:</label>';
            echo '<input type="text" name="house_no" value="' . $row['HOUSE_NO'] . '">';
            echo '<label>Road No:</label>';
            echo '<input type="text" name="road_no" value="' . $row['ROAD_NO'] . '">';
            echo '<label>Area:</label>';
            echo '<input type="text" name="area" value="' . $row['AREA'] . '">';
            echo '<label>District:</label>';
            echo '<input type="text" name="district" value="' . $row['DISTRICT'] . '">';

            // Display the additional guardian information
            echo '<h3>Additional Guardian Information</h3>';
            echo '<label>Adoptive Parent:</label>';
            echo '<input type="text" name="adoptive_parent" value="' . $row['ADOPTIVE_PARENT'] . '">';
            echo '<label>Adoption Date:</label>';
            echo '<input type="text" name="adaption_date" value="' . $row['ADAPTION_DATE'] . '">';

            echo '<input type="submit" value="Update">';
            echo '</form>';
        } else {
            echo '<p>Orphan not found.</p>';
        }

        // Close the database connection
        oci_free_statement($query);
        oci_close($connection);
    } else {
        echo '<p>No orphan ID specified.</p>';
    }
    ?>
  </div>
</body>
</html>
