<?php
require_once('MysqliDb.php');
class Mysql
{
    private $query;
    private $id;
    private $db;
    private $table;
    private $mp_p_pn;
    private $mp_p_la;
    private $mp_p_si;
    private $mp_p_mr;
    private $ei_s_id;
    private $ei_p_id;
    private $data;
    public function __construct()
    {
        require_once('includes/credentials.php');
        $this->db = new MysqliDb($servername, $username, $password, $dbname);
        $this->id       = (isset($_POST['id'])) ? $_POST['id'] : 3;
        $this->mp_p_pn  = (isset($_POST['mp_p_pn'])) ? $_POST['mp_p_pn']:3;
        $this->mp_p_la  = (isset($_POST['mp_p_la'])) ? $_POST['mp_p_la']:3;
        $this->mp_p_si  = (isset($_POST['mp_p_si'])) ? $_POST['mp_p_si']:3;
        $this->mp_p_mr  = (isset($_POST['mp_p_mr'])) ? $_POST['mp_p_mr']:3;
        $this->ei_s_id  = (isset($_POST['ei_s_id'])) ? $_POST['ei_s_id']:'none';
        $this->ei_p_id  = (isset($_POST['ei_p_id'])) ? $_POST['ei_p_id']:'none';
        $this->table    = (isset($_POST['table'])) ? $_POST['table']:3;
        $this->data     = (isset($_POST['data'])) ? $_POST['data']:3;
    }
    public function get_product()
    {
        $this->db->where('id', $this->id);
        $p = $this->db->getOne('products');
        echo "
                <div class='form container manage-table manage-product'>
                    <h1>Manage Product</h1>
                    <div class='form-row'>
                    <div class='form-group col-sm-6'>
                    <label for='product-name'>Product Name</label>
                    <input name='ProductName' id='product-name' value='{$p['ProductName']}' class='product-name form-control'></input>
                    <label for='label'>Label</label>
                    <input name='Label' id='label' value='{$p['Label']}' class='label form-control'></input>
                    </div>
                    <div class='form-group col-sm-6'>
                    <label for='starting-inventory'>Starting Inventory</label>
                    <input name='StartingInventory' id='starting-inventory' value='{$p['StartingInventory']}' class='starting-inventory form-control'></input>
                    <label for='minimum-required'>Minimum Required</label>
                    <input name='MinimumRequired' id='minimum-required' value='{$p['MinimumRequired']}' class='minimum-required form-control'></input>
                    </div>
                    </div>
                    <button data-id='{$p['id']}' data-action='manage_entry' data-table='products' class='get edit-product btn btn-primary'>Confirm</button></br>
                    <p class='parCreate'></p>
                </div>
                                 ";
    }
    public function manage_entry()
    {
        $this->db->where('id', $this->id);
        if ($this->db->update($this->table, $this->data)) {
            echo $this->db->count . ' records were updated';
        } else {
            echo 'update failed: ' . $this->db->getLastError();
        }
    }
    public function manage_products()
    {
        $products = $this->db->get('products');
        echo view('products', 'open');
        foreach ($products as $p) {
            echo "
                <tr class='edit_row'>
                 <td>{$p['ProductName']}
                 <div data-id='{$p['id']}' class='manage'>
                 <i data-id='{$p['id']}' data-action='conf_del_p' class='fas fa-times edit get'></i>
                 <i data-id='{$p['id']}' data-action='get_product' class='fas fa-pencil-alt edit get'></i>
                 </div>
                 </td>
                 <td>{$p['Label']}</td>
                 <td>{$p['StartingInventory']}</td>
                 <td>{$p['MinimumRequired']}</td>
                 </tr>
                 ";
        }
        echo view('products', 'close');
    }
    public function manage_outgoing()
    {
        $this->db->join("products p", "p.id=o.ProductId", "LEFT");
        $outgoing = $this->db->get('outgoing o', null, "o.First, o.Middle, o.Last, o.ProductId, o.NumberShipped, o.OrderDate, p.ProductName"); //contains an Array 10 users
        if ($this->db->count == 0) {
            echo "<td align=center colspan=4>No products found</td>";
            return;
        }
        echo view('outgoing', 'open');
        foreach ($outgoing as $o) {
            echo "
                <tr>
         <td>{$o['First']}
                 <div data-id='{$i['id']}' class='manage'>
                 <i data-id='{$i['id']}' data-action='conf_del_p' class='fas fa-times edit incoming get'></i>
                 <i data-id='{$i['id']}' data-supplier='{$i['SupplierId']}' data-product='{$i['ProductId']}' data-action='get_outgoing_product' class='fas fa-pencil-alt edit single-incoming get'></i>
                 </div>
         </td>
         <td>{$o['Middle']}</td>
         <td>{$o['Last']}</td>
         <td>{$o['ProductName']}</td>
         <td>{$o['NumberShipped']}</td>
         <td>{$o['OrderDate']}</td>
         </tr>
         ";
        }
        echo view('outgoing', 'close');
    }
    public function del_i()
    {
        $this->db->where('id', $this->id);
        if ($this->db->delete('incoming')) {
            echo 'Product successfully deleted';
        }
    }
    public function del_p()
    {
        $this->db->where('id', $this->id);
        if ($this->db->delete('products')) {
            echo 'Product successfully deleted';
        }
        $this->db->where('ProductId', $this->id);
        if ($this->db->delete('incoming')) {
            echo 'Incoming successfully deleted';
        }
        $this->db->where('ProductId', $this->id);
        if ($this->db->delete('outgoing')) {
            echo 'Outgoing successfully deleted';
        }
    }
    public function conf_del_p()
    {
        $this->db->where('id', $this->id);
        $p= $this->db->getOne('products');
        echo "
                <div class='container manage-table'>
                    <h1>Delete Product</h1>
                    <h2 class=''>Are you sure? This will get rid of matching entries in outgoing and incoming. </br>Delete {$p['ProductName']}?</h2>
                    <button data-id='{$p['id']}' data-action='del_p' class='get delete-product btn btn-primary'>Confirm</button></br>
                    <p class='parCreate'></p>
                </div>
                                 ";
    }
    public function vendors()
    {
        $vendors = $this->db->get('vendors');
        foreach ($this->ei_s_id!='none'?$this->move_to_top($vendors, $this->ei_s_id):$vendors as $v) {
            echo "<option data-id='{$v['id']}' value='{$v['id']}'>{$v['supplier']}</option>";
        }
    }
    public function items()
    {
        $items = $this->db->get('products');
        foreach ($this->ei_p_id!='none'?$this->move_to_top($items, $this->ei_p_id):$items as $i) {
            echo "<option value='{$i['id']}'>{$i['ProductName']}</option>";
        }
    }
    public function move_to_top($array, $id)
    {
        foreach ($array as $key => $val) {
            if ($val['id'] == $id) {
                unset($array[$key]);
                array_unshift($array, $val);
                return $array;
            }
        }
    }
    public function manage_incoming()
    {
        $this->db->join("products p", "p.id=i.ProductId", "LEFT");
        $this->db->join("vendors v", "v.id=i.SupplierId", "LEFT");
        $incoming = $this->db->get("incoming i", null, "i.id, i.SupplierId, i.ProductId, v.supplier, p.ProductName, i.PurchaseDate, i.NumReceived");
        if ($this->db->count == 0) {
            echo "<td align=center colspan=4>No products found</td>";
            return;
        }
        echo view('incoming', 'open');
        foreach ($incoming as $i) {
            echo "
                <tr class='edit_row'>
         <td>{$i['PurchaseDate']}
                 <div data-id='{$i['id']}' class='manage'>
                 <i data-id='{$i['id']}' data-action='conf_del_p' class='fas fa-times edit incoming get'></i>
                 <i data-id='{$i['id']}' data-supplier='{$i['SupplierId']}' data-product='{$i['ProductId']}' data-action='get_incoming_product' class='fas fa-pencil-alt edit single-incoming get'></i>
                 </div>
         </td>
         <td>{$i['ProductName']}</td>
         <td>{$i['NumReceived']}</td>
         <td>{$i['supplier']}</td>
         </tr>
         ";
        }
        echo view('incoming', 'close');
    }
    public function get_incoming_product()
    {
        $this->db->join("products p", "p.id=i.ProductId", "LEFT");
        $this->db->join("vendors v", "v.id=i.SupplierId", "RIGHT");
        $this->db->where("i.id", $this->id);
        $i= $this->db->getOne("incoming i", null, "i.PurchaseDate, p.ProductName, i.NumReceived, v.supplier");
        if ($this->db->count == 0) {
            echo "<td align=center colspan=4>No products found</td>";
            return;
        }
        echo view('incoming_product', 'open');
        echo "
        <input name='PurchaseDate' id='purchase-date' value='{$i['PurchaseDate']}' class='purchase-date form-control'></input>
        </div>
        <div class='form-group col-sm-6'>
        <label for='number-received'>Number Received</label>
        <input name='NumReceived' id='number-received' value='{$i['NumReceived']}' class='number-received form-control'></input>
        <label for='vendor'>Vendor</label>
        <select name='SupplierId' class='form-control vendor-dropdown'>
        </select>
        </div>
        </div>
        <button data-id='{$i['id']}' data-action='manage_entry' data-table='incoming' class='get edit-product btn btn-primary'>Confirm</button></br>
                                 ";
        echo view('incoming_product', 'close');
    }
    public function get_incoming()
    {
        $this->db->join("products p", "p.id=i.ProductId", "LEFT");
        $this->db->join("vendors v", "v.id=i.SupplierId", "LEFT");
        $incoming = $this->db->get("incoming i", null, "v.supplier, p.ProductName, i.PurchaseDate, i.NumReceived");
        if ($this->db->count == 0) {
            echo "<td align=center colspan=4>No products found</td>";
            return;
        }
        echo view('incoming', 'open');
        foreach ($incoming as $i) {
            echo "
                <tr>
         <td>{$i['PurchaseDate']}</td>
         <td>{$i['ProductName']}</td>
         <td>{$i['NumReceived']}</td>
         <td>{$i['supplier']}</td>
         </tr>
         ";
        }
        echo view('incoming', 'close');
    }

    public function get_outgoing()
    {
        $this->db->join('products p', 'p.id=o.ProductId', 'RIGHT');
        $outgoing = $this->db->get('outgoing o', null, 'o.First, o.Middle, o.Last, p.ProductName, o.NumberShipped, o.OrderDate'); //contains an Array 10 users
        if ($this->db->count == 0) {
            echo "<td align=center colspan=4>No products found</td>";
            return;
        }
        echo view('outgoing', 'open');
        foreach ($outgoing as $o) {
            echo "
                <tr>
         <td>{$o['First']} </td>
         <td>{$o['Middle']}</td>
         <td>{$o['Last']}</td>
         <td>{$o['ProductName']}</td>
         <td>{$o['NumberShipped']}</td>
         <td>{$o['OrderDate']}</td>
         </tr>
         ";
        }
        echo view('outgoing', 'close');
    }
    public function get_current_inv()
    {
        $products = $this->db->rawQuery('SELECT p.id, coalesce(ig.NumReceived,0) as NumReceived,
        coalesce(og.NumShipped,0) as NumShipped, p.Label,
        coalesce((p.StartingInventory-og.NumShipped+ig.NumReceived), p.StartingInventory)
        as OnHand, p.ProductName,
        p.StartingInventory, p.MinimumRequired
        from products p
        left outer join (
            select productid, sum(NumReceived) as NumReceived
            from incoming
            group by productid
        ) as ig on p.id = ig.productid
        left outer join (
            select productid, sum(NumberShipped) as NumShipped
            from outgoing
            group by productid
        ) as og on p.id = og.productid');
        if ($this->db->count == 0) {
            echo "<td align=center colspan=4>No products found</td>";
            return;
        }
        echo view('current_inv', 'open');
        foreach ($products as $p) {
            echo "<tr>
            <td>{$p['ProductName']}</td>
            <td>{$p['Label']}</td>
            <td>{$p['StartingInventory']}</td>
            <td>{$p['NumShipped']}</td>
            <td>{$p['NumReceived']}</td>
            <td>{$p['OnHand']}</td>
            <td>{$p['MinimumRequired']}</td>
        </tr>";
        }
        echo view('current_inv', 'close');
    }


    public function get_archive($table, $name, $page)
    {
        $page--;
        $page *= 9;
        return "SELECT * FROM $table limit 10 offset $page";
    }
}
$action= (isset($_POST['action'])) ? $_POST['action'] : 3;
$object = new Mysql();
switch ($action) {
  case 'get_current_inv': $object->get_current_inv();
  break;
  case 'get_outgoing': $object->get_outgoing();
  break;
  case 'get_incoming': $object->get_incoming();
  break;
  case 'prod_dropdown': $object->prod_dropdown();
  break;
  case 'add_new_product': $object->add_new_product();
  break;
  case 'manage_products': $object->manage_products();
  break;
  case 'get_product': $object->get_product();
  break;
  case 'manage_entry': $object->manage_entry();
  break;
  case 'conf_del_p': $object->conf_del_p();
  break;
  case 'del_p': $object->del_p();
  break;
  case 'manage_incoming': $object->manage_incoming();
  break;
  case 'get_incoming_product': $object->get_incoming_product();
  break;
  case 'items': $object->items();
  break;
  case 'vendors': $object->vendors();
  break;
  case 'manage_outgoing': $object->manage_outgoing();
  break;
  case 'create_triggers': $object->create_triggers();
  break;
}

function view($view, $operate)
{
    if ($view == "incoming_product" && $operate == "close") {
        return   "<p class='parCreate'></p>
                </div>";
    }
    if ($operate=='close') {
        return '</tbody>
        </table>
        </div>
        ';
    }
    switch ($view) {
    case 'incoming_product':
    return "
    <div class='container manage-table'>
    <h1>Manage Product</h1>
    <div class='form-row'>
    <div class='form-group col-sm-6'>
    <label for='product-name'>Product Name</label>
    <select name='ProductId' class='form-control item-dropdown'>
    </select>
    <label for='purchase-date'>Purchase Date</label>
    ";
    case 'products':
    return '
    <div class="table-wrapper">
    <table class="fl-table">
      <thead>
        <tr>
            <th>Name</th>
            <th>Label</th>
            <th>Starting Inventory</th>
            <th>Minimum Required</th>
        </tr>
        </thead>
        <tbody>
        ';
    case 'current_inv':
      return '
      <div class="table-wrapper">
      <table class="fl-table">
        <thead>
          <tr>
              <th>Name</th>
              <th>Label</th>
              <th>Current Inventory</th>
              <th>Shipped</th>
              <th>Incoming</th>
              <th>On Hand</th>
              <th>Minimum Required</th>
          </tr>
          </thead>
          <tbody>
          ';
      case 'incoming':
        return '
      <div class="table-wrapper">
      <table class="fl-table">
        <thead>
        <tr>
            <th>Purchase Date</th>
            <th>Product Name</th>
            <th>Number Received</th>
            <th>Supplier</th>
        </tr>
        </thead>
        <tbody>
          ';
      case 'outgoing':
        return '
        <div class="table-wrapper">
        <table class="fl-table">
          <thead>
            <tr>
            <th>First Name</th>
            <th>Middle Name</th>
            <th>Last Name</th>
            <th>Product Name</th>
            <th>Number Shipped</th>
            <th>Order Date</th>
        </tr>
        </thead>
        <tbody>
              ';
    }
}
function move_to_top(&$array, $key)
{
    $temp = array($key => $array[$key]);
    unset($array[$key]);
    $array = $temp + $array;
}
