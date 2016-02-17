<?php
session_start();

if (isset($_GET['name']) && isset($_SESSION['userid'])):
    require_once("../dbcon.php");
    $name = cleanInput($_GET['name']);

    $getRooms = "SELECT *
  			           FROM chat_rooms
  		             WHERE name = '$name'";

    $roomResults = mysql_query($getRooms);

    if (mysql_num_rows($roomResults) < 1) {
        header("Location: ../chatrooms.php");
        die();
    }

    while ($rooms = mysql_fetch_array($roomResults)) {
        $file = $rooms['file'];
    }

    ?>
    <?php include_once('../common/header.php'); ?>
    <script type="text/javascript" src="/chat/Test/room/chat.js"></script>
    <script type="text/javascript">
        var chat = new Chat('<?php echo $file; ?>');
        chat.init();
        chat.getUsers(<?php echo "'" . $name . "','" . $_SESSION['userid'] . "'"; ?>);
        var name = '<?php echo $_SESSION['userid'];?>';
    </script>
    <script type="text/javascript" src="settings.js"></script>


    <div id="room" class="container z-depth-1">

        <div class="fill cyan lighten-4 z-depth-1">
            <div class="container"><h2 class="light white-text"><?php echo $name; ?></h2>
                <p class="white-text">The Salty Farmers recommends 8 glasses of water a day. Probably more.</p>
            </div>
        </div>
        <div id="room-container" class="row">

            <div class="row">
                <div class="row">
                    <div id="user-list-wrap" class="col s12 m4 right">
                        <div id="userlist"></div>
                    </div>
                    <div id="chat-wrap" class="col s12 m8">
                        <div id="chat-area"></div>
                    </div>
                </div>
                <div class="padd"></div>
                <form id="send-message-area" action="">
                    <div class="row">
                        <div class="container">
                            <div class="col s3">
                                <div id="username" class="chip truncate">
                                    <?php echo $_SESSION['userid']; ?>
                                </div>
                            </div>
                            <div class="input-field col s9 right">
                            <textarea id="sendie" maxlength='100' class="materialize-textarea"
                                      placeholder="Send a message!"></textarea>
                                <div class="chip right">Enter to send</div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
    <div class="fullbackground" id="chatbg"></div>
    <script type="text/javascript">
        $(document).ready(function () {
            setTimeout(function () {
                $('#chat-area p span').not(".test").each(function () {
                    if ($(this).text() === usernameid) {
                        $(this).addClass("active");
                        $(this).parent().addClass("active");
                    }
                });;
            }, 200);
        });
    </script>
    <?php include_once("../common/footer.php"); ?>
    </div>
    </body>

    </html>

    <?php
endif;
?>
