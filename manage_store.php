<?php
    include_once 'header.php';
    $operation = ""; 
?>

<!-- CSS Start -->
<link rel="stylesheet" href="./assets/css/manage_store.css">
<!--  CSS End -->
<title>Popup Form</title>
<style>
/* Styling for the popup form */
.popup-form {
    display: none;
    position: fixed;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
}

.popup-form-content {
    background-color: #fff;
    border: 1px solid #ccc;
    border-radius: 5px;
    width: 500px;
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    padding: 20px;
}

.popup-form label {
    display: block;
    margin-bottom: 10px;
}

.popup-form input[type="text"] {
    width: 100%;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 3px;
}

.popup-form button {
    padding: 10px 20px;
    background-color: #4CAF50;
    color: #fff;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: background-color 0.3s ease;
}

.popup-form button:hover {
    background-color: #45a049;
}
</style>
<script>
function showPopupForm() {
    document.getElementById('popupForm').style.display = 'block';
}

function hidePopupForm() {
    document.getElementById('popupForm').style.display = 'none';
    window.location.reload();
}

function submitForm() {
    if (validateForm()) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "manage_store.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        var formData = new FormData(document.getElementById("form1"));

        xhr.onreadystatechange = function() {
            if (xhr.readyState === XMLHttpRequest.DONE) {
                if (xhr.status === 200) {
                    // Handle the response from the server
                    console.log(xhr.responseText);
                    hidePopupForm(); // Hide the popup form after successful update
                    // window.location.reload(); // Reload the page to reflect the updated data
                } else {
                    // Handle any errors
                    console.error(xhr.statusText);
                }
            }
        };

        xhr.send(formData);
    }
}

function fillForm(stID) {
  var btnAction = document.getElementById('btnAction');
  var form = document.getElementById('form1');
  var txtID = document.getElementById('txtID');
  var txtName = document.getElementById('txtName');
  var txtAddress = document.getElementById('txtAddress');

  // Send a POST request to retrieve store data
  var xhr = new XMLHttpRequest();
  xhr.open("POST", "get_store_details.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.onreadystatechange = function() {
    if (xhr.readyState === XMLHttpRequest.DONE) {
      if (xhr.status === 200) {
        console.log(xhr.responseText); // Log the response for debugging

        try {
          var storeData = JSON.parse(xhr.responseText);
          if (storeData) {
            txtID.value = storeData.st_id;
            txtName.value = storeData.st_name;
            txtAddress.value = storeData.st_address;
            btnAction.innerHTML = "Edit"; // Set button name to "Edit"
            form.setAttribute("action", "manage_store.php"); // Set form action for edit mode
            showPopupForm();
          }
        } catch (error) {
          console.error(error);
        }
      } else {
        console.error(xhr.statusText);
      }
    }
  };
  xhr.send("st_id=" + stID);
}



</script>

<div id="popupForm" class="popup-form" style="display: none;">
    <div class="popup-form-content">
        <h2>Store Information</h2>
        <form name="form1" id="form1" method="post" action="#">
            <input type="hidden" name="st_id" id="txtID" value="">
            <label for="storeName">Store Name:</label>
            <input type="text" name="txtName" id="txtName" value="" required>
            <label for="storeAddress">Store Address:</label>
            <input type="text" name="txtAddress" id="txtAddress" value="" required>
            <div style="display: flex; justify-content: flex-end;">
                <button type="button" onclick="hidePopupForm()"
                    style="margin-top: 10px; padding: 10px 20px; background-color: #ddd; color: #000; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.3s ease;">Close</button>
                    <button id="btnAction" type="submit" onclick="submitForm()"
                    style="margin-top: 10px; margin-left: 10px; padding: 10px 20px; background-color: rgb(145, 203, 234); color: #fff; border: none; border-radius: 5px; cursor: pointer; font-size: 16px; transition: background-color 0.3s ease;"
                    name="<?php echo ($operation === 'edit') ? 'btnEdit' : 'btnAdd'; ?>"><?php echo ($operation === 'edit') ? 'Edit' : 'Submit'; ?></button>
            </div>
        </form>
    </div>
</div>

<!--  -->
<div class="body">
    <h1 style="text-align: center; font-weight: bold">Toy Stores</h1>
    <button onclick="showPopupForm()"
        style="font-size: 18px; margin-left:8%; padding: 10px 20px; background-color: rgb(145, 203, 234); color: #000; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s ease;">Add
        Store</button>
    <?php
if (isset($_SESSION['form_data'])) {
    $form_data = $_SESSION['form_data'];
    // Xóa dữ liệu trong session sau khi khôi phục
    unset($_SESSION['form_data']);
  }
                require_once 'connect.php';
                $conn = new Connect();
                $db_link = $conn->connectToPDO();
                if(isset($_GET['st_id'])):
                        $value = $_GET['st_id'];
                        $sqlSelect = "SELECT * FROM store WHERE st_id = ?";
                        $stmt = $db_link->prepare($sqlSelect);
                        $stmt->execute(array($value));
                        if($stmt->rowCount()>0):
                                $re = $stmt->fetch(PDO::FETCH_ASSOC);
                                $stID = $re['st_id'];
                                $stName = $re['st_name'];
                                $stAddress = $re['st_address'];
                        endif;
                endif;
// ADDING
                if(isset($_POST['txtAddress']) && isset($_POST['txtName'])){
                        // $stID = $_POST['txtID'];
                        $stName = $_POST['txtName'];
                        $stAddress = $_POST['txtAddress'];
                    
                        $conn = new Connect();
                        $db_link = $conn->connectToMySQL();
                        
                        if(isset($_POST['st_id'])) {
                            // Edit operation
                            $stID = $_POST['st_id'];
                            $sqlUpdate = "UPDATE `store` SET `st_name`=?, `st_address`=? WHERE `st_id` = ?";
                            $stmt = $db_link->prepare($sqlUpdate);
                            $execute = $stmt->execute(array($stName, $stAddress, $stID));
                            if($execute) {
                                $operation = "edit"; // Set operation as "edit" for button name
                            } else {
                                echo "Failed".$execute;
                            }
                        } else {
                            // Add operation
                            $sqlInsert = "INSERT INTO `store`(`st_name`, `st_address`) VALUES (?,?)";
                            $stmt = $db_link->prepare($sqlInsert);
                            $execute = $stmt->execute(array($stName, $stAddress));
                            if($execute) {
                                $operation = "add"; // Set operation as "add" for button name
                            } else {
                                echo "Failed ".$execute;
                            }
                        }
                    } 

?>

    <!-- <form id="form1" name="form1" method="POST" action="" class="form-horizontal" role="form">
                    <div class="form-group pb-3 pt-3">
                            <div class="col-sm-12">
                                <input type="text" name="st_id" id="txtID" class="form-control" placeholder="Store ID" value='<?php echo isset($_GET["st_id"])?($_GET["st_id"]):"";?>' hidden>
                            </div>
                    </div>
                    <div class="form-group pb-3">
                            <label for="txtTen" class="col-sm-2 control-label">Store Name:  </label>
                            <div class="col-sm-12">
                                <input required type="text" name="txtName" id="txtName" class="form-control" placeholder="Store Name" value='<?php echo isset($stName)?($stName):"";?>'>
                            </div>
                            <h6 style="font-style: italic; color: green; padding: 3px 0 0 10px; font-size: 13px">(*) Enter your store name here</h6>
                    </div>
                    <div class="form-group pb-3">
                            <label for="txtTen" class="col-sm-2 control-label">Store Address:  </label>
                            <div class="col-sm-12">
                                <input required type="text" name="txtAddress" id="txtAddress" class="form-control" placeholder="Store Address" value='<?php echo isset($stAddress)?($stAddress):"";?>'>
                            </div>
                            <h6 style="font-style: italic; color: green; padding: 3px 0 0 10px; font-size: 13px">(*) Enter your store address here</h6>
                    </div>
                    <div class="form-group pb-3">
                        <div class="col-sm-offset-2 col-sm-10">
                            <input style="background-color: #2B3467; border: none" type="submit" class="btn btn-primary" name="<?php echo isset($_GET["st_id"]) ? "btnEdit" : "btnAdd"; ?>" id="btnAction" value="<?php echo isset($_GET["st_id"]) ? "Update" : "Add new"; ?>" />
                        </div>
                    </div>
                </form> -->
</div>

<div class="table-responsive" id="tables">
    <table class="table table-hover" id="myTable">
        <thead>
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Store Name</th>
                <th scope="col">Store Address</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            <?php
                        include_once 'connect.php';
                        $conn = new Connect();
                        $db_link = $conn->connectToMySQL();
                        $sql = "SELECT * FROM `store` ORDER BY `st_id` ASC";
                        $re = $db_link->query($sql);
                        while($row = $re->fetch_assoc()): 
                    ?>
            <tr>
                <td><?=$row['st_id']?></td>
                <td><?=$row['st_name']?></td>
                <td><?=$row['st_address']?></td>
                <td>
                    <a>
                        <div class="button" onclick="fillForm('<?= $row['st_id'] ?>')">Edit</div>
                        <div class="button" onclick="removeStore('<?=$row['st_id']?>')">Delete</div>
                    </a>
                </td>
            </tr>
            <?php
                        endwhile;
                    ?>
        </tbody>
    </table>
</div>

<?php
// DELETING
require_once 'connect.php';
if(isset($_GET['del_id'])){
    $pro_del = $_GET['del_id'];
    $query = "DELETE FROM `store` WHERE `st_id` = ?";
    $stmt = $db_link->prepare($query);
    $stmt->bind_param('s', $pro_del);
    $stmt->execute();
    if($stmt){
        header("Location:manage_store.php");
    }
}
?>
<script>
function removeStore(id2) {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleted',
                text: 'The item has been deleted.',
                icon: 'success',
                showConfirmButton: false
            })
            setTimeout(() => {
                window.location.href = 'manage_store.php?del_id=' + id2;
            }, 2000);

        } else if (result.isDenied) {
            Swal.fire('Changes are not saved', '', 'info')
        }
    })
}
</script>

<!-- JS
============================================ -->
<script src="../js/alert.js"></script>
<script src="../js/pagination.js"></script>
<script src="sweetalert2.all.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- jQuery JS -->
<script src="../js/vendor/jquery-3.6.0.min.js"></script>
<!-- Migrate JS -->
<script src="../js/vendor/jquery-migrate-3.3.2.min.js"></script>
<!-- Bootstrap JS -->
<script src="../js/bootstrap.bundle.min.js"></script>
<!-- Plugins JS -->
<script src="../js/plugins.js"></script>
<!-- Main JS -->
<script src="../js/main.js"></script>


<?php
    include_once 'footer.php';
?>