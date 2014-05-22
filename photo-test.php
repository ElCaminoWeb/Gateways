<!DOCTYPE html>
<html>
    <head>
        <title>Gateway Portal</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link type="text/css" href="./css/prettify.css" rel="stylesheet" media="screen"/>
        <link type="text/css" href="./css/bootstrap.css" rel="stylesheet" media="screen"/>
        <link href="css/bootstrap-glyphicons.css" rel="stylesheet">
        <link type="text/css" href="./css/new_style.css" rel="stylesheet" />
    </head>
    <body data-offset="50" data-spy="scroll" data-target="#sideMenu">
        <div class="navbar">
            <a class="navbar-brand" href="#">Gateway Portal</a>
            <ul class="nav navbar-nav">
                <li class="active"><a href="index.php">Express Checkout</a></li>
                <li><a href="auth.php">Do Authorisation</a></li>
                <li><a href="masspay.php">Mass Pay</a></li>
            </ul>
            <a href="http://localhost/" class="navbar-text pull-right"><span class="glyphicon glyphicon-home"></span></a>
        </div>
        <div class="container" id="content">

            <form action="./php/accept-file.php" method="post" enctype="multipart/form-data">
                Upload a photo: <input type="file" name="photo" size="25" />
                <input type="submit" name="submit" value="Submit" />
            </form>

        </div>
    </div>
    <script type="text/javascript" src="./js/jscolor/jscolor.js"></script>
    <script type="text/javascript" src="https://code.jquery.com/jquery-1.8.3.js"></script>
    <script type="text/javascript" src="./js/bootstrap.js"></script>
    <script type="text/javascript" src="./js/edit.js"></script>
    <script type="text/javascript" src="./js/create.js"></script>
    <script type="text/javascript" src="./js/main.js"></script>
</body>
</html>
