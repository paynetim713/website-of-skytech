<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <title>SkyTech Aviation Supplies: Home</title>

    <!-- Bootstrap 3.3.7 CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <style>
        /* 让背景图片铺满整个页面 */
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: url('p2.jpg') center center no-repeat fixed;
            background-size: cover;
        }
    </style>
</head>
<body>

    <?php include_once 'nav_bar.php'; ?>

    <!-- ✅ 移除 Welcome 文本 -->

    <!-- ✅ 加载 jQuery 和 Bootstrap 3.3.7 JavaScript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>

</body>
</html>
