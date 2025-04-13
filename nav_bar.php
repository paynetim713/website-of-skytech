
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 3.3.7 CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <style>
        .navbar-custom {
            background-color: #ffffff;
            border-bottom: 2px solid #ddd;
        }
        .navbar-brand {
            font-weight: bold;
            color: #333 !important;
        }
        .navbar-nav > li > a {
            color: #333 !important;
        }
        .navbar-right {
            margin-right: 15px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-default navbar-custom">
    <div class="container-fluid">
        <div class="navbar-header">
            <!-- 移动端菜单折叠按钮 -->
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-menu">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php">SkyTech Aviation Supplies</a>
        </div>

        <div class="collapse navbar-collapse" id="navbar-menu">
            <ul class="nav navbar-nav">
                <li><a href="index.php">Home</a></li>
            </ul>

            <ul class="nav navbar-nav navbar-right">
                <?php if (isset($_SESSION['user_id'])) { ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            Menu <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="products.php">Products</a></li>
                            <li><a href="customers.php">Customers</a></li>
                            <li><a href="orders.php">Orders</a></li>
                            
                            <?php if (isset($_SESSION['user_level'])): ?>
                            <?php if ($_SESSION['user_level'] === 'Admin' || $_SESSION['user_level'] === 'Staff'): ?>
                             <li><a href="staffs.php">Manage Staff</a></li>
                          <?php endif; ?>
                           <?php endif; ?>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                            <?php echo $_SESSION['user_name']; ?> (<?php echo $_SESSION['user_level']; ?>) <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                <?php } else { ?>
                    <li><a href="login.php">Login</a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script src="js/bootstrap.min.js"></script>


<script>
    $(document).ready(function(){
        $('.dropdown-toggle').dropdown();
    });
</script>

</body>
</html>
