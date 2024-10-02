<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'fashion');

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get customer ID from URL
$customer_id = $_GET['id'];

// Fetch customer details from the database
$sql = "SELECT * FROM users WHERE USER_ID = $customer_id";
$result = $conn->query($sql);

// Check if the customer exists
if ($result->num_rows > 0) {
    $customer = $result->fetch_assoc();
} else {
    echo "Customer not found.";
    exit;
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Customer</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <h1>Customer Details</h1>
    <table border="1" cellpadding="10" cellspacing="0">
        <tr>
            <th>ID</th>
            <td><?php echo $customer['USER_ID']; ?></td>
        </tr>
        <tr>
            <th>Name</th>
            <td><?php echo $customer['USERNAME']; ?></td>
        </tr>
        <tr>
            <th>Email</th>
            <td><?php echo $customer['EMAIL']; ?></td>
        </tr>
        <!-- Add more fields as necessary -->
    </table>

    <br>
    <a href="customers.php"><button>Back</button></a>
</body>
</html>
