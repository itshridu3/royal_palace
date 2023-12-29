<?php

    require('../admin/inc/db_config.php');
    require('../admin/inc/essentials.php');
    date_default_timezone_set("Asia/Dhaka");
    session_start();

    if (isset($_GET['fetch_rooms']))
    {


        $chk_avail = json_decode($_GET['chk_avail'],true);

        if ($chk_avail['checkin']!='' && $chk_avail['checkout']!=''){
            $today_date = new DateTime(date("Y-m-d"));
            $checkin_date = new DateTime($chk_avail['checkin']);
            $checkout_date = new DateTime($chk_avail['checkout']);

            if ($checkin_date == $checkout_date){
                echo "<h3 class='text-center text-danger'>Invalid Date!</h3>";
                exit();
            }
            else if ($checkout_date < $checkin_date){
                echo "<h3 class='text-center text-danger'>Invalid Date!</h3>";
                exit();
            }
        }

        $guests = json_decode($_GET['guests'],true);
        $adults = ($guests['adults']!='')? $guests['adults'] :0;
        $children = ($guests['children']!='')? $guests['children'] :0;

        $count_rooms = 0;
        $output = "";

        $setting_q = "SELECT * FROM `settings` WHERE `sr_no`=1";

        $setting_q = mysqli_fetch_assoc(mysqli_query($con,$setting_q));

        $room_res = select("SELECT * FROM `rooms` WHERE `adult`>? AND `children`>? AND `status`=? AND `removed`=? ORDER BY `id` DESC", [$adults,$children,1, 0], 'iiii');

        while ($room_data = mysqli_fetch_assoc($room_res))
        {

//            if ($chk_avail['checkin']!='' && $chk_avail['checkout']!='')
//            {
//                $tb_query = "SELECT COUNT(*) AS `total_bookings` FROM `bookings_order` AND check_out>? AND check_in<?";
//
//                $values = ['booked', $room_data['id'],$chk_avail['checkin'],$chk_avail['checkout']];
//                $tb_fetch = mysqli_fetch_assoc(select($tb_query,$values,'siss'));
//
//                if (($room_data['quantity']-$tb_fetch['total_bookings'])==0){
//                    continue;
//                }
//
//            }

            //get features of room

            $fea_q = mysqli_query($con, "SELECT f.name FROM `features` f 
                                INNER JOIN `room_features` rfea ON f.id = rfea.features_id 
                                WHERE rfea.room_id = '$room_data[id]'ORDER BY `id`DESC LIMIT 5");

            $features_data = "";
            while ($fea_row = mysqli_fetch_assoc($fea_q)) {
                $features_data .= "<span class='badge bg-light text-dark text-warp me-1 mb-1'>
                                $fea_row[name]
                                </span>";

            }

            //get facilities of room
            $fac_q = mysqli_query($con, "SELECT f.name FROM `facilities` f 
                                INNER JOIN `room_facilities` rfac ON f.id = rfac.facilities_id 
                                WHERE rfac.room_id = '$room_data[id]' ORDER BY `id`DESC LIMIT 5");

            $facilities_data = "";

            while ($fac_row = mysqli_fetch_assoc($fac_q)) {
                $facilities_data .= "<span class='badge bg-light text-dark text-warp me-1 mb-1'>
                                $fac_row[name]
                                </span>";

            }

            //get thumbnail of image

            $room_thumb = ROOMS_IMG_PATH . "thumbnail.jpg";
            $thumb_q = mysqli_query($con, "SELECT * FROM `room_images` 
                            WHERE `room_id`='$room_data[id]' 
                            AND `thumb`='1'");


            if (mysqli_num_rows($thumb_q) > 0) {
                $thumb_res = mysqli_fetch_assoc($thumb_q);
                $room_thumb = ROOMS_IMG_PATH . $thumb_res['image'];
            }


            $book_btn = "";
            global $setting_q;
            $login = 0;

            if (!$setting_q['shutdown']) {
                if (isset($_SESSION['login']) && $_SESSION['login']) {
                    $login = 1;
                }
                $book_btn = "<button onclick='checkLoginToBook($login,$room_data[id])' class='btn btn-sm text-white bg-2 w-100 shadow-none mb-2'>Book Now</button>";
            }

            // print room card

            $output.= "
                <div class='card mb-4 border-0 shadow'>
                    <div class='row g-0 p-3 align-items-center'>
                        <div class='col-md-5 mb-lg-0 mb-lg-0 mb-3'>
                            <img src='$room_thumb' class='img-fluid rounded' alt='...'>
                            
                        </div>
                
                        <div class='col-md-5 px-lg-3 px-md-3 px-0'>
                        <h5 class=''>$room_data[name]</h5>
                
                        <div class='features mb-3'>
                            <p class='mb-1'>Room Features</p>
                            $features_data
                        </div>
                
                        <div class='facilities mb-3'>
                            <p class='mb-1'>Room Facilities</p>
                            $facilities_data
                            </div>
                
                        <div class='guests'>
                            <p class='mb-1'>Guests</p>
                            <span class='badge bg-light text-dark text-warp'>
                                $room_data[adult] Adult
                            </span>
                            <span class='badge bg-light text-dark text-warp'>
                                $room_data[children] Children
                            </span>
                        </div>
                        </div>
                
                        <div class='col-md-2 text-center'>
                            <h6 class='mb-4'>৳$room_data[price] Every Night</h6>
                
                            $book_btn
                            <a href='room_details.php?id=$room_data[id]' class='btn btn-sm w-100 text-white bg-3 shadow-none'>More Details</a>
                           
                        </div>
                    </div>
                </div>
            ";

            $count_rooms++;
        }
    if ($count_rooms>0){
        echo $output;
    }else{
        echo "<h3 class='text-center text-danger'>No Rooms Available!</h3>";
    }
    }

?>