<!DOCTYPE html>
<html>
<head>
	<title>Do Authorisation</title>
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link type="text/css" href="./css/prettify.css" rel="stylesheet" media="screen"/>
	<link type="text/css" href="./css/bootstrap.css" rel="stylesheet" media="screen"/>
	<link href="css/bootstrap-glyphicons.css" rel="stylesheet">
	<link type="text/css" href="./css/new_style.css" rel="stylesheet" />
</head>
<body data-spy="scroll" data-target=".sideMenu">
	<?php session_start();$_SESSION['STAGE'] = "Auth";  ?>
	<div class="navbar">
		<a class="navbar-brand" href="#">Gateway Portal</a>
		<ul class="nav navbar-nav">
			<li><a href="index.php">Express Checkout</a></li>
			<li class="active"><a href="auth.php">Do Authorisation</a></li>
		</ul>
	</div>
	<div class="container">
  		<div class="row">
	  		<div class="col-lg-3 sideMenu">
	  			<div class="sidebar affix">
		  			<ul class="nav sidenav">
		  				<li class="active">
		  				    <a href="#config">Configuration</a>
		  					<ul class="nav">
		  						<li class="active"><a href="#gateway">Gateway</a></li>
		  					</ul>
		  				</li>
		  				<li><a href="#authDetails">Authorisation Details</a></li>
		  				<li>
		  					<a href="#orderDetails">Order Details</a>
		  					<ul class="nav">
		  						<li><a href="#totals">Totals</a></li>
		  						<li><a href="#shipping">Shipping Details</a></li>
				  			</ul>
				  		</li>
			  		</ul>
			  		<br>
			  		<button id="checkout" class="btn btn-primary" onclick="submitForm()" type="button">
					        Do Authorisation
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
	  			<form class="form-horizontal" id="form" action="./php/SetExpressCheckout.php" method="post">
	  				<h3 id="config">Configuration <small>Choose the gateway you would like to test</small></h3>
  					<div class="form-group" id="GATEWAY">
						<label id="gateway" class="col-lg-2 control-label">Gateway</label>
   						<div class="col-lg-10">
							<select class="form-control" name="GATEWAY" onchange="update(this)">
								<option>Standard</option>
							    <option>DataCash</option>
							    <option>CyberSource (SO)</option>
							    <option>SagePay (API)</option>
							</select>
						</div>
					</div>
					<section id="cybersource" style="display:none;">
						<h4>CyberSource Specific Fields</h4>
						<div class="form-group" id="billTo_email">
							<label class="col-lg-2 control-label">Email</label>
							<div class="col-lg-10">
								<input class="form-control" type="text" name="billTo_email" onchange="update(this)" />
							</div>
						</div>
					</section>
					<hr><h3 id="authDetails">Authorisation Details <small>Specify the details about the authorisation</small></h3>
	  				<div class="form-group" id="TRANSACTIONID">
	  					<label class="col-lg-2 control-label">Transaction ID</label>
	  					<div class="col-lg-10">
		  					<input class="form-control" type="text" name="TRANSACTIONID" onchange="update(this)"/>
		  				</div>
	  				</div>
 						<div class="form-group" id="AMT">
 							<label class="col-lg-2 control-label">Amount</label>
 							<div class="col-lg-10">
  							<input class="form-control" type="text" name="AMT" onchange="update(this)"/>
  						</div>
 						</div>
 						<div class="form-group" id="CURRENCYCODE">
						<label class="col-lg-2 control-label">Currency</label>
						<div class="col-lg-10">
							<select class="form-control" name="CURRENCYCODE">
								<option value="GBP" selected="selected">GBP</option>
							    <option value="USD">USD</option>
							    <option value="EUR">EUR</option>
							    <option value="AUD">AUD</option>
							    <option value="BRL">BRL</option>
							    <option value="CAD">CAD</option>
							    <option value="CZK">CZK</option>
		                        <option value="DKK">DKK</option>
		                        <option value="HKD">HKD</option>
		                        <option value="HUF">HUF</option>
		                        <option value="ILS">ILS</option>
		                        <option value="JPY">JPY</option>
		                        <option value="MYR">MYR</option>
		                        <option value="MXN">MXN</option>
		                        <option value="NOK">NOK</option>
		                        <option value="NZD">NZD</option>
		                        <option value="PHP">PHP</option>
		                        <option value="PLN">PLN</option>
		                        <option value="SGD">SGD</option>
		                        <option value="SEK">SEK</option>
		                        <option value="CHF">CHF</option>
		                        <option value="TWD">TWD</option>
		                        <option value="THB">THB</option>
		                        <option value="TRY">TRY</option>
	                     	</select>
	                     </div>
                    </div>
                    <hr><h3 id="orderDetails">Order Details <small>List the details of the original order</small></h3>
                    <h4 id="totals">Totals</h4>
 					<div class="form-group" id="ITEMAMT">
 						<label class="col-lg-2 control-label">Item Amount</label>
 						<div class="col-lg-10">
  							<input class="form-control" type="text" name="ITEMAMT" onchange="update(this)"/>
  						</div>
 					</div>					                    
					<div class="form-group" id="SHIPPINGAMT">
						<label class="col-lg-2 control-label">Shipping Amount</label>
						<div class="col-lg-10">
							<input class="form-control" type="text" name="SHIPPINGAMT" onchange="update(this)" />
						</div>
					</div>
					<div class="form-group" id="HANDLINGAMT">
						<label class="col-lg-2 control-label">Handling Amount</label>
						<div class="col-lg-10">
							<input class="form-control" type="text" name="HANDLINGAMT" onchange="update(this)" />
						</div>
					</div>
					<div class="form-group" id="TAXAMT">
						<label class="col-lg-2 control-label">Tax Amount</label>
						<div class="col-lg-10">
							<input class="form-control" type="text" name="TAXAMT" onchange="update(this)" />
						</div>
					</div>
					<div class="form-group" id="INSURANCEAMT">
						<label class="col-lg-2 control-label">Insurance Amount</label>
						<div class="col-lg-10">
							<input class="form-control" type="text" name="INSURANCEAMT" onchange="update(this)" />
						</div>
					</div>
					<div class="form-group" id="SHIPDISCAMT">
						<label class="col-lg-2 control-label">Shipping Discount</label>
						<div class="col-lg-10">
							<input class="form-control" type="text" name="SHIPDISCAMT" onchange="update(this)" />
						</div>
					</div><hr>
					<h4 id="shipping">Shipping Details</h4>
					<div class="form-group" id="SHIPTONAME">
						<label class="col-lg-2 control-label">Name</label>
						<div class="col-lg-10">
							<input class="form-control" type="text" name="SHIPTONAME" onchange="update(this)" />
						</div>
					</div>
					<div class="form-group" id="SHIPTOSTREET">
						<label class="col-lg-2 control-label">Street 1</label>
						<div class="col-lg-10">
							<input class="form-control" type="text" name="SHIPTOSTREET" onchange="update(this)" />
						</div>
					</div>
					<div class="form-group" id="SHIPTOSTREET2">
						<label class="col-lg-2 control-label">Street 2</label>
						<div class="col-lg-10">
							<input class="form-control" type="text" name="SHIPTOSTREET2" onchange="update(this)" />
						</div>
					</div>
					<div class="form-group" id="SHIPTOCITY">
						<label class="col-lg-2 control-label">City</label>
						<div class="col-lg-10">
							<input class="form-control" type="text" name="SHIPTOCITY" onchange="update(this)" />
						</div>
					</div>
					<div class="form-group" id="SHIPTOSTATE">
						<label class="col-lg-2 control-label">State</label>
						<div class="col-lg-10">
							<input class="form-control" type="text" name="SHIPTOSTATE" onchange="update(this)" />
						</div>
					</div>
					<div class="form-group" id="SHIPTOZIP">
						<label class="col-lg-2 control-label">Zip</label>
						<div class="col-lg-10">
							<input class="form-control" type="text" name="SHIPTOZIP" onchange="update(this)" />
						</div>
					</div>
					<div class="form-group" id="SHIPTOCOUNTRY">
						<label class="col-lg-2 control-label">Country</label>
						<div class="col-lg-10">
							<input class="form-control" type="text" name="SHIPTOCOUNTRY" onchange="update(this)" />
						</div>
					</div>
					<div class="form-group" id="SHIPTOPHONENUM">
						<label class="col-lg-2 control-label">Phone Number</label>
						<div class="col-lg-10">
							<input class="form-control" type="text" name="SHIPTOPHONENUM" onchange="update(this)" />
						</div>
					</div>
					<hr>
					<button class="btn btn-large btn-primary" onclick="submitForm()" type="button">
				        Do Authorisation
				    </button>
  				</form>
  			</div>
  		</div>
 	</div>
  	<script type="text/javascript" src="./js/jscolor/jscolor.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.js"></script>
  	<script type="text/javascript" src="./js/bootstrap.js"></script>
  	<script type="text/javascript" src="./js/main.js"></script>
</body>
</html>
