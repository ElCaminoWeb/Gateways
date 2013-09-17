<!DOCTYPE html>
<html>
    <head>
        <title>Express Checkout</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link type="text/css" href="./css/prettify.css" rel="stylesheet" media="screen"/>
        <link type="text/css" href="./css/bootstrap.css" rel="stylesheet" media="screen"/>
        <link href="css/bootstrap-glyphicons.css" rel="stylesheet">
        <link type="text/css" href="./css/new_style.css" rel="stylesheet" />
    </head>
    <body data-offset="50" data-spy="scroll" data-target="#nav">
        <?php
            session_start();
            $_SESSION['STAGE'] = "EC_Start";
            include './php/general.php';
        ?>
        <div class="navbar">
            <a class="navbar-brand" href="#">Gateway Portal</a>
            <ul class="nav navbar-nav">
                <li class="active"><a href="index.php">Express Checkout</a></li>
                <li><a href="auth.php">Do Authorisation</a></li>
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
                                    <li><a href="#device">Device</a></li>
                                </ul>
                            </li>
                            <li id ="lnkcustom">
                                <a href="#customisation">Customisation</a>
                                <ul class="nav">
                                    <li><a href="#paypal_pages">PayPal Pages</a></li>
                                    <li><a href="#URLs">Return / Cancel URLs</a></li>
                                    <li><a href="#other">Other Features</a></li>
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
                    <div class="row" id="error">
                        <div id="errorBox" class="alert alert-error" style="display:none"></div>
                    </div>
                    <div class="row" id="warning">
                        <div id="warningBox" class="alert" style="display:none"></div>
                    </div>
                    <form class="form-horizontal" id="form" action="./php/redirect.php" method="post">
                        <fieldset id="config">
                            <h3>Configuration <small>Choose the gateway and device you would like to test</small></h3>
                            <div class="form-group" id="GATEWAY">
                                <label id="gateway" class="col-lg-2 control-label">Gateway</label>
                                <div class="col-lg-10">
                                    <select class="form-control" name="GATEWAY" onchange="update(this)">
                                        <option>Standard</option>
                                        <option>DataCash</option>
                                        <option>CyberSource</option>
                                        <option>SagePay (API)</option>
                                        <option>Authipay (API)</option>
                                        <option>Authipay (HSS)</option>
                                        <option>Authipay (VT)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="DEVICE">
                                <label class="col-lg-2 control-label" id="device">Device</label>
                                <div class="col-lg-10">
                                    <select class="form-control" name="DEVICE" onchange="update(this)">
                                        <option>Desktop</option>
                                        <option>Mobile</option>
                                    </select>
                                </div>
                            </div>
                        </fieldset>
                        <fieldset id="sellers"></fieldset>
                        <fieldset id="customise">
                            <h3 id="customisation">Customisation <small>Specify the unique customisation for this order</small></h3>
                            <h4 id="paypal_pages">PayPal Pages</h4>
                            <div class="form-group">
                                <div class="btn-group col-lg-2">
                                    <button type="button" class="btn btn-default" id="logo">Logo</button>
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                        <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a onclick="updateLogo(this)" id="LOGOIMG">Logo</a></li>
                                        <li><a onclick="updateLogo(this)"id="HDRIMG">Header Image</a></li>
                                    </ul>
                                </div>
                                <div class="col-lg-10">
                                    <input class="form-control" type="text" name="LOGOIMG" value="https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg" onchange="update(this)"/>
                                </div>
                            </div>
                            <div class="form-group" id="CARTBORDERCOLOR">
                                <label class="col-lg-2 control-label">Border colour</label>
                                <div class="col-lg-10">
                                    <input class="form-control color" type="text" class="color" name="CARTBORDERCOLOR" value="00457C" />
                                </div>
                            </div>
                            <div class="form-group" id="BRANDNAME">
                                <label class="col-lg-2 control-label">Brand name</label>
                                <div class="col-lg-10">
                                    <input class="form-control" type="text" name="BRANDNAME" value="Gateway Test" />
                                </div>
                            </div>
                            <div class="form-group" id="SOLUTIONTYPE">
                                <label class="col-lg-2 control-label">Solution Type</label>
                                <div class="col-lg-10">
                                    <select class="form-control" name="SOLUTIONTYPE">
                                        <option value="Sole">Sole</option>
                                        <option value="Mark">Mark</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="COMMIT">
                                <label class="col-lg-2 control-label">User Action</label>
                                <div class="col-lg-10">
                                    <select class="form-control" name="COMMIT">
                                        <option value="No" >Continue</option>
                                        <option value="Yes">Commit</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="LANDINGPAGE">
                                <label class="col-lg-2 control-label">Landing Page</label>
                                <div class="col-lg-10">
                                    <select class="form-control" name="LANDINGPAGE">
                                        <option value="Login" selected="selected">Login</option>
                                        <option value="Billing">Billing</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="LOCALECODE">
                                <label class="col-lg-2 control-label">Locale</label>
                                <div class="col-lg-10">
                                    <select class="form-control" name="PAYMENTREQUEST_0_LOCALECODE">
                                        <option value="GB" selected="selected">GB</option>
                                        <option value="DE">DE</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="NOSHIPPING">
                                <label class="col-lg-2 control-label">No shipping</label>
                                <div class="col-lg-10">
                                    <input type="checkbox" name="NOSHIPPING" id="NOSHIPPING" onchange="updateShippping(this.checked)" value="Yes">
                                </div>
                            </div>
                            <div class="form-group" id="ALLOWNOTE">
                                <label class="col-lg-2 control-label">Allow Note</label>
                                <div class="col-lg-10">
                                    <input type="checkbox" name="ALLOWNOTE" value="Yes" />
                                </div>
                            </div>
                            <input type="hidden" name="ADDROVERRIDE" />
                            <h4 id="addAddress">
                                <button class="btn btn-primary add" type="button" onclick="addAddress()">
                                    Override Address 
                                    <span class="glyphicon glyphicon-pencil"/>
                                </button>
                            </h4>
                            
                            </section>
                            <hr width="80%">
                            <h4 id="URLs">Return / Cancel URLs</h4>
                            <div class="form-group" id="RETURNURL">
                                <label class="col-lg-2 control-label">Return URL</label>
                                <div class="col-lg-10">
                                    <input class="form-control" type="text" name="RETURNURL" value="https://localhost/Gateways/php/redirect.php" onchange="update(this)"/>
                                </div>
                            </div>
                            <div class="form-group" id="CANCELURL">
                                <label class="col-lg-2 control-label">Cancel URL</label>
                                <div class="col-lg-10">
                                    <input class="form-control" type="text" name="CANCELURL" value="https://localhost/Gateways/index.php" onchange="update(this)"/>
                                </div>
                            </div>
                            <hr width="80%">
                            <h4 id="other">Other Features</h4>
                            <div class="form-group" id="MAXAMT">
                                <label class="col-lg-2 control-label">Max Amount</label>
                                <div class="col-lg-10">
                                    <input class="form-control" type="text" name="MAXAMT" value="" onchange="update(this)" />
                                </div>
                            </div>
                            <div class="form-group" id="CUSTOM">
                                <label class="col-lg-2 control-label">Custom</label>
                                <div class="col-lg-10">
                                    <input class="form-control" type="text" name="CUSTOM" placeholder="Custom" />
                                </div>
                            </div>
                            <div class="form-group" id="SETTLEMENT">
                                <label class="col-lg-2 control-label">Settlement</label>
                                <div class="col-lg-10">
                                    <select class="form-control" name="SETTLEMENT">
                                        <option value="1" selected="selected">Immediate</option>
                                        <option value="0">Deferred</option>
                                        <option value="multi">Deferred-Multi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" id="REQCONFIRMSHIPPING">
                                <label class="col-lg-2 control-label">Req Conf Ship</label>
                                <div class="col-lg-10">
                                    <input type="checkbox" class="paramCheckbox" name="REQCONFIRMSHIPPING" value="1" />
                                </div>
                            </div>
                            <div class="form-group" id="REQBILLINGADDRESS">
                                <label class="col-lg-2 control-label">Req Billing Addr</label>
                                <div class="col-lg-10">
                                    <input type="checkbox" class="paramCheckbox" name="REQBILLINGADDRESS" value="1" />
                                </div>
                            </div>
                        </fieldset>
                    </form>
                </div>
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
