<?php
session_start();

if (!isset($_SESSION['userid'])):
    include_once('common/header.php');
    ?>
    <div class="row">
        <div class="container">
            <div class="col sm12 m8 offset-m2">
                <div class="card" id="register">
                    <div class="card-image waves-effect waves-block waves-light">
                        <img class="activator" src="images/home-card.jpg">
                    </div>
                    <div class="card-content">
                        <span class="card-title activator grey-text text-darken-4">Get Registered<i
                                class="material-icons right">more_vert</i></span>
                    </div>
                    <div class="card-reveal">
                        <span class="card-title grey-text text-darken-4">Register a username<i
                                class="material-icons right">close</i></span>
                        <p class="center">User names stay active for two hours!</p>
                        <div class="container center">
                            <div class="padd"></div>
                            <form method="post" action="jumpin.php">
                                <label>Desired Username:</label>
                                <div class="row">
                                    <div class="input-field col s12">
                                        <input type="text" id="userid" name="userid"/>
                                    </div>
                                    <input type="submit" value="Check" id="jumpin"
                                           class="waves-effect waves-light btn"/>
                                </div>
                                <div id="status">
                                    <?php if (isset($_GET['error'])): ?>
                                        <!-- Display error when returning with error URL param? -->
                                    <?php endif; ?>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    </div>
    <?php include_once("common/footer.php"); ?>
    </div>
    </body>
    </html>

    <?php
else:
    require_once("chatrooms.php");
endif;
?>

