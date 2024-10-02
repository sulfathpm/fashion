<?php
session_start();
error_reporting(0);
// Connect to MySQL database
$dbcon = mysqli_connect("localhost", "root", "", "fashion");

// Check connection
if (!$dbcon) {
    die("Connection failed: " . mysqli_connect_error());
}
$sql="SELECT * FROM users WHERE USER_ID='$_SESSION[USER_ID]'";
$data=mysqli_query($dbcon,$sql);
if($data){
    $user=mysqli_fetch_array($data);
}
$_SESSION["DRESS_ID"]=null;
$_SESSION["FABRIC_ID"]=null;
$_SESSION["ORDER_ID"]=null;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
            color: #333;
        }

        .navbar {
            background-color: #333;
            padding: 15px 0;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .navbar a {
            color: #fff;
            padding: 14px 20px;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }

        .navbar a:hover, .navbar a.customize-button {
            background-color: palevioletred;
            border-radius: 20px;
        }

        .profile-container {
            max-width: 1000px;
            margin: 50px auto;
            background-color: #fff;
            padding: 30px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .profile-header img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid palevioletred;
        }

        .profile-info {
            margin-left: 20px;
        }

        .profile-info h2 {
            font-weight: 600;
            margin-bottom: 5px;
            color: palevioletred;
        }

        .profile-info p {
            font-size: 16px;
            color: #666;
        }

        .profile-section {
            margin-bottom: 30px;
        }

        .profile-section h3 {
            margin-bottom: 10px;
            font-size: 20px;
            color: palevioletred;
            border-bottom: 2px solid #ececec;
            padding-bottom: 10px;
        }

        .order-card {
            background-color: #f5f5f5;
            padding: 15px;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            transition: all 0.3s ease;
        }

        .order-card:hover {
            background-color: #f0d9e0;
        }

        .order-card h4 {
            color: #333;
        }

        .order-card p {
            font-size: 14px;
            color: #555;
        }

        .order-card .status {
            color: #28a745;
            font-weight: 600;
        }

        .settings-link {
            text-decoration: none;
            color: palevioletred;
            font-weight: bold;
            display: inline-block;
            margin-top: 10px;
        }

        .settings-link:hover {
            text-decoration: underline;
        }

        .btn-edit {
            padding: 10px 20px;
            background-color: palevioletred;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .btn-edit:hover {
            background-color: #d75a8a;
        }

        .footer {
            text-align: center;
            padding: 10px;
            background-color: #343a40;
            color: white;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
        .customize-button{
            background-color: #d1477a !important;
        }
        .customize-button:hover{
            color: black !important;
            background-color: rgb(247, 144, 178)!important;
        }
    </style>
</head>
<body>

    <div class="navbar">
        <a href="custmrdshbrd.php">Home</a>
        <a href="fabric.php">Choose by Fabric</a>
        <a href="abt.php">About</a>
        <a href="contact1.php">Contact</a>
        <?php
        if($_SESSION["USER_ID"]==null){
           echo "<a href='login.php'>Login</a>";
        }else{
            echo "<a href='logout.php'>Logout</a>";
            echo "<a href='profile.php'>Profile</a>";
        }
        ?>
        <a href="customize1.php" class="customize-button">Customize Now</a>
        <!-- <a href="customize.php" class="customize-button" style="background-color: palevioletred; border-radius: 20px;">Customize Now</a> -->
    </div>

    <div class="profile-container">
        <!-- Profile Header -->
        <div class="profile-header">
            <img src="https://via.placeholder.com/120" alt="Profile Picture">
            <div class="profile-info">
                <h2><?php echo $user['USERNAME']; ?></h2>
                <p>Email: <?php echo $user['EMAIL'] ?></p>
                <p>Member since: <?php echo substr($user['CREATED_AT'],0,11) ?></p>
                <button class="btn-edit">Edit Profile</button>
            </div>
        </div>

        <!-- Order History Section -->
        <div class="profile-section">
            <h3>Order History</h3>
            <?php
                $sql="SELECT * FROM orders WHERE USER_ID='$_SESSION[USER_ID]'";
                $data2=mysqli_query($dbcon,$sql);
                if($data2){
                    $count=mysqli_num_rows($data2);
                    if($count==0){
                        echo"<div class='order-card'>
                            <div>
                                <h4>NO ORDER FOUND</h4>
                                <p>Start your first purchase from here</p>
                            </div>
                            
                        </div>";
                    }else{
                        
                        for($i=0;$i<$count;$i++){
                            $order=mysqli_fetch_array($data2);

                            if($order['DRESS_ID']==null){
                                //fabric purchase
                                $order_type='Fabric purchase';
                                $sql="SELECT * FROM fabrics WHERE FABRIC_ID='$order[FABRIC_ID]'";
                                $data3=mysqli_query($dbcon,$sql);
                                if($data3){
                                    $fabric=mysqli_fetch_array($data3);
                                }
                                echo"<div class='order-card'>
                                        <div>
                                            <h4>".$fabric['NAME']." - ".$order_type."</h4>
                                            <p>Order Date: ".substr($order['CREATED_AT'],0,11)."</p>
                                        </div>
                                        <div>
                                            <p class='status'>".$order['STATUSES']."</p>
                                            <a href='ordered_dress.php?id=".$order['ORDER_ID']."&type=".$order_type."' class='settings-link'>View Details</a>
                                        </div>
                                    </div>";
                                    continue;


                            }
                            
                            
                            if($order['FABRIC_ID']!=null && $order['OPTION_ID']!=null){
                                //full customisation
                                $order_type='Fully customised';


                            }else if($order['OPTION_ID']!=null){
                                //dress+customisation
                                $order_type='Dress customised';


                            }else{
                                //dress purchase
                                $order_type='Dress purchase';


                            }


                            
                            $sql="SELECT * FROM dress WHERE DRESS_ID='$order[DRESS_ID]'";
                            $data3=mysqli_query($dbcon,$sql);
                            if($data3){
                                $dress=mysqli_fetch_array($data3);
                            }
                            echo"<div class='order-card'>
                                    <div>
                                        <h4>".$dress['NAME']." - ".$order_type."</h4>
                                        <p>Order Date: ".substr($order['CREATED_AT'],0,11)."</p>
                                    </div>
                                    <div>
                                        <p class='status'>".$order['STATUSES']."</p>
                                        <a href='ordered_dress.php?id=".$order['ORDER_ID']."&type=".$order_type."' class='settings-link'>View Details</a>
                                    </div>
                                </div>";
                            }
                        
                    }
                    
                }
                    
            ?>
        </div>

        <!-- Saved Customizations Section -->
        <div class="profile-section">
            <h3>Saved Customizations</h3>
            <p>You currently have no saved customizations.</p>
        </div>

        <!-- Account Settings Section -->
        <div class="profile-section">
            <h3>Account Settings</h3>
            <a href="#" class="settings-link">Change Password</a>
            <br>
            <a href="#" class="settings-link">Update Delivery Address</a>
        </div>
    </div>

    <div class="footer">
        <p>&copy; 2024 Women's Boutique. All Rights Reserved.</p>
    </div>

</body>
</html>
