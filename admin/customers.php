<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'fashion');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch customers from the database
$sql = "SELECT USER_ID, USERNAME, EMAIL FROM users where USER_TYPE = 'CUSTOMER'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Management</title>
    <link rel="stylesheet" href="admin.css">
</head>

<body>
    <div class="header">
        <h1>Customer Management</h1>
    </div>

    <div class="admin-dashboard">
        <aside class="sidebar">
            <h3>Menu</h3>
            <a href="customers.php">Customer Management</a>
            <a href="staff.php">Staff Management</a>
            <a href="communications.php">Communication</a>
            <a href="manageDesign.php">Manage Designs</a>
            <a href="OrderManage.php">Order Management</a>
        </aside>

        <main class="content">
            <section id="customers-section" class="section">
                <h3>Customers</h3>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Loop through the results and display each customer
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["USER_ID"] . "</td>";
                                echo "<td>" . $row["USERNAME"] . "</td>";
                                echo "<td>" . $row["EMAIL"] . "</td>";
                                echo "<td>
                                    <a href='view_customer.php?id=" . $row["USER_ID"] . "'><button>View</button></a>
                                    <a href='edit_customer.php?id=" . $row["USER_ID"] . "'><button>Edit</button></a>
                                    <a href='delete_customer.php?id=" . $row["USER_ID"] . "'><button>Delete</button></a>
                                  </td>";
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>No customers found</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </section>
        </main>
    </div>

    <?php
    // Close the database connection
    $conn->close();
    ?>
</body>

</html>
