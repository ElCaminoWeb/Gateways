<!DOCTYPE html>
<html>
<head>
  	<title>Response</title>
  	<?php
  	    session_start();
  	    $gateways = unserialize($_SESSION['GATEWAYS']);
  	    include './php/' .  $gateways[$_SESSION['GATEWAY']];
  	?>
  	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link type="text/css" href="./css/prettify.css" rel="stylesheet" media="screen"/>
	<link type="text/css" href="./css/bootstrap.css" rel="stylesheet" media="screen"/>
	<link href="css/bootstrap-glyphicons.css" rel="stylesheet">
	<link type="text/css" href="./css/new_style.css" rel="stylesheet" />
</head>
<body data-spy="scroll" data-target=".sideMenu">
	<div class="navbar">
		<a class="navbar-brand" href="#">Gateway Portal</a>
		<ul class="nav navbar-nav">
			<li><a href="index.php">Express Checkout</a></li>
			<li><a href="auth.php">Do Authorisation</a></li>
		</ul>
	</div>
	<div class="container">
		<div class="row">
		  	<div class="col-lg-5">
		  		<h3><span class="label pull-right"> <?php  echo $_SESSION['GATEWAY'];  ?></span>Gateway: </h3>
		  		<h3>Response Type: <?php
		            switch($_SESSION['ACK']) {
		                case "Success":
		                    echo '<span class="label label-success pull-right">Success</span>';
		                    break;
		                case "SuccessWithWarning":
		                    echo '<span class="label label-warning pull-right">Success with warning</span>';
		                    break;
		                case "Warning":
		                    echo '<span class="label label-warning pull-right">Warning</span>';
		                    break;
		                case "Failure":
		                    echo '<span class="label label-danger pull-right">Failure</span>';
		                    break;
		                } 
		        ?></h3>
		  		<h3>Method Called: <span class="label label-info pull-right"><?php echo $_SESSION['METHOD'] ?></span></h3>
		  	</div>
		  	<div class="col-lg-7">
		    	<div class="col-lg-12" style="padding-top: 55px;">
		    		<div class="col-lg-4 col-offset-8">
		    			<button class="btn btn-danger" onclick="cancel()" type="button">Cancel <span class="glyphicon glyphicon-remove-circle"></span></button>
		    		</div> 
		    	</div>
		    	<div class="col-lg-12" style="padding-top: 10px;">
		    		<div class="col-lg-4 col-offset-8">
		    			<button class="btn btn-primary" onclick="submit()" type="button">Continue <span class="glyphicon glyphicon-ok-circle"></span></button>
		    		</div> 
		   		</div>
		  	</div>
		</div>
		<hr>
		<div class="row">
			<div class="col-lg-12">
		  		<h3>Request</h3>
		    	<pre class="prettyprint" id="request">
		    		<?php echo "\n" . decode($_SESSION['REQUEST']) . "\n"; ?>
		    	</pre>
		    </div>
		 	<div class="col-lg-12">
		  		<h3>Response</h3>
		    	<pre class="prettyprint" id="response">
		    		<?php echo"\n" . decode($_SESSION['RESPONSE']) . "\n";?>
		    	</pre>
		    </div>
		</div>
  	</div>
  	<script>
		function submit() { window.location = "http://localhost/Gateways/php/redirect.php"; }
  	</script>
  	<script type="text/javascript" src="./js/jscolor/jscolor.js"></script>
	<script type="text/javascript" src="http://code.jquery.com/jquery-1.8.3.js"></script>
  	<script type="text/javascript" src="./js/bootstrap.js"></script>
  	<script type="text/javascript" src="./js/main.js"></script>
  	<script type="text/javascript" src="./js/prettify.js"></script>
  	<script src="https://google-code-prettify.googlecode.com/svn/loader/run_prettify.js"></script>
</body>
</html>

