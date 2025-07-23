<!DOCTYPE html>
<html>
<head>
    <title>Teacher Information</title>
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
        <h1>Teacher Information</h1>
        <div class="btn-container">
            <a class="button" href="routine.html">Teacher Routine</a>
        </div>
        <table>
            <tr>
                <th>Teacher ID</th>
                <th>Teacher Name</th>
                <th>Teacher Salary</th>
                <th>Joining Date</th>
                <th>Education Qualification</th>
                <th>Subject</th>
            </tr>

            <?php
            // Connect to the database
            $connection = oci_connect('DBMS_TEST', '1234', 'localhost/XE');

            if (!$connection) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }

            // Retrieve teacher information from the Teacher table
            $selectTeacherQuery = "SELECT t.Teacher_ID, t.Teacher_Name, t.Teacher_Salary, t.Joining_Date, t.Education_Qualification, t.Subject
                                   FROM Teacher t";
            $selectTeacherStmt = oci_parse($connection, $selectTeacherQuery);
            oci_execute($selectTeacherStmt);

            // Display teacher information in the table
            while ($row = oci_fetch_assoc($selectTeacherStmt)) {
                echo "<tr>";
                echo "<td>" . $row['TEACHER_ID'] . "</td>";
                echo "<td>" . $row['TEACHER_NAME'] . "</td>";
                echo "<td>" . $row['TEACHER_SALARY'] . "</td>";
                echo "<td>" . $row['JOINING_DATE'] . "</td>";
                echo "<td>" . $row['EDUCATION_QUALIFICATION'] . "</td>";
                echo "<td>" . $row['SUBJECT'] . "</td>";
                echo "</tr>";
            }

            // Close the database connection
            oci_free_statement($selectTeacherStmt);
            oci_close($connection);
            ?>

        </table>
    </div>
</body>
</html>
