<?php
    require('inc/essentials.php');
    require ('inc/db_config.php');
    adminLogin();
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <?php require('inc/links.php');?>

    <style>
        .text-bg{
            color: white !important;
        }
    </style>

</head>
<body class="bg-light">
   
    <?php

        require('inc/header.php');

        $is_shutdown = mysqli_fetch_assoc(mysqli_query($con,"SELECT `shutdown` FROM `settings` "));

        $current_bookings = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(id) AS `count` FROM `payment`"));
        $unread_queries = mysqli_fetch_assoc(mysqli_query($con,"SELECT COUNT(sr_no) AS `count` FROM `user_queries` WHERE `seen`=0"));

        $current_users = mysqli_fetch_assoc(mysqli_query($con, "SELECT 
        COUNT(id) AS `total`,
        COUNT(CASE WHEN `status`=1 THEN 1 END) AS `active`,
        COUNT(CASE WHEN `status`=0 THEN 1 END) AS `inactive`
        FROM `user_cred`"));

    ?>


    <div class="container-fluid" id="main-content">
        <div class="row">
           <div class="col-lg-10 ms-auto p-4 overflow-hidden">
               <div class="d-flex align-items-center justify-content-between mb-4">
                   <h3>DASHBOARD</h3>
                   <?php if ($is_shutdown['shutdown']){
                       echo<<<data
                            <h6 class="badge bg-danger py-2 px-3 rounded">Shutdown mode is Active !</h6>
                        data;
                   }?>
               </div>

               <div class="row mb-4">
                   <div class="col-md-4 mb-4">
                       <a href="new_bookings.php" class="text-decoration-none">
                           <div class="card text-center text-success p-3">
                               <h6>New Booking</h6>
                               <h1 class="mt-2 mb-0"><?php echo $current_bookings['count']?></h1>
                           </div>
                       </a>
                   </div>
                   <div class="col-md-4 mb-4">
                       <a href="user_queries.php" class="text-decoration-none">
                           <div class="card text-center text-info p-3">
                               <h6>New Queries</h6>
                               <h1 class="mt-2 mb-0"><?php echo $unread_queries['count']?></h1>
                           </div>
                       </a>
                   </div>
                   <div class="col-md-4 mb-4">
                       <a href="users.php" class="text-decoration-none">
                           <div class="card text-center text-info p-3">
                               <h6>User</h6>
                               <h1 class="mt-2 mb-0"><?php echo $current_users['total']?></h1>
                           </div>
                       </a>
                   </div>
               </div>

               <div class="d-flex align-items-center justify-content-between mb-4">
                   <h3>Booking Analytics</h3>
                   <select class="form-select shadow-none bg-light w-auto" onchange="booking_analytics(this.value)">
                       <option value="1">Past 30 Days</option>
                       <option value="2">Past 60 Days</option>
                       <option value="3">Past 90 Days</option>
                       <option value="4">All Time</option>
                   </select>
               </div>

               <div class="row mb-3">
                   <div class="col-md-6 mb-4">
                       <div class="card text-center text-primary p-3">
                           <h6>Total Booking</h6>
                           <h1 class="mt-2 mb-0" id="total_booking"></h1>
                           <h6 class="mt-2 mb-0" id="total_price">৳0</h6>
                       </div>
                   </div>

                   <div class="col-md-6 mb-4">
                       <div class="card text-center text-success p-3">
                           <h6>Active Booking</h6>
                           <h1 class="mt-2 mb-0" id="active_bookings">0</h1>
                           <h6 class="mt-2 mb-0" id="active_price">৳0</h6>
                       </div>
                   </div
               </div>

               <div class="d-flex align-items-center justify-content-between mb-4">
                   <h3>User, Queries Analytics</h3>
                   <select class="form-select shadow-none bg-light w-auto" onchange="user_analytics(this.value)">
                       <option value="1">Past 30 Days</option>
                       <option value="2">Past 60 Days</option>
                       <option value="3">Past 90 Days</option>
                       <option value="4">All Time</option>
                   </select>
               </div>

               <div class="row mb-3">
                   <div class="col-md-6 mb-4">
                       <div class="card text-center text-primary p-3">
                           <h6>New Registration</h6>
                           <h1 class="mt-2 mb-0" id="total_new_reg">0</h1>
                       </div>
                   </div>

                   <div class="col-md-6 mb-4">
                       <div class="card text-center text-success p-3">
                           <h6> Queries</h6>
                           <h1 class="mt-2 mb-0" id="total_queries">0</h1>
                       </div>
                   </div
               </div>

               <h5>Users</h5>

               <div class="row mb-4">
                   <div class="col-md-4 mb-4">
                           <div class="card text-center text-success p-3">
                               <h6>Total Users</h6>
                               <h1 class="mt-2 mb-0"><?php echo $current_users['total']?></h1>
                           </div>
                   </div>
                   <div class="col-md-4 mb-4">
                           <div class="card text-center text-info p-3">
                               <h6>Active Users</h6>
                               <h1 class="mt-2 mb-0"><?php echo $current_users['active']?></h1>
                           </div>
                   </div>
                   <div class="col-md-4 mb-4">
                           <div class="card text-center text-warning p-3">
                               <h6>Inactive Users</h6>
                               <h1 class="mt-2 mb-0"><?php echo $current_users['inactive']?></h1>
                           </div>
                   </div>
               </div>

            </div>
        </div>
    </div>



<?php require('inc/scripts.php');?>

    <script src="scripts/dashboard.js"></script>

</body>
</html>