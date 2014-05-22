// JScript source code
var notSupported = new Object();
notSupported["Standard"] = [];
notSupported["DataCash"] = ["DEVICE", "PAYMENTREQUEST_0_PAYMENTACTION", "LANDINGPAGE", "L_PAYMENTREQUEST_0_DESC0", "PARALLEL"];
notSupported["CyberSource"] = ["SOLUTIONTYPE", "ALLOWNOTE", ""];
//createSeller(0);
initialLoad();
//createSeller(1);


function initialLoad() {
    createImportant();
    createOther();
}

function createImportant() {
    var cont = document.getElementById("important");
    cont.appendChild(createSearch("TRANSACTIONID", "Transaction Id", "1234567890ABCDEFGHI", true, "transactionSearch()", "")); 
    
    var tranSearch = createFieldContainer("TRANSEARCH", "Transaction Details");
    var iframeCont = createInpContainer();
    var iframe = document.createElement("iframe");
    iframe.setAttribute("id","tranSearch");
    iframe.setAttribute("seamless","")
    iframeCont.appendChild(iframe);
    
    tranSearch.appendChild(iframeCont);
    
    cont.appendChild(tranSearch);
    
    
    var type = new Array();
    type[0] = {value: "Full", selected: true, disp: "Full refund (default)"};
    type[1] = {value: "Partial", selected: false, disp: "Partial refund"};
    type[2] = {value: "ExternalDispute", selected: false, disp: "External dispute. (Value available since version 82.0)"};
    type[3] = {value: "Other", selected: false, disp: "Other type of refund. (Value available since version 82.0)"};
    cont.appendChild(createSelect("REFUNDTYPE", "Refund Type", type, true, "infREFUNDTYPE"));
    
    cont.appendChild(createInput("AMT", "Refund Amount", "5.00", true, ""));
    
    var currency = new Array();
    currency[0] = {value: "AUD", selected: false, disp: "Australian Dollar"};
    currency[1] = {value: "BRL", selected: false, disp: "Brazilian Real"};
    currency[2] = {value: "CAD", selected: false, disp: "Canadian Dollar"};
    currency[3] = {value: "CZK", selected: false, disp: "Czech Koruna"};
    currency[4] = {value: "DKK", selected: false, disp: "Danish Krone"};
    currency[5] = {value: "EUR", selected: false, disp: "Euro"};
    currency[6] = {value: "HKD", selected: false, disp: "Hong Kong Dollar"};
    currency[7] = {value: "HUF", selected: false, disp: "Hungarian Forint"};
    currency[8] = {value: "ILS", selected: false, disp: "Israeli New Sheqel"};
    currency[9] = {value: "JPY", selected: false, disp: "Japanese Yen"};
    currency[10] = {value: "MYR", selected: false, disp: "Malaysian Ringgit"};
    currency[11] = {value: "MXN", selected: false, disp: "Mexican Peso"};
    currency[12] = {value: "NOK", selected: false, disp: "Norwegian Krone"};
    currency[13] = {value: "NZD", selected: false, disp: "New Zealand Dollar"};
    currency[14] = {value: "PHP", selected: false, disp: "Philippine Peso"};
    currency[15] = {value: "PLN", selected: false, disp: "Polish Zloty"};
    currency[16] = {value: "GBP", selected: true, disp: "Pound Sterling"};
    currency[17] = {value: "SGD", selected: false, disp: "Singapore Dollar"};
    currency[18] = {value: "SEK", selected: false, disp: "Swedish Krona"};
    currency[19] = {value: "CHF", selected: false, disp: "Swiss Franc"};
    currency[20] = {value: "TWD", selected: false, disp: "Taiwan New Dollar"};
    currency[21] = {value: "THB", selected: false, disp: "Thai Baht"};
    currency[22] = {value: "TRY", selected: false, disp: "Turkish Lira"};
    currency[23] = {value: "RUB", selected: false, disp: "Russian Ruble"};
    currency[24] = {value: "USD", selected: false, disp: "U.S. Dollar"};

    cont.appendChild(createSelect("CURRENCYCODE", "Currency", currency, true, "infCURRENCYCODE"));
}

function createOther() {
    var cont = document.getElementById("other");
    cont.appendChild(createInput("PAYERID", "Payer Id", "", true, ""));
    cont.appendChild(createInput("INVOICEID", "Invoice Id", "123456", true, ""));
    
    var source = new Array();
    source[0] = {value: "any", selected: true, disp: "The merchant does not have a preference. Use any available funding source."};
    source[1] = {value: "default", selected: false, disp: "Use the merchant's preferred funding source, as configured in the merchant's profile."};
    source[2] = {value: "instant", selected: false, disp: "Use the merchant's balance as the funding source."};
    source[3] = {value: "eCheck", selected: false, disp: "The merchant prefers using the eCheck funding source."};
    cont.appendChild(createSelect("REFUNDSOURCE", "Refund Source", source, true, ""));
    
    cont.appendChild(createInput("AMT", "Refund Amount", "5.00", true, ""));
}

function changeRT (elmt) {
    
}

function transactionSearch() {
    var transId = document.getElementsByName("TRANSACTIONID")[0].value;
    var iframe = document.getElementById("tranSearch");
    iframe.setAttribute("src","https://localhost/Gateways/php/transactionSearch.php?transId=" + transId );
}



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

function supported(gateway) {
    reset();
    var params = notSupported[gateway];
    for (var inc = 0; inc < params.length; inc++) {
        var param = document.getElementsByName(params[inc])[0];
        param.setAttribute("value", "");
        param.setAttribute("disabled", "disabled");
    }
}


function loadSession() {
    var inputs = document.getElementsByTagName('input');
    var inc = 0;
    while (inc < inputs.length) {
        var input = inputs[inc];
        var name = input.getAttribute("name");
        var value = urldecode(getValue(name));
        if (value != "-1") {
            input.setAttribute("value",value);
        }
        inc ++;
    }
    var slts = document.getElementsByTagName('select');
    inc = 0;
    while (inc < slts.length){      
        var slt = slts[inc];
        var name = slt.getAttribute("name");
        var value = urldecode(getValue(name));
        if (value != "-1") {
            for (var optInc = 0; optInc < slt.options.length; optInc ++) {
                var option = slt.options[optInc];
                if (option.getAttribute("value") == value) {
                    option.setAttribute("selected","selected");
                } else if (option.hasAttribute("selected")) {
                    option.removeAttribute("selected");
                }
            } 
        }
        inc ++;
    }
   // updateTotal();
}



