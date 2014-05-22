// JScript source code
var numberOfItems = 0;
var notSupported = new Object();
notSupported["Standard"] = [];
notSupported["DataCash"] = ["DEVICE", "PAYMENTREQUEST_0_PAYMENTACTION", "LANDINGPAGE", "L_PAYMENTREQUEST_0_DESC0", "PARALLEL"];
notSupported["CyberSource"] = ["SOLUTIONTYPE", "ALLOWNOTE", ""];
$(window).load(function() {
    createGeneral();
    createMPItem('EmailAddress');
    createSideMenu();
});
function createGeneral() {
    var cont = document.getElementById("general").parentNode;
    cont.appendChild(createInput("EMAILSUBJECT", "Email Subject", "Your Payment", true, "infEMAILSUBJECT"));

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

    var sltType = new Array();
    sltType[0] = {value: "EmailAddress", selected: true, disp: "Email Address"};
    sltType[1] = {value: "UserId", selected: false, disp: "User ID"};
    sltType[2] = {value: "PhoneNumber", selected: false, disp: "Phone Number"};
    cont.appendChild(createSelect("RECEIVERTYPE", "Receiver Type", sltType, true, "infRECEIVERTYPE"));
    document.getElementsByName("RECEIVERTYPE")[0].setAttribute("onchange","updateReceiverType(this)");
}

function updateReceiverType(elmtRT) {
    var value = elmtRT.options[elmtRT.selectedIndex].getAttribute("value");
    switch (value) {
        case 'EmailAddress':
            var newName = "L_EMAIL";
            var text = "Email Address";
            var def = "jsmith@test.com";
            break;
        case 'UserId':
            var newName = "L_RECEIVERID";
            var text = "Payer Id";
            var def = "ASDLAJKSLDKSD";
            break;
        case 'PhoneNumber':
            var newName = "L_RECEIVERPHONE";
            var text = "Phone Number";
            var def = "01234567890";
            break;
    }
    var emails = document.getElementsByName("L_EMAIL0");
    var nums = document.getElementsByName("L_RECEIVERPHONE0");
    var ids = document.getElementsByName("L_RECEIVERID0");
    if (emails.length > 0) {
        var name = "L_EMAIL";
    } else if (nums.length > 0) {
        var name = "L_RECEIVERPHONE";
    } else if (ids.length > 0) {
        var name = "L_RECEIVERID";
    }
    for (var inc = 0; inc < numberOfItems; inc ++) {
        var elmt = document.getElementsByName(name + inc)[0];
        elmt.setAttribute("name", newName + inc);
        elmt.setAttribute("value",def);
        var lbl = document.getElementById("rt" + inc);
        lbl.innerHTML = text;
    }
}


function createMPItem(type) {
    var cont = document.getElementById("items");
    var itemCont = document.createElement("section");
    itemCont.setAttribute("id", "item" + numberOfItems);
    
    var itemNumber = numberOfItems + 1;
    var h4 = document.createElement("h4");
    h4.innerHTML = "Mass Pay Item " + itemNumber;
    itemCont.appendChild(h4);
    switch (type) {
        case 'EmailAddress':
            itemCont.appendChild(createSpecialInput("L_EMAIL" + numberOfItems, "Email Address", "jsmith@test.com", true, "infL_EMAILn", "rt" + numberOfItems));
            break;
        case 'UserId':
            itemCont.appendChild(createSpecialInput("L_RECEIVERID" + numberOfItems, "Payer Id", "ASDLAJKSLDKSD", true, "infL_RECEIVERIDn", "rt" + numberOfItems));
            break;
        case 'PhoneNumber':
            itemCont.appendChild(createSpecialInput("L_RECEIVERPHONE" + numberOfItems, "Phone Number", "01234567890", true, "infL_RECEIVERPHONEn", "rt" + numberOfItems));
            break;
    }
    itemCont.appendChild(createInput("L_AMT" + numberOfItems, "Amount", "5.00", true, "infL_AMTn"));
    itemCont.appendChild(createInput("L_UNIQUEID" + numberOfItems, "Unique Id", new Date().getTime() / 1000, true, "infUNIQUEIDn"));
    itemCont.appendChild(createInput("L_NOTE" + numberOfItems, "Note", "This is your payment.", true, "infL_NOTEn"));
    
    cont.appendChild(itemCont);
    numberOfItems ++;
    
}

function createSpecialInput(inpName, lblText, inpValue, enabled, infId, lblId) {
     // The checkbox/label container
    var cont = createSpecialFieldContainer(inpName, lblText, lblId);
    // The input container
    var inpCont = createInpContainer();
    // The input
    var inp = document.createElement("input");
    if (inpName != "") {
        inp.setAttribute("name", inpName);
    }
    if (inpValue != "") {
        inp.setAttribute("value", inpValue); 
    }
    if (!enabled) {
        inp.setAttribute("disabled", "disabled");
    }
    inp.setAttribute("class", "form-control");
    inp.setAttribute("type", "text");
    inp.setAttribute("onchange", "update(this)");
    
    inpCont.appendChild(inp);
    cont.appendChild(inpCont);
    
    // The information associated with this field
    cont.appendChild(createPopover(infId));
    
    return cont;
}

function createSpecialFieldContainer(contId, lblText, lblId) {
    var cont = document.createElement("div");
    cont.setAttribute("id", contId);
    cont.setAttribute("class", "form-group");

    // The label associated with the input
    cont.appendChild(createSpecialLabel(lblText, lblId));
    
    return cont;
}

function createSpecialLabel(lblText, lblId) {
    var lbl = document.createElement("label");
    lbl.setAttribute("class", "col-lg-2 control-label");
    lbl.setAttribute("id", lblId);
    lbl.innerHTML = lblText;
    return lbl;
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



