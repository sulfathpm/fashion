<?php
// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "fashion");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize message variable
$message = "";

// Check if deletion is confirmed via GET parameter
if (isset($_GET['confirm']) && $_GET['confirm'] == 'yes' && isset($_GET['id'])) {
    // Get fabric ID from the URL
    $fabric_id = $_GET['id'];

    // Delete fabric from the database
    $sql = "DELETE FROM fabrics WHERE FABRIC_ID = $fabric_id";

    if ($conn->query($sql) === TRUE) {
        $message = "Fabric deleted successfully.";
    } else {
        $message = "Error deleting fabric: " . $conn->error;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Fabric</title>
    <link rel="stylesheet" href="admin.css">
    <script>
        function confirmDeletion(fabricId) {
            var confirmation = confirm("Are you sure you want to delete this fabric?");
            if (confirmation) {
                // If confirmed, redirect with confirmation parameter
                window.location.href = "deleteFabric.php?id=" + fabricId + "&confirm=yes";
            } else {
                // If not confirmed, return to the manage fabrics page
                window.location.href = "manageFabric.php";
            }
        }
    </script>
</head>
<body>
    <?php if ($message): ?>
        <h1><?php echo $message; ?></h1>
    <?php endif; ?>
    <a href="manageFabric.php"><button>Back</button></a>
</body>
</html>
