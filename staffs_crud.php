<?php
include_once 'database.php';
// 确保只有管理员可以访问此页面
if (!isset($_SESSION['user_id']) || $_SESSION['user_level'] !== 'Admin') {
    echo "<p style='color:red;'>Access Denied: You do not have permission to access this page.</p>";
    exit();
}

$conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// 创建新员工
if (isset($_POST['create'])) {
    try {
        $stmt = $conn->prepare("INSERT INTO tbl_staffs_a185125_pt2 (fld_name, fld_role, fld_email, fld_phone, password, user_level) 
                                VALUES (:name, :role, :email, :phone, :password, :user_level)");
        $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':role', $_POST['role'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(':phone', $_POST['phone'], PDO::PARAM_STR);
        $stmt->bindParam(':password', password_hash($_POST['password'], PASSWORD_BCRYPT), PDO::PARAM_STR); // 加密密码
        $stmt->bindParam(':user_level', $_POST['user_level'], PDO::PARAM_STR);

        $stmt->execute();
        header("Location: staffs.php");
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// 更新员工信息
if (isset($_POST['update'])) {
    try {
        // 先获取旧密码
        $stmt = $conn->prepare("SELECT password FROM tbl_staffs_a185125_pt2 WHERE fld_staff_id = :sid");
        $stmt->bindParam(':sid', $_POST['oldsid'], PDO::PARAM_INT);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // 如果用户没有修改密码，保持原密码
        $new_password = $_POST['password'];
        if ($new_password === "") {
            $hashed_password = $user['password']; // 旧密码
        } else {
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT); // 重新加密新密码
        }

        $stmt = $conn->prepare("UPDATE tbl_staffs_a185125_pt2 
                                SET fld_name = :name, fld_role = :role, fld_email = :email, fld_phone = :phone, 
                                    password = :password, user_level = :user_level 
                                WHERE fld_staff_id = :sid");
        $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':role', $_POST['role'], PDO::PARAM_STR);
        $stmt->bindParam(':email', $_POST['email'], PDO::PARAM_STR);
        $stmt->bindParam(':phone', $_POST['phone'], PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR); // 使用正确的密码
        $stmt->bindParam(':user_level', $_POST['user_level'], PDO::PARAM_STR);
        $stmt->bindParam(':sid', $_POST['oldsid'], PDO::PARAM_INT);

        $stmt->execute();
        header("Location: staffs.php");
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// 删除员工
if (isset($_GET['delete'])) {
    try {
        $stmt = $conn->prepare("DELETE FROM tbl_staffs_a185125_pt2 WHERE fld_staff_id = :sid");
        $stmt->bindParam(':sid', $_GET['delete'], PDO::PARAM_INT);
        $stmt->execute();
        header("Location: staffs.php");
        exit();
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// 获取单个员工信息（用于编辑）
if (isset($_GET['edit'])) {
    try {
        $stmt = $conn->prepare("SELECT * FROM tbl_staffs_a185125_pt2 WHERE fld_staff_id = :sid");
        $stmt->bindParam(':sid', $_GET['edit'], PDO::PARAM_INT);
        $stmt->execute();
        $editrow = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$conn = null;
?>
