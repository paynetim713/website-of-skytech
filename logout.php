<?php
session_start();
session_destroy(); // 清除所有 session
header("Location: login.php"); // 退出后重定向到登录页面
exit();
?>
