<?php
//session_destroy();
if (!isset($_SESSION)) {
    session_start();
}
include './php/general.php';
//session_destroy();
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Gateway Portal</title>
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
                <li class="current"><a href="index.php">Express Checkout</a></li>
                <li><a href="auth.php">Do Authorisation</a></li>
                <li><a href="masspay.php">Mass Pay</a></li>
                <li><a href="refund.php">Refund</a></li>
            </ul>
            <ul class="nav navbar-nav pull-right">
                <li>
                    <a href="http://localhost/index.php"><span class="glyphicon glyphicon-home"></span></a>
                </li>
            </ul>
        </div>
        <div class="container" id="content">
            <div class="row">
                <div class="col-lg-3 sideMenu" id="toc"></div>
                <div class="col-lg-9" id="main" style="padding-top: 10px;">
                    <div class="row" id="warning">
                        <div id="warningBox" class="alert" style="display:none"></div>
                    </div>
                    <form class="form-horizontal" id="form" action="./php/redirect.php" method="post" enctype="multipart/form-data">
                        <fieldset>
                            <h3 id="config">Configuration <small>Choose the gateway that you would like to test</small></h3>
                            <div class="form-group" id="GATEWAY">
                                <label class="col-lg-2 control-label">Gateway</label>
                                <div class="col-lg-9">
                                    <select class="form-control" name="GATEWAY" onchange="update(this)">
                                        <option value="Standard">Standard</option>
                                        <option value="DataCash">DataCash</option>
                                        <option value="CyberSource">CyberSource</option>
                                        <option value="SagePay (API)">SagePay (API)</option>
                                        <option value="Authipay (API)">Authipay (API)</option>
                                        <option value="Authipay (HSS)">Authipay (HSS)</option>
                                    </select>
                                </div>
                                <div class="col-lg-1">
                                    <button id="infGATEWAY" name="moreInfo" class="btn btn-link" rel="popover">
                                        <span class="glyphicon glyphicon-info-sign"></span>
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset id="sellers"></fieldset>
                        <fieldset id="customise">
                            <h3 id="customisation">Customisation <small>Specify the unique customisation for this order</small></h3>
                            <h4 id="pp_pages">PayPal Pages</h4>
                            <section id="paypal_pages">
                                <div class="form-group">
                                    <div class="btn-group col-lg-2">
                                        <button type="button" class="btn btn-default" id="logo">Logo</button>
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                            <span class="caret"></span>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a onclick="updateLogo(this)" id="LOGOIMG">Logo</a></li>
                                            <li><a onclick="updateLogo(this)" id="HDRIMG">Header Image</a></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-9">
                                        <input class="form-control" type="text" name="LOGOIMG" value="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg" onchange="update(this)"/>
                                    </div>
                                    <div class="col-lg-1">
                                        <button id="infLOGOIMG" name="moreInfo" class="btn btn-link" rel="popover">
                                            <span class="glyphicon glyphicon-info-sign"></span>
                                        </button>
                                        <button id="infHDRIMG" name="moreInfo" class="btn btn-link hide" rel="popover">
                                            <span class="glyphicon glyphicon-info-sign"></span>
                                        </button>
                                    </div>
                                    <div class="col-lg-12">
                                        <h5>-OR-</h5>
                                    </div>
                                    <div class="col-lg-4 col-offset-4">
                                        <input type="file" name="logo" size="25" onchange="logoUploaded()" />
                                    </div>
                                </div>
                            </section>
                            <hr width="80%">
                            <h4 id="URLs">Return / Cancel URLs</h4>
                            <div class="form-group" id="RETURNURL">
                                <label class="col-lg-2 control-label">Return URL</label>
                                <div class="col-lg-9">
                                    <input class="form-control" type="text" name="RETURNURL" value="https://localhost/Gateways/php/redirect.php" onchange="update(this)"/>
                                </div>
                                <div class="col-lg-1">
                                    <button id="infRETURNURL" name="moreInfo" class="btn btn-link" rel="popover">
                                        <span class="glyphicon glyphicon-info-sign"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group" id="CANCELURL">
                                <label class="col-lg-2 control-label">Cancel URL</label>
                                <div class="col-lg-9">
                                    <input class="form-control" type="text" name="CANCELURL" value="https://localhost/Gateways/index.php" onchange="update(this)"/>
                                </div>
                                <div class="col-lg-1">
                                    <button id="infCANCELURL" name="moreInfo" class="btn btn-link" rel="popover">
                                        <span class="glyphicon glyphicon-info-sign"></span>
                                    </button>
                                </div>
                            </div>
                            <hr width="80%">
                            <h4 id="other">Other Features</h4>
                            <div class="form-group" id="MAXAMT">
                                <label class="col-lg-2 control-label">Max Amount</label>
                                <div class="col-lg-9">
                                    <input class="form-control" type="text" name="MAXAMT" value="" onchange="update(this)" />
                                </div>
                                <div class="col-lg-1">
                                    <button id="infMAXAMT" name="moreInfo" class="btn btn-link" rel="popover">
                                        <span class="glyphicon glyphicon-info-sign"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group" id="SETTLEMENT">
                                <label class="col-lg-2 control-label">Settlement</label>
                                <div class="col-lg-9">
                                    <select class="form-control" name="SETTLEMENT">
                                        <option value="1" selected="selected">Immediate</option>
                                        <option value="0">Deferred</option>
                                        <option value="multi">Deferred-Multi</option>
                                    </select>
                                </div>
                                <div class="col-lg-1">
                                    <button id="infSETTLEMENT" name="moreInfo" class="btn btn-link" rel="popover">
                                        <span class="glyphicon glyphicon-info-sign"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group" id="REQCONFIRMSHIPPING">
                                <label class="col-lg-2 control-label">Req Conf Ship</label>
                                <div class="col-lg-9">
                                    <input type="checkbox" class="paramCheckbox" name="REQCONFIRMSHIPPING" value="1" />
                                </div>
                                <div class="col-lg-1">
                                    <button id="infREQCONFIRMSHIPPING" name="moreInfo" class="btn btn-link" rel="popover">
                                        <span class="glyphicon glyphicon-info-sign"></span>
                                    </button>
                                </div>
                            </div>
                            <div class="form-group" id="REQBILLINGADDRESS">
                                <label class="col-lg-2 control-label">Req Billing Addr</label>
                                <div class="col-lg-9">
                                    <input type="checkbox" class="paramCheckbox" name="REQBILLINGADDRESS" value="1" />
                                </div>
                                <div class="col-lg-1">
                                    <button id="infREQBILLINGADDRESS" name="moreInfo" class="btn btn-link" rel="popover">
                                        <span class="glyphicon glyphicon-info-sign"></span>
                                    </button>
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
        <script type="text/javascript" src="https://code.jquery.com/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="./js/main.js"></script>
        <script type="text/javascript" src="./js/ec.js"></script>
        
        <?php
        loadSession($_SESSION);
        $_SESSION['STAGE'] = "EC_Start";
        ?>
    </body>
</html>
