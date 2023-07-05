<?php
    include_once 'header.php';
?>

<!-- CSS Start -->
<link rel="stylesheet" href="./assets/css/add_category.css">
<!--  CSS End -->

<div class="body">
        <h1>Toy Categories</h1>
            <div id="main" class="container border">
                <div className="page-heading pb-2 mt-4 mb-2 ">
                
                </div>
<?php
// ADDING
                require_once 'connect.php';
                $conn = new Connect();
                $db_link = $conn->connectToPDO();
                if(isset($_GET['cid'])):
                        $value = $_GET['cid'];
                        $sqlSelect = "SELECT * FROM category WHERE cate_id = ?"; //This one
                        $stmt = $db_link->prepare($sqlSelect);
                        $stmt->execute(array("$value"));
                        if($stmt->rowCount()>0):
                                $re = $stmt->fetch(PDO::FETCH_ASSOC);
                                $cate_name = $re['cate_name'];
                        endif;
                endif;

                if(isset($_POST['txtID']) && isset($_POST['txtName'])):
                        $cid = $_POST['txtID'];
                        $cname = $_POST['txtName'];
                        //Check duplicate value
                        $conn = new Connect();
                        $db_link = $conn->connectToMySQL();
                        $query = "SELECT COUNT(*) as count FROM `category` WHERE `cate_id` = '$cid'";
                        $re = $db_link->query($query);
                        $row = $re->fetch_assoc();
                        $count = $row['count'];
                        if(isset($_POST['btnAdd'])):
                                $pattern = '/^(C)\d{4}$/';
                                $input = $_POST['txtID'];
                            if (preg_match($pattern, $input)):
                                if ($count > 0) {
                                        // Duplicate data found, handle the error
                                        echo '<script>alert("Error: Duplicate data found!")</script>';
                                }else{
                                $sqlInsert = "INSERT INTO `category`(`cate_id`, `cate_name`) VALUES (?,?)";
                                $stmt = $db_link->prepare($sqlInsert);
                                $execute = $stmt->execute(array("$cid","$cname"));
                                if($execute){
                                        echo '<script>
                                        Swal.fire({
                                          title: "Success!",
                                          text: "Item added successfully!",
                                          icon: "success",
                                          confirmButtonText: "OK"
                                        });
                                      </script>';

                                        header("Location:add_category.php");
                                        exit();
                                }else{
                                        echo "Failed ".$execute;
                                }
                                }
                           else:
                                echo '<script>alert("Error: Enter an ID that start with either B, C or P followed by four digits")</script>';  
                           endif;
                        else:
// UPDATING
                                        $sqlUpdate = "UPDATE `category` SET `cate_id`=?,`cate_name`=? WHERE `cate_id` = ?";
                                        $stmt = $db_link->prepare($sqlUpdate);
                                        $execute = $stmt->execute(array("$cid","$cname","$cid"));
                                        if($execute){
                                                header("Location: view_category.php");
                                        }else{
                                                echo "Failed".$execute;
                                        }
                        endif;
                endif;                
?>

                <form id="form1" name="form1" method="post" action="" class="form-horizontal" role="form">
                    <div class="form-group pb-3 pt-3">
                        <label for="txtTen" class="col-sm-2 control-label">Category Id:  </label>
                            <div class="col-sm-12">
                                <input required type="text" name="txtID" id="txtID" class="form-control" placeholder="Category ID" value='<?php echo isset($_GET["cid"])?($_GET["cid"]):"";?>'>
                            </div>
                            <h6 style="font-style: italic; color: green; padding: 3px 0 0 10px; font-size: 13px">(*)Enter your Category Id here with P followed by 4 numbers (Ex:C0001)</h6>
                    </div>
                    <div class="form-group pb-3">
                            <label for="txtTen" class="col-sm-2 control-label">Category Name:  </label>
                            <div class="col-sm-12">
                                <input required type="text" name="txtName" id="txtName" class="form-control" placeholder="Category Name" value='<?php echo isset($cate_name)?($cate_name):"";?>'>
                            </div>
                            <h6 style="font-style: italic; color: green; padding: 3px 0 0 10px; font-size: 13px">(*) Enter your category here</h6>
                    </div>
                        <style>
                                .select{
                                        padding-right: 20px;
                                }
                        </style>
                    <div class="form-group pb-3">
                        <div class="col-sm-offset-2 col-sm-10">
                                <input style="background-color: #2B3467; border: none" type="submit"  class="btn btn-primary " name="<?php echo isset($_GET["cid"])?"btnEdit":"btnAdd"; ?>" id="btnAction" value='<?php echo isset($_GET["cid"])?"Update":"Add new"?>'/>
                                <input style="background-color: #2B3467; border: none" type="button" class="btn btn-primary" name="btnIgnore"  id="btnIgnore" value="Back to list" onclick="window.location.href='view_category.php'" />
                        </div>
                    </div>
                </form>
            </div>
    </div>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <!-- JS
============================================ -->

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