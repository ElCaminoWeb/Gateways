<?php
if (!isset($_SESSION)) {
    session_start();
}
include './php/general.php';
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Refund</title>
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
                <li><a href="masspay.php">Mass Pay</a></li>
                <li class="current"><a href="refund.php">Refund</a></li>
            </ul>
            <ul class="nav navbar-nav pull-right">
                <li>
                    <a href="http://localhost/index.php"><span class="glyphicon glyphicon-home"></span></a>
                </li>
            </ul>

        </div>
        <div class="container">
            <div class="row">
                <div class="col-lg-3 sideMenu">
                    <div class="sidebar affix">
                        <ul class="nav sidenav" id="sideMenu">
                            <li class="active">
                                <a href="#config">Configuration</a>
                                <ul class="nav">
                                    <li class="active"><a href="#gateway">Gateway</a></li>
                                </ul>
                            </li>
                            <li id ="lnkTransaction">
                                <a href="#refund">Refund Details</a>
                                <ul class="nav">
                                    <li><a href="#transId">Transaction Id</a></li>
                                    <li><a href="#refType">Type</a></li>
                                    <li><a href="#refAmt">Amount</a></li>
                                    <li><a href="#refCur">Currency</a></li>
                                    <li><a href="#refOther">Other</a></li>
                                </ul>
                            </li>
                        </ul>
                        <br>
                        <button id="checkout" class="btn btn-link" onclick="submitForm()" type="button">
                            <img src="./img/PP_Buttons_CheckOut_195x37_v3.png" />
                        </button>
                    </div>
                </div>
                <div class="col-lg-9" id="main" style="padding-top: 10px;">
                    <form class="form-horizontal" id="form" action="./php/redirect.php" method="post">
                        <fieldset id="config">
                            <h3>Configuration <small>Choose the gateway you would like to test</small></h3>
                            <div class="form-group" id="GATEWAY">
                                <label class="col-lg-2 control-label">Gateway</label>
                                <div class="col-lg-9">
                                    <select class="form-control" name="GATEWAY">
                                        <option value="Standard">Standard</option>
                                    </select>
                                </div>
                                <div class="col-lg-1">
                                    <button id="infGATEWAY" name="moreInfo" class="btn btn-link" rel="popover">
                                        <span class="glyphicon glyphicon-info-sign"></span>
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset id="refund">
                            <h3>Refund Details <small>Specify the details about the refund you would like to perform</small></h3>
                            <section id="important"></section>                          
                            <section id="other"></section>
                        </fieldset>
                        <fieldset id="other"></fieldset>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="./js/jscolor/jscolor.js"></script>
        <script type="text/javascript" src="https://code.jquery.com/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="./js/bootstrap.js"></script>
        <script type="text/javascript" src="./js/edit.js"></script>
        <script type="text/javascript" src="./js/create.js"></script>
        <script type="text/javascript" src="./js/refund.js"></script>
        <?php
        loadSession($_SESSION);
        $_SESSION['STAGE'] = "RF_Start";
        ?>
    </body>
</html>
