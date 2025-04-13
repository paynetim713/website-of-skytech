<?php
session_start();
include_once 'database.php';

// 确保用户已登录
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 确保只有管理员可以访问
if ($_SESSION['user_level'] !== 'Admin') {
    echo "<script>alert('Access Denied: You do not have permission to access this page.'); window.history.back();</script>";
    exit();
}

try {
    // 连接数据库
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ 创建产品（带事务处理）
    if (isset($_POST['create'])) {
        try {
            $conn->beginTransaction(); // 开始事务

            // 插入产品
            $stmt = $conn->prepare("INSERT INTO tbl_products_a185125_pt2 
                                   (fld_product_name, fld_price, fld_type, fld_brand, fld_warranty_period, fld_quantity) 
                                   VALUES (:name, :price, :type, :brand, :warranty, :quantity)");
            $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
            $stmt->bindParam(':price', $_POST['price'], PDO::PARAM_STR);
            $stmt->bindParam(':type', $_POST['type'], PDO::PARAM_STR);
            $stmt->bindParam(':brand', $_POST['brand'], PDO::PARAM_STR);
            $stmt->bindParam(':warranty', $_POST['warranty'], PDO::PARAM_STR);
            $stmt->bindParam(':quantity', $_POST['quantity'], PDO::PARAM_INT);
            $stmt->execute();

            // 获取新创建的产品 ID
            $product_id = $conn->lastInsertId();

            // ✅ 处理图片上传
            if (!empty($_FILES["image"]["name"])) {
                $target_dir = "uploads/";
                $target_file = $target_dir . "p" . $product_id . ".jpg";
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

                // ✅ 验证是否为图片
                $check = getimagesize($_FILES["image"]["tmp_name"]);
                if ($check === false) {
                    throw new Exception("Error: The uploaded file is not a valid image.");
                }

                // ✅ 限制格式（只允许 JPG 和 PNG）
                if ($imageFileType != "jpg" && $imageFileType != "png") {
                    throw new Exception("Error: Only JPG and PNG files are allowed!");
                }

                // ✅ 限制大小（不超过 1MB）
                if ($_FILES["image"]["size"] > 1048576) {  // 1MB = 1024 * 1024 bytes
                    throw new Exception("Error: File is too large! Maximum allowed size is 1MB.");
                }

                // ✅ 限制图片尺寸（最大 300x400）
                list($width, $height) = getimagesize($_FILES["image"]["tmp_name"]);
                if ($width > 300 || $height > 400) {
                    throw new Exception("Error: Image dimensions exceed the allowed limit of 300x400 pixels.");
                }

                // ✅ 移动文件
                if (!move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                    throw new Exception("Error: There was an error uploading the file.");
                }

                // ✅ 更新数据库中的图片路径
                $stmt = $conn->prepare("UPDATE tbl_products_a185125_pt2 SET image_path = :image_path WHERE fld_product_id = :pid");
                $stmt->bindParam(':image_path', $target_file, PDO::PARAM_STR);
                $stmt->bindParam(':pid', $product_id, PDO::PARAM_INT);
                $stmt->execute();
            }

            $conn->commit(); // 提交事务
            echo "<script>alert('Product created successfully!'); window.location='products.php';</script>";
            exit();
        } catch (Exception $e) {
            $conn->rollBack(); // 回滚事务
            echo "<script>alert('" . $e->getMessage() . "'); window.history.back();</script>";
            exit();
        }
    }

    // ✅ 更新产品
    if (isset($_POST['update'])) {
        $stmt = $conn->prepare("UPDATE tbl_products_a185125_pt2 
                                SET fld_product_name=:name, fld_price=:price, 
                                    fld_type=:type, fld_brand=:brand, fld_warranty_period=:warranty, fld_quantity=:quantity 
                                WHERE fld_product_id=:pid");
        $stmt->bindParam(':pid', $_POST['pid'], PDO::PARAM_INT);
        $stmt->bindParam(':name', $_POST['name'], PDO::PARAM_STR);
        $stmt->bindParam(':price', $_POST['price'], PDO::PARAM_STR);
        $stmt->bindParam(':type', $_POST['type'], PDO::PARAM_STR);
        $stmt->bindParam(':brand', $_POST['brand'], PDO::PARAM_STR);
        $stmt->bindParam(':warranty', $_POST['warranty'], PDO::PARAM_STR);
        $stmt->bindParam(':quantity', $_POST['quantity'], PDO::PARAM_INT);
        $stmt->execute();

        // ✅ 处理图片上传
        if (!empty($_FILES["image"]["name"])) {
            $target_dir = "uploads/";
            $target_file = $target_dir . "p" . $_POST['pid'] . ".jpg";

            // ✅ 删除旧图片
            if (file_exists($target_file)) {
                unlink($target_file);
            }

            // ✅ 移动新文件
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $stmt = $conn->prepare("UPDATE tbl_products_a185125_pt2 SET image_path = :image_path WHERE fld_product_id = :pid");
                $stmt->bindParam(':image_path', $target_file, PDO::PARAM_STR);
                $stmt->bindParam(':pid', $_POST['pid'], PDO::PARAM_INT);
                $stmt->execute();

                echo "<script>alert('Image updated successfully!'); window.location='products.php';</script>";
                exit();
            } else {
                echo "<script>alert('Image upload failed! Check folder permissions.'); window.history.back();</script>";
                exit();
            }
        } else {
            echo "<script>alert('Product details updated successfully!'); window.location='products.php';</script>";
            exit();
        }
    }

    // ✅ 删除产品
    if (isset($_GET['delete'])) {
        try {
            $stmt = $conn->prepare("DELETE FROM tbl_products_a185125_pt2 WHERE fld_product_id = :pid");
            $stmt->bindParam(':pid', $_GET['delete'], PDO::PARAM_INT);
            $stmt->execute();

            // ✅ 删除对应的图片文件
            $image_path = "uploads/p" . $_GET['delete'] . ".jpg";
            if (file_exists($image_path)) {
                unlink($image_path);
            }

            echo "<script>alert('Product deleted successfully!'); window.location='products.php';</script>";
            exit();
        } catch (PDOException $e) {
            echo "<script>alert('Error: Unable to delete product. Make sure there are no linked records.'); window.history.back();</script>";
            exit();
        }
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>
