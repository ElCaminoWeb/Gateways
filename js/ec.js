// JScript source code

//include ('./js/edit.js');
//include ('./js/create.js');


var numberOfSellers = 0;
var numberOfItems = new Array();
var notSupported = new Object();
notSupported["Standard"] = [];
notSupported["DataCash"] = ["DEVICE", "PAYMENTREQUEST_0_PAYMENTACTION", "LANDINGPAGE", "L_PAYMENTREQUEST_0_DESC0", "PARALLEL"];
notSupported["CyberSource"] = ["ALLOWNOTE"];
notSupported["SagePay (API)"] = ["SOLUTIONTYPE", "ALLOWNOTE"];
include('./js/jscolor/jscolor.js');
include('./js/seller.js');
$(window).load(function() {
    createSeller();
    createPayPalPages();
    createSideMenu();
    loadSession();
});
function createPayPalPages() {
    var cont = document.getElementById("paypal_pages");
    cont.appendChild(createInput("CARTBORDERCOLOR", "Border Colour", "00457C", true, "infCARTBORDERCOLOR"));
    document.getElementsByName("CARTBORDERCOLOR")[0].setAttribute("class", "form-control color");
    cont.appendChild(createInput("BRANDNAME", "Brand name", "Gateway Test", true, "infBRANDNAME"));

    var sltSolutionType = new Array();
    sltSolutionType[0] = {value: "Sole", selected: true, disp: "Sole"};
    sltSolutionType[1] = {value: "Mark", selected: false, disp: "Mark"};
    cont.appendChild(createSelect("SOLUTIONTYPE", "Solution Type", sltSolutionType, true, "infSOLUTIONTYPE"));

    var sltCommit = new Array();
    sltCommit[0] = {value: "No", selected: true, disp: "Continue"};
    sltCommit[1] = {value: "Yes", selected: false, disp: "Commit"};
    cont.appendChild(createSelect("COMMIT", "User Action", sltCommit, true, "infCOMMIT"));

    var sltLanding = new Array();
    sltLanding[0] = {value: "Login", selected: true, disp: "Login"};
    sltLanding[1] = {value: "Billing", selected: false, disp: "Billing"};
    cont.appendChild(createSelect("LANDINGPAGE", "Landing Page", sltLanding, true, "infLANDINGPAGE"));

    var sltLocale = new Array();
    sltLocale[0] = {value: "AT", selected: false, disp: "Austria"};
    sltLocale[1] = {value: "AU", selected: false, disp: "Australia"};
    sltLocale[2] = {value: "BE", selected: false, disp: "Belgium"};
    sltLocale[3] = {value: "BR", selected: false, disp: "Brazil"};
    sltLocale[4] = {value: "CA", selected: false, disp: "Canada"};
    sltLocale[5] = {value: "CH", selected: false, disp: "Switzerland"};
    sltLocale[6] = {value: "CN", selected: false, disp: "China"};
    sltLocale[7] = {value: "DE", selected: false, disp: "Germany"};
    sltLocale[8] = {value: "ES", selected: false, disp: "Spain"};
    sltLocale[9] = {value: "GB", selected: true, disp: "United Kingdom"};
    sltLocale[10] = {value: "FR", selected: false, disp: "France"};
    sltLocale[11] = {value: "IT", selected: false, disp: "Italy"};
    sltLocale[12] = {value: "NL", selected: false, disp: "Netherlands"};
    sltLocale[13] = {value: "PL", selected: false, disp: "Poland"};
    sltLocale[14] = {value: "PT", selected: false, disp: "Portugal"};
    sltLocale[15] = {value: "RU", selected: false, disp: "Russia"};
    sltLocale[16] = {value: "US", selected: false, disp: "United States"};
    sltLocale[17] = {value: "da_DK", selected: false, disp: "Danish (for Denmark only)"};
    sltLocale[18] = {value: "he_IL", selected: false, disp: "Hebrew (all)"};
    sltLocale[19] = {value: "id_ID", selected: false, disp: "Indonesian (for Indonesia only)"};
    sltLocale[20] = {value: "ja_JP", selected: false, disp: "Japanese (for Japan only)"};
    sltLocale[21] = {value: "no_NO", selected: false, disp: "Norwegian (for Norway only)"};
    sltLocale[22] = {value: "pt_BR", selected: false, disp: "Brazilian Portuguese (for Portugal and Brazil only)"};
    sltLocale[23] = {value: "ru_RU", selected: false, disp: "Russian (for Lithuania, Latvia, and Ukraine only)"};
    sltLocale[24] = {value: "sv_SE", selected: false, disp: "Swedish (for Sweden only)"};
    sltLocale[25] = {value: "th_TH", selected: false, disp: "Thai (for Thailand only)"};
    sltLocale[26] = {value: "tr_TR", selected: false, disp: "Turkish (for Turkey only)"};
    sltLocale[27] = {value: "zh_CN", selected: false, disp: "Simplified Chinese (for China only)"};
    sltLocale[28] = {value: "zh_HK", selected: false, disp: "Traditional Chinese (for Hong Kong only)"};
    sltLocale[29] = {value: "zh_TW", selected: false, disp: "Traditional Chinese (for Taiwan only)"};
    cont.appendChild(createSelect("LOCALECODE", "Locale Code", sltLocale, true, "infLOCALECODE"));

    var sltNoShipping = new Array();
    sltNoShipping[0] = {value: "0", selected: true, disp: "Display shipping address"};
    sltNoShipping[1] = {value: "1", selected: false, disp: "Do not display shipping address"};
    sltNoShipping[2] = {value: "2", selected: false, disp: "If you do not pass the shipping address, PayPal obtains it from the buyer's account profile."};
    cont.appendChild(createSelect("NOSHIPPING", "No Shipping", sltNoShipping, true, "infNOSHIPPING"));

    var sltAllowNote = new Array();
    sltAllowNote[0] = {value: "1", selected: true, disp: "Yes"};
    sltAllowNote[1] = {value: "0", selected: false, disp: "No"};
    cont.appendChild(createSelect("ALLOWNOTE", "Allow Note", sltAllowNote, true, "infALLOWNOTE"));

    var addrOver = document.createElement("input");
    addrOver.setAttribute("type", "hidden");
    addrOver.setAttribute("name", "ADDROVERRIDE");
    cont.appendChild(addrOver);
    cont.appendChild(createButton("addAddress", "addAddress()", "Override Address", "pencil"));
}

function noItems(none) {
    var itemTotal = document.getElementById("PAYMENTREQUEST_0_ITEMAMT");
    var newClass = (none) ? "form-group" : "hide";
    itemTotal.setAttribute("class", newClass);
}

function overrideAddress(override) {
    var val = (override) ? "1" : "0";
    document.getElementsByName("ADDROVERRIDE")[0].value = val;
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

function loadSession() {
    var logoFound = getSpecifics();
    var inputs = document.getElementsByTagName('input');
    var inc = 0;
    while (inc < inputs.length) {
        var input = inputs[inc];
        var name = input.getAttribute("name");
        if (((name != "LOGOIMG") && (name != "HDRIMG")) || (logoFound)) {
            var value = urldecode(getValue(name));
            if (value != "-1") {
                input.setAttribute("value", value);
            }
        }
        inc++;
    }
    var slts = document.getElementsByTagName('select');
    inc = 0;
    while (inc < slts.length) {
        var slt = slts[inc];
        var name = slt.getAttribute("name");
        var value = urldecode(getValue(name));
        if (value != "-1") {
            for (var optInc = 0; optInc < slt.options.length; optInc++) {
                var option = slt.options[optInc];
                if (option.getAttribute("value") == value) {
                    option.setAttribute("selected", "selected");
                } else if (option.hasAttribute("selected")) {
                    option.removeAttribute("selected");
                }
            }
        }
        inc++;
    }

    updateTotal();
}

function getSpecifics() {
    // Get number of sellers
    // Get number of items
    var sellerId = 0;
    var numberOfItems = determineItems(sellerId);
    //alert(numberOfItems);
    if (numberOfItems == 0) {
        noItems(true);
    }
    // Is there a BA?
    var BA = determineBA(sellerId);
    // Has the address been overriden?
    var AO = determineAO();
    // What type of logo has been used
    var logo = determineLogo();

    return logo;
}

function determineItems(sellerId) {
    var payReq = "L_PAYMENTREQUEST_" + sellerId + "_";
    var itemAr = ["NAME", "DESC", "QTY", "NUMBER", "AMT"];
    var itemNo = 0;
    var def = true;
    while (def) {
        var found = false;
        for (var inc = 0; inc < itemAr.length; inc++) {
            var tmpName = payReq + itemAr[inc] + itemNo;
            var tmpVal = getValue(tmpName);
            if (tmpVal != "-1") {
                found = true;
            }
        }
        if (found) {
            itemNo++;
            createItem(sellerId);
        } else {
            def = false;
        }
    }

    return itemNo;
}

function determineBA(sellerId) {
    var start = "L_BILLING";
    var descAr = ["TYPE", "AGREEMENTDESCRIPTION"];

    var found = false;
    for (var inc = 0; inc < descAr.length; inc++) {
        var tmpName = start + descAr[inc] + sellerId;
        var tmpVal = getValue(tmpName);
        if (tmpVal != "-1") {
            found = true;
        }
    }
    if (found) {
        createBA(sellerId);
    }

    return found;
}

function determineAO() {
    var found;
    var addrOver = getValue("ADDROVERRIDE");
    if (addrOver == "-1") {
        found = false;
    } else if (addrOver == "1") {
        found = true;
        addAddress();
    } else {
        found = true;
    }

    return found;
}

function determineLogo() {
    var found = false;
    if (getValue("LOGOIMG") != "-1") {
        if (getValue("LOGOIMG") == "See uploaded") {
            found = false;
        } else {
            found = true;
        }
    } else if (getValue("HDRIMG") != "-1") {
        if (getValue("HDRIMG") == "See uploaded") {
            found = false;
        } else {
            updateLogo(document.getElementById("HDRIMG"));
            found = true;
        }

    }

    return found;

}

function logoUploaded() {
    var logo = document.getElementsByName("LOGOIMG")[0];
    var hdr = document.getElementsByName("HDRIMG")[0];
    if (logo != undefined) {
        logo.setAttribute("value", "See uploaded");
        logo.setAttribute("readonly", "readonly");
    } else if (hdr != undefined) {
        hdr.setAttribute("value", "See uploaded");
        hdr.setAttribute("readonly", "readonly");
    }
}