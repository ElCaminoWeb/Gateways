// JScript source code

include ('./js/bootstrap.js');
include ('./js/edit.js');
include ('./js/create.js');
include ('./js/sideMenu.js');

function include(jsFilePath) {
    var js = document.createElement("script");
    js.type = "text/javascript";
    js.src = jsFilePath;

    document.body.appendChild(js);
}

function urldecode(str) {
    return decodeURIComponent((str + '').replace(/\+/g, '%20'));
}

function submitForm() {
    //checkEmpty();
    var sltGateway = document.getElementsByName("GATEWAY")[0];
    var gateway = sltGateway.options[sltGateway.options.selectedIndex].getAttribute("value");
    //alert(gateway);
    if (gateway == "SagePay (API)") {
        //document.getElementById("form").setAttribute("action","https://test.sagepay.com/Simulator/VSPFormGateway.asp");
    }
    document.getElementById("form").submit();
}

function reset() {
    var inputs = document.getElementsByTagName("*");
    for (var inc = 0; inc < inputs.length; inc++) {
        var param = inputs[inc];
        if (param.hasAttribute("disabled")) {
            param.removeAttribute("disabled");
        }
    }
    loadSession();
}

/*function supported(gateway) {
    reset();
    var params = notSupported[gateway];
    for (var inc = 0; inc < params.length; inc++) {
        var param = document.getElementsByName(params[inc])[0];
        param.setAttribute("value", "");
        param.setAttribute("disabled", "disabled");
    }
}*/