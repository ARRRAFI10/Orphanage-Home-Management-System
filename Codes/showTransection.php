<!DOCTYPE html>
<html>
<head>
    <title>Transaction Information</title>
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

        .available-fund {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px;
            background-color: #f2f2f2;
            border: 1px solid #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Transaction Information</h1>
        <div class="available-fund">
            <h2>Available Fund:</h2>
            <?php
            // Connect to the database
            $connection = oci_connect('DBMS_TEST', '1234', 'localhost/XE');

            if (!$connection) {
                $e = oci_error();
                trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
            }

            // Retrieve available fund from the Orphanage table
            $selectOrphanageQuery = "SELECT Available_fund FROM Orphanage";
            $selectOrphanageStmt = oci_parse($connection, $selectOrphanageQuery);
            oci_execute($selectOrphanageStmt);
            $row = oci_fetch_assoc($selectOrphanageStmt);
            $availableFund = $row['AVAILABLE_FUND'];

            echo '<span id="available-fund-value">' . $availableFund . '</span>';

            // Close the database connection
            oci_free_statement($selectOrphanageStmt);
            oci_close($connection);
            ?>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Type</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Connect to the database
                $connection = oci_connect('DBMS_TEST', '1234', 'localhost/XE');

                if (!$connection) {
                    $e = oci_error();
                    trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
                }

                // Retrieve transaction information from the Transaction table
                $selectTransactionQuery = "SELECT Transection_ID, Transection_Amount, Transection_Date, Transection_Type FROM Transection";
                $selectTransactionStmt = oci_parse($connection, $selectTransactionQuery);
                oci_execute($selectTransactionStmt);

                // Display transaction information in the table
                while ($row = oci_fetch_assoc($selectTransactionStmt)) {
                    echo '<tr>';
                    echo '<td>' . $row['TRANSECTION_ID'] . '</td>';
                    echo '<td>' . $row['TRANSECTION_AMOUNT'] . '</td>';
                    echo '<td>' . $row['TRANSECTION_DATE'] . '</td>';
                    echo '<td>' . $row['TRANSECTION_TYPE'] . '</td>';
                    echo '</tr>';
                }

                // Close the database connection
                oci_free_statement($selectTransactionStmt);
                oci_close($connection);
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
