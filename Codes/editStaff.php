<!DOCTYPE html>
<html>
<head>
  <title>Edit Staff Information</title>
  <link rel="stylesheet" type="text/css" href="RegisterGuardian.css">
</head>
<body>
  <div class="container">
    <h2>Edit Staff Information</h2>

    <?php
    // Check if an ID parameter is provided in the URL for staff
    if (isset($_GET['id'])) {
        $staffId = $_GET['id'];

        // Connect to the database
        $connection = oci_connect('DBMS_TEST', '1234', 'localhost/XE');

        if (!$connection) {
            $e = oci_error();
            trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
        }

        // Retrieve the staff information
        $sqlQuery = "SELECT S.Staff_ID, S.Staff_Name, S.Staff_Salary, S.Joining_Date, S.Designation, S.Address.House_No AS HouseNo, S.Address.Road_No AS RoadNo, 
                     S.Address.Area AS Area, S.Address.District AS District, S.contact
                     FROM Staff S
                     WHERE S.Staff_ID = :staff_id";

        $query = oci_parse($connection, $sqlQuery);
        oci_bind_by_name($query, ':staff_id', $staffId);
        oci_execute($query);

        $row = oci_fetch_assoc($query);

        // Display the edit form
        if ($row) {
            echo '<form method="POST" action="updateStaff.php">';
            echo '<input type="hidden" name="staff_id" value="' . $row['STAFF_ID'] . '">';

            // Display the staff information
            echo '<h3>Staff Information</h3>';
            echo '<label>Staff Name:</label>';
            echo '<input type="text" name="staff_name" value="' . $row['STAFF_NAME'] . '">';
            echo '<label>Staff Salary:</label>';
            echo '<input type="text" name="staff_salary" value="' . $row['STAFF_SALARY'] . '">';
            echo '<label>Joining Date:</label>';
            echo '<input type="text" name="joining_date" value="' . $row['JOINING_DATE'] . '">';
            echo '<label>Designation:</label>';
            echo '<input type="text" name="designation" value="' . $row['DESIGNATION'] . '">';
            echo '<label>House No:</label>';
            echo '<input type="text" name="house_no" value="' . $row['HOUSENO'] . '">';
            echo '<label>Road No:</label>';
            echo '<input type="text" name="road_no" value="' . $row['ROADNO'] . '">';
            echo '<label>Area:</label>';
            echo '<input type="text" name="area" value="' . $row['AREA'] . '">';
            echo '<label>District:</label>';
            echo '<input type="text" name="district" value="' . $row['DISTRICT'] . '">';
            echo '<label>Contact:</label>';
            echo '<input type="text" name="contact" value="' . $row['CONTACT'] . '">';

            echo '<input type="submit" value="Update">';
            echo '</form>';
        } else {
            echo '<p>Staff not found.</p>';
        }

        // Close the database connection
        oci_free_statement($query);
        oci_close($connection);
    } else {
        echo '<p>No staff ID specified.</p>';
    }
    ?>
  </div>
</body>
</html>
