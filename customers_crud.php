<?php

include_once 'database.php';


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


if (isset($_POST['create']) && $_SESSION['user_level'] === 'Admin') {
    try {
        $stmt = $conn->prepare("INSERT INTO tbl_customers_a185125_pt2 (fld_name, fld_contact, fld_address) 
                               VALUES (:name, :contact, :address)");
        $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':contact', $_POST['contact'], PDO::PARAM_STR);
        $stmt->bindParam(':address', $_POST['address'], PDO::PARAM_STR);
        $stmt->execute();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


if (isset($_POST['update']) && $_SESSION['user_level'] === 'Admin') {
    try {
        $stmt = $conn->prepare("UPDATE tbl_customers_a185125_pt2 
                                SET fld_name = :name, fld_contact = :contact, fld_address = :address 
                                WHERE fld_customer_id = :cid");
        $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':contact', $_POST['contact'], PDO::PARAM_STR);
        $stmt->bindParam(':address', $_POST['address'], PDO::PARAM_STR);
        $stmt->bindParam(':cid', $_POST['cid'], PDO::PARAM_INT);
        $stmt->execute();
        header("Location: customers.php");
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}


if (isset($_GET['delete']) && $_SESSION['user_level'] === 'Admin') {
    try {
        
        $stmt = $conn->prepare("SELECT COUNT(*) FROM tbl_customers_a185125_pt2 WHERE fld_customer_id = :cid");
        $stmt->bindParam(':cid', $_GET['delete'], PDO::PARAM_INT);
        $stmt->execute();
        $count = $stmt->fetchColumn();

        if ($count == 0) {
            echo "Error: Customer not found.";
            exit();
        }

     
        $stmt = $conn->prepare("DELETE FROM tbl_customers_a185125_pt2 WHERE fld_customer_id = :cid");
        $stmt->bindParam(':cid', $_GET['delete'], PDO::PARAM_INT);
        $stmt->execute();

       
        header("Location: customers.php");
        exit();
    } catch(PDOException $e) {
        var_dump($e->getMessage());
        exit();
    }
}

if (isset($_GET['edit'])) {
    if ($_SESSION['user_level'] !== 'Admin') {
        echo "<p style='color:red;'>Access Denied: You do not have permission to edit customer records.</p>";
        exit();
    }
    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_customers_a185125_pt2 WHERE fld_customer_id = :cid");
        $stmt->bindParam(':cid', $_GET['edit'], PDO::PARAM_INT);
        $stmt->execute();
        $editrow = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$conn = null;
?>
