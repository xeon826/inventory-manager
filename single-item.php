  <script src="jquery.min.js"></script>
  <script src="index.js"></script>
  <script src="main.js"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="style.css">
<?php
include($_SERVER['DOCUMENT_ROOT'].'/includes/credentials.php');
$vendor = $_GET["vendor"];
$id = $_GET["id"];
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$name= (isset($_POST['query'])) ? $_POST['query'] : 3;
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
<div class="container">
  <div class="row">
<?php
// $sql = "SELECT * from outgoing AS p JOIN vendors AS r ON p.vendor = r.vendor and p.id=$id limit 1";
$sql = "SELECT * from incoming AS p JOIN vendors AS r ON p.vendor = r.vendor and p.id=$id limit 1";
$result = $conn->query($sql);
if (empty($vendor) && $result >0) {
    $sql = "SELECT * FROM products where id = $id";
    $result = $conn->query($sql);
}
if ($result->num_rows > 0) {
    // output data of each row
    while ($row = $result->fetch_assoc()) {
        echo '
         <div class="col-sm-5">
          <img class="view-item" src="'.$row["img"].'"/>
         </div>
         <div class="col-sm-7">
          <input value ="'.$row["name"].'" class="view-name">
          <textarea class="view-description">
            '.$row["description"].'
          </textarea>
         </div>
         <div class="col-sm-4">
          <input value="'.$row["SKU"].'" class="view-sku">
          <input value="'.$row["Stock"].'" class="view-stock">
          <input value="$'.$row["Price"].'" class="view-price">
         </div>
         ';
    }
} else {
    echo "0 results";
}
$conn->close();
?>
  </div>
</div>
