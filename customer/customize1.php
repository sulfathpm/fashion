<?php
session_start();
error_reporting(0);
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "fashion";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if($_SERVER["REQUEST_METHOD"] == "GET"){
   
    //check if the user logged in or not
    if($_SESSION["USER_ID"]==null){
         $_SESSION["KEY"]='to-customise-dress';
        // header("Location: login.php"); 
        echo "<script>alert('You need to login to customise dress'); window.location.href='login.php';</script>";
    }

    

}



if (isset($_POST['customize'])) {
    $dress_id = htmlspecialchars($_POST['dress-style']);
    $fabric_id = htmlspecialchars($_POST['fabric']);
    $color = htmlspecialchars($_POST['color']);
    $embellishments = htmlspecialchars($_POST['embellishments']);
    $sizes = htmlspecialchars($_POST['sizes']);
    $dresslength = htmlspecialchars($_POST['dresslength']);
    $sleevelength = htmlspecialchars($_POST['sleevelength']);

    $shoulder = htmlspecialchars($_POST['shoulder']);
    $bust = htmlspecialchars($_POST['bust']);
    $waist = htmlspecialchars($_POST['waist']);
    $hips = htmlspecialchars($_POST['hips']);
    
    $totalcost= htmlspecialchars($_POST['total-price']);

    $additionalNotes = htmlspecialchars($_POST['additional-notes']);
    
    $current_date = date('Y-m-d');
    $estimate_date = date('Y-m-d', strtotime($current_date . ' + 9 days'));
    $actual_date = date('Y-m-d', strtotime($current_date . ' + 14 days'));
    
    //to ensure a color is selected or not(#fff means no color selected or default color)
    if($color=='#fff'){
        $color="default";
    }
    if($fabric_id=='default'){
        $fabric_id=null;
    }

  // Insert data into database
    
    //first add data to measurement table
    if($_SESSION["DRESS_ID"]==$dress_id){
        $sql="INSERT INTO measurements( USER_ID, DRESS_ID, SHOULDER, BUST, WAIST, HIP) VALUES ('$_SESSION[USER_ID]','$dress_id','$shoulder','$bust','$waist','$hips')";
    }else{
        $sql="INSERT INTO measurements( USER_ID, DRESS_ID, FABRIC_ID, SHOULDER, BUST, WAIST, HIP) VALUES ('$_SESSION[USER_ID]','$dress_id','$fabric_id','$shoulder','$bust','$waist','$hips')";
    }
    //$sql="INSERT INTO measurements( USER_ID, DRESS_ID, FABRIC_ID, SHOULDER, BUST, WAIST, HIP) VALUES ('$_SESSION[USER_ID]','$dress_id','$fabric_id','$shoulder','$bust','$waist','$hips')";
    $measurent=mysqli_query($conn,$sql);
    if($measurent){
        $sql="SELECT * FROM measurements WHERE USER_ID='$_SESSION[USER_ID]' AND DRESS_ID='$dress_id'";
        $mdata=mysqli_query($conn,$sql);
        if($mdata){
            $mkey=mysqli_fetch_array($mdata);
            $m_id=$mkey['MEASUREMENT_ID'];
            if($_SESSION["DRESS_ID"]==$dress_id){
                $sql="INSERT INTO customizations( DRESS_ID, MEASUREMENT_ID, COLOR, EMBELLISHMENTS, DRESS_LENGTH, SLEEVE_LENGTH, ADDITIONAL_NOTES) VALUES ('$dress_id','$m_id','$color','$embellishments','$dresslength','$sleevelength','$additionalNotes')";
            }else{
                $sql="INSERT INTO customizations( DRESS_ID, FABRIC_ID, MEASUREMENT_ID, COLOR, EMBELLISHMENTS, DRESS_LENGTH, SLEEVE_LENGTH, ADDITIONAL_NOTES) VALUES ('$dress_id','$fabric_id','$m_id','$color','$embellishments','$dresslength','$sleevelength','$additionalNotes')";
            }
            
            $cdata=mysqli_query($conn,$sql);
            if($cdata){
                $sql="SELECT * FROM customizations WHERE MEASUREMENT_ID='$m_id'";
                $kdata=mysqli_query($conn,$sql);
                $ckey=mysqli_fetch_array($kdata);
                $o_id=$ckey['OPTION_ID'];
                //insert to orders table
                if($_SESSION["DRESS_ID"]==$dress_id){
                    $sql="INSERT INTO orders(USER_ID, DRESS_ID, OPTION_ID, STATUSES, SSIZE, TOTAL_PRICE, ESTIMATED_DELIVERY_DATE, ACTUAL_DELIVERY_DATE) VALUES ('$_SESSION[USER_ID]','$dress_id','$o_id','PENDING','$sizes','$totalcost','$estimate_date','$actual_date')";
                }else{
                    $sql="INSERT INTO orders(USER_ID, DRESS_ID, FABRIC_ID, OPTION_ID, STATUSES, SSIZE, TOTAL_PRICE, ESTIMATED_DELIVERY_DATE, ACTUAL_DELIVERY_DATE) VALUES ('$_SESSION[USER_ID]','$dress_id','$fabric_id','$o_id','PENDING','$sizes','$totalcost','$estimate_date','$actual_date')";
                }
                $odata=mysqli_query($conn,$sql);
                if($odata){
                    echo "<script>alert('Customised dress order placed successfully!'); window.location.href='custmrdshbrd.php';</script>";
                }
            }
        }
    }

    
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customize Your Dress</title>
    
    <style>
        /* Add your CSS here */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            margin: 0;
            padding: 0;
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

        .container {
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h2 {
            text-align: center;
            color: palevioletred;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            margin-bottom: 5px;
            font-weight: 600;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group input[type="color"] {
            padding: 3px;
            height: 45px;
        }

        #total-price {
            color: palevioletred;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
        }

        button {
            background-color: palevioletred;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 20px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
            display: block;
            width: 100%;
            margin: 0 auto;
        }

        button:hover {
            background-color: #d06c9f;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background-color: #333;
            color: white;
            margin-top: 40px;
            border-radius: 0 0 10px 10px;
        }

        @media (min-width: 768px) {
            .form-group {
                flex-direction: row;
                align-items: center;
            }

            .form-group label {
                width: 30%;
                margin-bottom: 0;
            }

            .form-group input,
            .form-group select,
            .form-group textarea {
                width: 65%;
            }
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
    </div>

    <div class="container">
        <h2>Customize Your Dress</h2>

        <form id="customization-form" method="POST" action="">
            <div class="form-group">
            <?php
                    if($_SESSION["DRESS_ID"]==null){

                        $sql="SELECT * FROM dress";
                        $dress=mysqli_query($conn,$sql);
                        if($dress){
                            $count=mysqli_num_rows($dress);

                            echo "<label for='dress-style'>Select Dress Style:</label>
                            <select id='dress-style' name='dress-style' onchange='updatePrice()'>
                            <option value='none' data-price='0'>None</option>";
                            for($i=0;$i<$count;$i++){
                                $row=mysqli_fetch_array($dress);
                                echo"   <option value='".$row['DRESS_ID']."' data-price='".$row['BASE_PRICE']."'>".$row['NAME']." - ₹".$row['BASE_PRICE']."</option>";
                            }
                        }
                        echo "</select>";
                    }else{
                        $sql="SELECT * FROM dress WHERE DRESS_ID=".$_SESSION["DRESS_ID"]."";
                        $data=mysqli_query($conn,$sql);
                        if($data){
                            $dress=mysqli_fetch_array($data);
                            echo "<label for='dress-style'>Your Dress Style:</label>
                                <select id='dress-style' name='dress-style' onchange='updatePrice()'>
                                    <option value='".$dress['DRESS_ID']."' data-price='".$dress['BASE_PRICE']."'>".$dress['NAME']." - ₹".$dress['BASE_PRICE']."</option>
                                </select>";
                        }
                        
                    }
                ?>
            </div>

            <div class="form-group">
            <?php
                    if($_SESSION["DRESS_ID"]==null){

                        $sql="SELECT * FROM fabrics";
                        $fabrics=mysqli_query($conn,$sql);
                        if($fabrics){
                            $count=mysqli_num_rows($fabrics);

                            echo "<label for='fabric'>Choose Fabric(Per meter):</label>
                                <select id='fabric' name='fabric' onchange='updatePrice()'>
                                <option value='default' data-price='0'>None</option>";
                            for($i=0;$i<$count;$i++){
                                $row=mysqli_fetch_array($fabrics);
                                echo"   <option value='".$row['FABRIC_ID']."' data-price='".$row['PRICE_PER_UNIT']."'>".$row['NAME']." - ₹".$row['PRICE_PER_UNIT']."</option>";
                            }
                        }
                        echo "</select>";
                    }else{
                        $sql="SELECT * FROM dress WHERE DRESS_ID=".$_SESSION["DRESS_ID"]."";
                        $data=mysqli_query($conn,$sql);
                        if($data){
                            $dress=mysqli_fetch_array($data);
                            echo "<label for='fabric'>Choose Fabric(Per meter):</label>
                                    <select id='fabric' name='fabric' onchange='updatePrice()'>
                                    <option value='default' data-price='0'>".$dress['FABRIC']."</option>
                                </select>";
                        }
                        
                    }
                ?>

            </div>

            <div class="form-group">
                <!-- <label for="color">Select Color:</label>
                <input type="color" id="color" name="color"> -->
                <label for="color">Select Color:</label>
            <select id="color" name="color" onchange="updateBackgroundColor()">
                <?php
                    if($_SESSION["DRESS_ID"]==null){
                        echo "<option value='#fff'>Select color</option>";
                    }else{
                        $sql="SELECT COLOR FROM dress WHERE DRESS_ID=".$_SESSION["DRESS_ID"]."";
                        $data=mysqli_query($conn,$sql);
                        if($data){
                            $color=mysqli_fetch_array($data);
                            echo "<option value='#fff'>".$color[0]."</option>";
                        }
                        
                    }
                ?>
                <option value="#FF0000">Red</option>
                <option value="#E4080A">Dark Red</option>
                <option value="#0000FF">Blue</option>
                <option value="#FFFF00">Yellow</option>
                <option value="#FFA500">Orange</option>
                <option value="#800080">Purple</option>
                <option value="#00FFFF">Aqua</option>
                <option value="#008080">Teal</option>
                <option value="#000080">Navy</option>
                <option value="#808000">Olive</option>
                <option value="#00FF00">Lime</option>
                <option value="#FF00FF">Fuchsia</option>
                <option value="#800000">Maroon</option>
                <option value="#808080">Grey</option>
                <option value="#C0C0C0">Silver</option>
                <option value="#FFFFFF">White</option>
                <option value="#000000">Black</option>
            </select>
            </div>
                
            <div class="form-group">
                <label for="embellishments">Add Embellishments(Per meter):</label>
                <select id="embellishments" name="embellishments" onchange="updatePrice()">
                    <option value="none" data-price="0">None</option>
                    <option value="beads" data-price="50">BEADS - +₹50</option>
                    <option value="sequins" data-price="100">SEQUINS - +₹100</option>
                    <option value="embroidery" data-price="0">EMBROIDERY - +₹150 </option>
                    <option value="applique" data-price="15">APPLIQUÉ - + ₹50</option>
                    <option value="lace" data-price="20">LACE - +₹100</option>
                    <option value="fringe" data-price="0">FRINGE - +₹100</option>
                    <option value="pearl" data-price="15">PEARL - +₹200 </option>
                    <option value="piping" data-price="20">PIPING - +₹50</option>
                    <option value="rhinestone" data-price="20">RHINESTONE - +₹150</option>

                </select>
                <!-- <p style="color:palevioletred">(Price may vary depending on the complexity of the design)</p> -->
            </div>
            <div class="form-group">
                <label for=""></label>
                
                <p style="color:palevioletred">(Price may vary depending on the complexity of the design)</p>
            </div>

            <div class="form-group">
                <label for="sizes">Choose Size:</label>
                <select id="sizes" name="sizes">
                    <option value="xs">XS</option>
                    <option value="s">S</option>
                    <option value="m">M</option>
                    <option value="l">L</option>
                    <option value="xl">XL</option>
                    <option value="xxl">XXL</option>
                </select>
            </div>
                <div class="form-group">
                <label for="dresslength">Dress Length:</label>
                <select id="dresslength" name="dresslength">
                    <option value="MINI">MINI</option>
                    <option value="KNEE-LENGTH">KNEE-LENGTH</option>
                    <option value="TEA-LENGTH">TEA-LENGTH</option>
                    <option value="MIDI">MIDI</option>
                    <option value="MAXI">MAXI</option>
                    <option value="FULL-LENGTH">FULL-LENGTH</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sleevelength">Sleeve Length:</label>
                <select id="sleevelength" name="sleevelength">
                    <option value="sleeveless">SLEEVELESS</option>
                    <option value="short">SHORT</option>
                    <option value="elbow">ELBOW</option>
                    <option value="3/4">3/4</option>
                    <option value="full">FULL-LENGTH</option>
                    </select>
            </div>

            <div class="form-group">
                <label for="measurements">Enter Measurements (in inches):</label>
                <input type="text" id="shoulder" name="shoulder" placeholder="shoulder">
                <input type="text" id="bust" name="bust" placeholder="Bust">
                <input type="text" id="waist" name="waist" placeholder="Waist">
                <input type="text" id="hips" name="hips" placeholder="Hips">
            </div>

            <div class="form-group">
                <label for="additional-notes">Additional Notes:</label>
                <textarea id="additional-notes" name="additional-notes" placeholder="Enter any special requests..."></textarea>
            </div>
            <div class="form-group">
                <label for="file-upload">Upload file:</label>
                <input type="file" id="file-upload" name="file-upload">
            </div>

            <h3>Total Price: ₹<span id="total-price" name="total-price">0</span></h3>
            <input type="hidden" id="hiddenTotalPrice" name="total-price">
            <!-- <button type="submit">Preview Customization</button> -->
        
            <button type="submit" name="customize">Submit</button>
        </form>

        <?php if (isset($message)): ?>
            <p><?php echo $message; ?></p>
        <?php endif; ?>
    </div>

    <div class="footer">
        <p>&copy; 2024 Women's Boutique. All Rights Reserved.</p>
    </div>
    <script>
        function updatePrice() {
            const dressStyle = document.getElementById('dress-style');
            const fabric = document.getElementById('fabric');
            const embellishments = document.getElementById('embellishments');
            
            const dressPrice = parseInt(dressStyle.options[dressStyle.selectedIndex].getAttribute('data-price'));
            const fabricPrice = parseInt(fabric.options[fabric.selectedIndex].getAttribute('data-price'));
            const embellishmentsPrice = parseInt(embellishments.options[embellishments.selectedIndex].getAttribute('data-price'));
            
            const totalPrice = dressPrice + fabricPrice + embellishmentsPrice;
            document.getElementById('total-price').innerText = totalPrice;
            // Set the hidden field value
            document.getElementById('hiddenTotalPrice').value = totalPrice;
        }
        function updateBackgroundColor() {
            var selectElement = document.getElementById("color");
            var selectedColor = selectElement.value;
            selectElement.style.backgroundColor = selectedColor;
        }

        document.addEventListener("DOMContentLoaded", updatePrice);
    </script>
</body>
</html>