<?php
// require_once 'connect.php';

// if (isset($_POST['st_id'])) {
//     $stID = $_POST['st_id'];

//     $conn = new Connect();
//     $db_link = $conn->connectToMySQL();
//     $sql = "SELECT * FROM store WHERE st_id = ?";
//     $stmt = $db_link->prepare($sql);
//     $stmt->bind_param('s', $stID);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     if ($result->num_rows > 0) {
//         $row = $result->fetch_assoc();
//         $storeData = array(
//             'st_id' => $row['st_id'],
//             'st_name' => $row['st_name'],
//             'st_address' => $row['st_address']
//         );
//         echo json_encode($storeData);
//     }
// }
?>
<?php

require_once 'connect.php';

if (isset($_POST['st_id'])) {
    $stID = $_POST['st_id'];
    
    $conn = new Connect();
    $db_link = $conn->connectToPDO();
    
    $sqlSelect = "SELECT * FROM store WHERE st_id = ?";
    $stmt = $db_link->prepare($sqlSelect);
    $stmt->execute(array($stID));
    
    $storeData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($storeData);
}
?>
