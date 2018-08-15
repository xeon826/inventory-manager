 <?php
include($_SERVER['DOCUMENT_ROOT'].'/includes/credentials.php');
// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
$name= (isset($_POST['name'])) ? $_POST['name'] : 3;
$starting = (isset($_POST['starting'])) ? $_POST['starting'] : '';
$received = (isset($_POST['received'])) ? $_POST['received'] : 0;
$shipped= (isset($_POST['shipped'])) ? $_POST['shipped'] : '';
$onHand= (isset($_POST['onHand'])) ? $_POST['onHand'] : '';
$minimumRequired= (isset($_POST['minimumRequired'])) ? $_POST['minimumRequired'] : '';
echo $name .' '.$sku.' '.$stock.' '.$price;
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
//sku 16 digits with letters, all caps
// $sql = "CREATE TABLE $table(name VARCHAR(255), SKU VARCHAR(255), Stock INT(11), Price DOUBLE(10,2))";
// $sql = "insert into products (name, SKU, Stock, Price) VALUES ('aadfsdf', 'dhdhd', 32, 32.00)";
$sql = "insert into inventory (ProductName, StartingInventory, InventoryReceived, InventoryShipped, InventoryOnHand, MinimumRequired) VALUES ('$name', '$image', '$sku', $stock, $price, $vendor, '$description')";

if ($conn->query($sql) === true) {
    echo "Record updated successfully";
} else {
    echo "Error updating record: " . $conn->error;
    echo $sql;
}

$conn->close();
?>
