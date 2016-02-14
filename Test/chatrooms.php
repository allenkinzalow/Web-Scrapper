<?php

session_start();

require_once("dbcon.php");

if (checkVar($_SESSION['userid'])):

    $getRooms = "SELECT *
        			 FROM chat_rooms";
    $roomResults = mysql_query($getRooms);
    include_once('common/header.php');

    ?>

    <div id="chatroom-container" class="container">
        <div id="chatrooms" class="white z-depth-1">
            <div id="rooms">
                <div class="row">
                <div class="col s12 m8">
                    <h3 class="light cyan-text text-darken-2">Active Chat Rooms</h3>
                    </div>

                <div id="you" class="col s12 m4 right">
                    <div class="light cyan-text text-darken-2">Logged in as: <?php echo $_SESSION['userid'] ?></div>
                    </div>
                    </div>
                <p>Warning: High Sodium Content</p>
                <ul class="collection">
                    <?php
                    while ($rooms = mysql_fetch_array($roomResults)):
                        $room = $rooms['name'];
                        $query = mysql_query("SELECT * FROM `chat_users_rooms` WHERE `room` = '$room' ") or die("Cannot find data" . mysql_error());
                        $numOfUsers = mysql_num_rows($query);
                        ?>
                        <li class="collection-item">
                            <a href="room/?name=<?php echo $rooms['name'] ?>"><?php echo $rooms['name'] . "<span>Users chatting: <strong>" . $numOfUsers . "</strong></span>" ?></a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </div>
        <div class="fullbackground" id="lobbybg"></div>
    </div>
    <?php include_once("common/footer.php");?>
    </div>
    </body>

    </html>

    <?php

else:
    header('Location: http://css-tricks.com/examples/Chat2/');

endif;

?>