<?php
if (!isset($_SESSION)) {
    session_start();
}
include './php/general.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Mass Pay</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link type="text/css" href="https://www.paypal-communications.com/App_Themes/css/styles.css" rel="stylesheet" media="screen"/>
        <link type="text/css" href="./css/bootstrap.css" rel="stylesheet" media="screen"/>
        <link href="css/bootstrap-glyphicons.css" rel="stylesheet">
        <link type="text/css" href="./css/new_style.css" rel="stylesheet" />
        
    </head>
    <body data-offset="61" data-spy="scroll" data-target="#sideMenu">
        <div class="navbar">
            <a class="navbar-brand" href="#"><img src="./img/logo.png" height="30px"/></a>
            <ul class="nav navbar-nav">
                <li><a href="index.php">Express Checkout</a></li>
                <li><a href="auth.php">Do Authorisation</a></li>
                <li class="current"><a href="masspay.php">Mass Pay</a></li>
                <li><a href="refund.php">Refund</a></li>
            </ul>
            <ul class="nav navbar-nav pull-right">
                <li>
                    <a href="http://localhost/index.php"><span class="glyphicon glyphicon-home"></span></a>
                </li>
            </ul>

        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-3 sideMenu" id="toc"></div>
                <div class="col-lg-9" id="main" style="padding-top: 10px;">
                    <div class="row" id="error">
                        <div id="errorBox" class="alert alert-error" style="display:none"></div>
                    </div>
                    <div class="row" id="warning">
                        <div id="warningBox" class="alert" style="display:none"></div>
                    </div>
                    <form class="form-horizontal" id="form" action="./php/redirect.php" method="post">
                        <fieldset>
                            <h3 id="config">Configuration <small>Choose the gateway and device you would like to test</small></h3>
                            <div class="form-group" id="GATEWAY">
                                <label class="col-lg-2 control-label">Gateway</label>
                                <div class="col-lg-9">
                                    <select class="form-control" name="GATEWAY">
                                        <option value="Standard">Standard</option>
                                        <option value="DataCash">DataCash</option>
                                    </select>
                                </div>
                                <div class="col-lg-1">
                                    <button id="infGATEWAY" name="moreInfo" class="btn btn-link" rel="popover">
                                        <span class="glyphicon glyphicon-info-sign"></span>
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset>
                            <h3 id="general">General <small>Specify the options applicable to all payments</small></h3>
                        </fieldset>
                        <fieldset>
                            <h3 id="mpitems">Mass Pay Items <small>Specify the options applicable to individual payments</small></h3>
                            <section id="items"></section>
                            <h4><button class="btn btn-primary add" type="button" onclick="createMPItem('EmailAddress')">
                                    Mass Pay Item 
                                    <span class="glyphicon glyphicon-plus"></span>
                                </button>
                            </h4>

                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="https://code.jquery.com/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="./js/main.js"></script>
        <script type="text/javascript" src="./js/masspay.js"></script>
        <?php
        loadSession($_SESSION);
        $_SESSION['STAGE'] = "MP_Start";
        ?>
    </body>
</html>
