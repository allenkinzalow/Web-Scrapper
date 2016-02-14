<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php if($name): ?>
        <title>Welcome to: <?php echo $name; ?></title>
    <?php else: ?>
        <title>The Salty Farmers</title>
    <?php endif;?>

    <script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js" type="text/javascript"></script>
    <link rel="stylesheet" type="text/css" href="/chat/Test/main1.css"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/0.97.5/js/materialize.min.js"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons"
          rel="stylesheet">

</head>

<body>
<div id="wrapper">
<div id="header">
    <nav>
        <div class="nav-wrapper blue-grey">
            <a href="/chat/Test" class="brand-logo center">The Salty Chat</a>
            <ul id="nav-mobile" class="left">
                <li><a href="http://kinztech.com/chat/Test/about/"><i class="material-icons">face</i></a></li>
            </ul>
        </div>
    </nav>
</div>
<div class="padd"></div>