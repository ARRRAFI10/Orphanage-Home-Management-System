<!DOCTYPE html>
<html>
<head>
    <title>Doctor Information</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        
        h1 {
            text-align: center;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
        }
        
        .btn-container {
            text-align: right;
            margin-top: 20px;
        }
        
        .btn-container a.button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #4CAF50;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }
        
        .btn-container a.button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Doctor Information</h1>
        <table>
            <tr>
                <th>Doctor ID</th>
                <th>Doctor Name</th>
                <th>Doctor Salary</th>
                <th>Joining Date</th>
                <th>Specialist</th>
                <th>Education Qualification</th>
            </tr>

            <?php
            // Connect to the database
            $connection = oci_connect('DBMS_TEST', '1234', 'localhost/XE');

            if (!$connection) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }

            // Retrieve doctor information from the Doctor table
            $selectDoctorQuery = "SELECT d.Doctor_ID, d.Doctor_Name, d.Doctor_Salary, d.Joining_Date, d.Specialist, d.Education_Qualification
                                  FROM Doctor d";
            $selectDoctorStmt = oci_parse($connection, $selectDoctorQuery);
            oci_execute($selectDoctorStmt);

            // Display doctor information in the table
            while ($row = oci_fetch_assoc($selectDoctorStmt)) {
                echo "<tr>";
                echo "<td>" . $row['DOCTOR_ID'] . "</td>";
                echo "<td>" . $row['DOCTOR_NAME'] . "</td>";
                echo "<td>" . $row['DOCTOR_SALARY'] . "</td>";
                echo "<td>" . $row['JOINING_DATE'] . "</td>";
                echo "<td>" . $row['SPECIALIST'] . "</td>";
                echo "<td>" . $row['EDUCATION_QUALIFICATION'] . "</td>";
                echo "</tr>";
            }

            // Close the database connection
            oci_free_statement($selectDoctorStmt);
            oci_close($connection);
            ?>

        </table>
    </div>
</body>
</html>
