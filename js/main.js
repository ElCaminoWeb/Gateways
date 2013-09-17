// JScript source code

//include ('./js/edit.js');
//include ('./js/create.js');


var numberOfSellers = 1;
var numberOfItems = new Array();
var notSupported = new Object();
notSupported["Standard"] = [];
notSupported["DataCash"] = ["DEVICE", "PAYMENTREQUEST_0_PAYMENTACTION", "LANDINGPAGE", "L_PAYMENTREQUEST_0_DESC0"];
notSupported["CyberSource"] = ["SOLUTIONTYPE"];
createSeller(0);
//createSeller(1);
function include(jsFilePath) {
    var js = document.createElement("script");
    js.type = "text/javascript";
    js.src = jsFilePath;

    document.body.appendChild(js);
}

function noItems(none) {
    var itemTotal = document.getElementById("PAYMENTREQUEST_0_ITEMAMT");
    var newClass = (none) ? "form-group":"hide";
    itemTotal.setAttribute("class",newClass);
}

function overrideAddress(override) {
    var val = (override) ? "1" : "0";
    document.getElementsByName("ADDROVERRIDE")[0].value = val;
}

function submitForm() {
    //checkEmpty();
    document.getElementById("form").submit();
}

function loadSession() {
    var inputs = document.getElementsByTagName('input');
    for (var inc = 0; inc < inputs.length; inc++) {
        var input = inputs[inc];
        var name = input.getAttribute("name");
        document.write("<?php displaySessionValue(" + name + ") ?>") ;
    }
}

function reset() {
    var inputs = document.getElementsByTagName("*");
    for (var inc = 0; inc < inputs.length; inc++) {
        var param = inputs[inc];
        if (param.hasAttribute("disabled")) {
            param.removeAttribute("disabled");
        }
    }
}

function supported(gateway) {
    reset();
    var params = notSupported[gateway];
    for (var inc = 0; inc < params.length; inc++) {
        var param = document.getElementsByName(params[inc])[0];
        param.setAttribute("value", "");
        param.setAttribute("disabled", "disabled");
    }
}

var defaults = new Array();


