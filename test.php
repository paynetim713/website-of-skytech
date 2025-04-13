<?php
$hashed_password = '$2y$10$R16OCMfaQPZP1//3J8GiquNmGTfTLtq3bT5nNCOpSMqI4IwMzy/4y'; // 确保与数据库存储的一致
$user_input_password = 'admin123';

if (password_verify($user_input_password, $hashed_password)) {
    echo "Password is correct!";
} else {
    echo "Incorrect password!";
}
?>
