<?php
// Connect to the database
$conn = mysqli_connect("localhost", "root", "", "fashion");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables for storing messages
$file_upload_message = "";
$fabric_add_message = "";

// Handle form submission to add new fabric
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $fabric_name = $_POST['fabric-name'];
    $description = $_POST['description'];
    $price_per_unit = $_POST['price-per-unit'];
    $available_quantity = $_POST['available-quantity'];

    // Define the path to the 'fabrics' folder
    $image_dir = "D:/xampp/htdocs/ab/fabric/";

    // Ensure the 'fabrics' folder is writable
    if (!is_dir($image_dir)) {
        mkdir($image_dir, 0777, true); // Create the folder if it doesn't exist
    }

    // Set the destination path for the uploaded file
    $image_url = $image_dir . basename($_FILES["image-upload"]["name"]);

    // Move the uploaded file to the 'fabrics' folder
    if (move_uploaded_file($_FILES["image-upload"]["tmp_name"], $image_url)) {
        $file_upload_message = "File uploaded successfully.";
    } else {
        $file_upload_message = "Error uploading file.";
    }

    // Use forward slashes to make the path consistent across different environments
    $image_url_for_db = "../fabric/" . basename($_FILES["image-upload"]["name"]);

    // Insert new fabric into the database with the relative path
    $sql = "INSERT INTO fabrics (NAME, DESCRIPTION, PRICE_PER_UNIT, AVAILABLE_QUANTITY, IMAGE_URL, CREATED_AT) 
            VALUES ('$fabric_name', '$description', $price_per_unit, $available_quantity, '$image_url_for_db', NOW())";

    if ($conn->query($sql) === TRUE) {
        $fabric_add_message = "New fabric added successfully.";
    } else {
        $fabric_add_message = "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Fetch fabrics from the database
$sql = "SELECT * FROM fabrics";
$result = $conn->query($sql);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <script>
        function viewFabric(fabricId) {
            // Redirect to a view page with the fabric ID in the query string
            window.location.href = "viewFabric.php?id=" + fabricId;
        }

        function confirmDeletion(fabricId) {
            var confirmation = confirm("Are you sure you want to delete this fabric?");
            if (confirmation) {
                // If confirmed, redirect to deleteFabric.php with the fabric ID
                window.location.href = "deleteFabric.php?id=" + fabricId + "&confirm=yes";
            }
        }
    </script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Fabrics</title>
    <link rel="stylesheet" href="admin1.css">
</head>
<body>
    <div class="header">
        <h1>Manage Fabrics</h1>
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
            <!-- Display messages here after form submission -->
            <?php if ($file_upload_message): ?>
                <p><?php echo $file_upload_message; ?></p>
            <?php endif; ?>
            
            <?php if ($fabric_add_message): ?>
                <p><?php echo $fabric_add_message; ?></p>
            <?php endif; ?>

            <section id="add-fabric-section" class="section">
                <h3>Add New Fabric</h3>
                <form action="" method="post" enctype="multipart/form-data">
                    <label for="fabric-name">Fabric Name:</label>
                    <input type="text" id="fabric-name" name="fabric-name" required>

                    <label for="description">Description:</label>
                    <textarea id="description" name="description" required></textarea>

                    <label for="price-per-unit">Price per Unit:</label>
                    <input type="number" id="price-per-unit" name="price-per-unit" required>

                    <label for="available-quantity">Available Quantity:</label>
                    <input type="number" id="available-quantity" name="available-quantity" required>

                    <label for="image-upload">Upload Image:</label>
                    <input type="file" id="image-upload" name="image-upload" accept="image/*" required>

                    <button type="submit">Add Fabric</button>
                </form>
            </section>

            <table>
                <thead>
                    <tr>
                        <th>Fabric ID</th>
                        <th>Fabric Name</th>
                        <th>Description</th>
                        <th>Price per Unit</th>
                        <th>Available Quantity</th>
                        <th>Image</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                        // Output data of each row
                        while($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row['FABRIC_ID'] . "</td>";
                            echo "<td>" . $row['NAME'] . "</td>";
                            echo "<td>" . $row['DESCRIPTION'] . "</td>";
                            echo "<td>₹" . $row['PRICE_PER_UNIT'] . "</td>";
                            echo "<td>" . $row['AVAILABLE_QUANTITY'] . "</td>";
                            // Use forward slashes for image URLs
                            echo "<td><img src='" . $row['IMAGE_URL'] . "' alt='" . $row['NAME'] . "' width='50'></td>";
                            echo "<td>
                                    <button onclick=\"viewFabric(" . $row['FABRIC_ID'] . ")\">View</button> 
                                    <a href='editFabric.php?id=" . $row['FABRIC_ID'] . "'><button>Edit</button></a> 
                                    <button onclick=\"confirmDeletion(" . $row['FABRIC_ID'] . ")\">Delete</button>
                                  </td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='7'>No fabrics found</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </main>
    </div>
</body>  
</html>
