<?php
include($_SERVER['DOCUMENT_ROOT'].'/includes/credentials.php');
include($_SERVER['DOCUMENT_ROOT'].'/functions.php');
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$name= (isset($_POST['query'])) ? $_POST['query'] : 3;
if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
}
// $sql = "SELECT * FROM products where name like '%$name%' limit 10 offset 0";
// $sql = "SELECT * FROM products where name like '%$name%' limit 10 offset 0";
$a = new Mysql;
// table name, search query, page number
$sql = $a->get_archive('products', $name, 1);
$result = $conn->query($sql);
if ($result->num_rows > 0) {
     // output data of each row
     echo '<div class="row displayBar">
        <div class="col-sm-2">
          <h1>Product Name</h1>
        </div>
        <div class="col-sm-2">
          <h1>Beginning Inventory</h1>
        </div>
        <div class="col-sm-2">
          <h1>Received</h1>
        </div>
        <div class="col-sm-2">
          <h1>Shipped</h1>
        </div>
        <div class="col-sm-2">
          <h1>On hand</h1>
        </div>
        <div class="col-sm-2">
          <h1>Minimum Required</h1>
        </div>
      </div>
      <div class="itemDisplay">
        <main class="row">
      ';
     while($row = $result->fetch_assoc()) {
         echo '
         <div class="col-sm-2 view-name">'.$row["ProductName"].'</div>
         <div class="col-sm-2 view-starting">'.$row["StartingInventory"].'</div>
         <div class="col-sm-2 view-received">'.$row["InventoryReceived"].'</div>
         <div class="col-sm-2 view-shipped">'. $row["InventoryShipped"].'</div>
         <div class="col-sm-2 view-on-hand">'.$row["InventoryOnHand"].'</div>
         <div class="col-sm-2 view-minimum-required">'.$row["MinimumRequired"].'</div>
         ';
     }
        echo '</main>
      </div>';
} else {
     echo "0 results";
}$conn->close();
?>
