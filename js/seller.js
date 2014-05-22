/* 
 * Contains all the functions used to create HTML elements
 */

function addAddress() {
    var cont = document.getElementById("paypal_pages");
    var SACont = document.createElement("section");
    SACont.setAttribute("id", "SACont");
    var hdrSA = document.createElement("h4");
    hdrSA.innerHTML = "Delivery Address";

    var removeBtn = document.createElement("button");
    removeBtn.setAttribute("type", "button");
    removeBtn.setAttribute("class", "btn btn-small btn-danger pull-right");
    removeBtn.setAttribute("onclick", "removeSA()");

    var span = document.createElement("span");
    span.setAttribute("class", "glyphicon glyphicon-trash");

    removeBtn.appendChild(span);

    hdrSA.appendChild(removeBtn);
    SACont.appendChild(hdrSA);

    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTONAME", "Name", "Customer Name", true, "infPAYMENTREQUEST_0_SHIPTONAME"));
    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTOSTREET", "Line 1", "Street 1", true, "infPAYMENTREQUEST_0_SHIPTOSTREET"));
    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTOSTREET2", "Line 2", "Street 2", true, "infPAYMENTREQUEST_0_SHIPTOSTREET2"));
    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTOCITY", "City", "City", true, "infPAYMENTREQUEST_0_SHIPTOCITY"));
    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTOSTATE", "State", "State", true, "infPAYMENTREQUEST_0_SHIPTOSTATE"));
    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTOZIP", "ZIP", "AB12 3CD", true, "infPAYMENTREQUEST_0_SHIPTOZIP"));
    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE", "Country", "GB", true, "infPAYMENTREQUEST_0_SHIPTOCOUNTRYCODE"));

    var button = document.getElementById("addAddress");
    cont.insertBefore(SACont, button);
    button.setAttribute("class", "hide");

    overrideAddress(true);
    getFields();
}
function createBA(sellerID) {
    var sellerCont = document.getElementById("seller" + sellerID)
    var sctBA = document.createElement("section");
    sctBA.setAttribute("id", "BA" + sellerID);


    var hdrBA = document.createElement("h4");
    hdrBA.innerHTML = "Billing Agreement";

    var removeBtn = document.createElement("button");
    removeBtn.setAttribute("class", "btn btn-small btn-danger pull-right");
    removeBtn.setAttribute("onclick", "removeBA(" + sellerID + ")");

    var span = document.createElement("span");
    span.setAttribute("class", "glyphicon glyphicon-trash");

    removeBtn.appendChild(span);

    hdrBA.appendChild(removeBtn);
    sctBA.appendChild(hdrBA);

    var currencyBAType = new Array();
    currencyBAType[0] = {value: "MerchantInitiatedBilling", selected: true, disp: "Merchant Initiated Billing"};
    currencyBAType[1] = {value: "MerchantInitiatedBillingSingleAgreement", selected: false, disp: "Merchant Initiated Billing Single Agreement"};
    sctBA.appendChild(createSelect("L_BILLINGTYPE" + sellerID, "Billing Type", currencyBAType, true));

    sctBA.appendChild(createInput("L_BILLINGAGREEMENTDESCRIPTION" + sellerID, "BA Desc", "", true));

    var button = document.getElementById("addBA" + sellerID);
    sellerCont.insertBefore(sctBA, button);
    button.setAttribute("class", "hide");

    createSideMenu();

}


function createItem(sellerID) {
    var numOfItems = numberOfItems[sellerID];
    var itemNumber = numOfItems + 1;
    var payReq = "L_PAYMENTREQUEST_" + sellerID + "_";
    var items = document.getElementById("items" + sellerID);
    var details = ["NAME", "DESC", "NUMBER", "QTY", "AMT"];
    var labels = ["Name", "Description", "Number", "Quantity", "Amount"];
    var values = ["Item " + itemNumber, "Description", sellerID + "0000" + itemNumber, "1", "5"];

    var itemCont = document.createElement("section");
    itemCont.setAttribute("id", sellerID + "item" + numOfItems);

    var h4 = document.createElement("h4");
    h4.innerHTML = "Item " + itemNumber;

    var removeBtn = document.createElement("button");
    removeBtn.setAttribute("class", "btn btn-small btn-danger pull-right");
    removeBtn.setAttribute("onclick", "removeItem(" + sellerID + "," + numOfItems + ")");

    var span = document.createElement("span");
    span.setAttribute("class", "glyphicon glyphicon-trash");

    removeBtn.appendChild(span);

    h4.appendChild(removeBtn);

    itemCont.appendChild(h4);

    for (var inc = 0; inc < 5; inc++) {
        itemCont.appendChild(createInput(payReq + details[inc] + numOfItems, labels[inc], values[inc], true, "infL_PAYMENTREQUEST_n_" + details[inc] + "m"));
    }
    items.appendChild(itemCont);
    //createMenuItem(sellerID);
    createSideMenu();
    noItems(false);
    numberOfItems[sellerID]++;

    updateTotal();

}

function createLabel(lblText, lblClass) {
    var lbl = document.createElement("label");
    lbl.setAttribute("class", "col-lg-2 control-label");
    lbl.innerHTML = lblText;
    return lbl;
}

function createFieldContainer(contId, lblText) {
    var cont = document.createElement("div");
    cont.setAttribute("id", contId);
    cont.setAttribute("class", "form-group");

    // The label associated with the input
    cont.appendChild(createLabel(lblText));

    return cont;
}

function createInpContainer() {
    var cont = document.createElement("div");
    cont.setAttribute("class", "col-lg-9");

    return cont;
}

function createCheckbox(cbxName, lblText, cbxValue, onChange, checked, infId) {
    // The input/label container
    var cont = createFieldContainer(cbxName, lblText);
    // The input container
    var cbxCont = createInpContainer();
    // The checkbox
    var cbx = document.createElement("input");
    if (cbxName != "") {
        cbx.setAttribute("name", cbxName);
    }
    if (cbxValue != "") {
        cbx.setAttribute("value", cbxValue);
    }
    if (onChange != "") {
        cbx.setAttribute("onchange", onChange);
    } else {
        cbx.setAttribute("onchange", "update(this)");
    }
    if (checked) {
        cbx.setAttribute("checked", "checked");
    }

    cbx.setAttribute("type", "checkbox");
    cbxCont.appendChild(cbx);
    cont.appendChild(cbxCont);

    // The information associated with this field
    cont.appendChild(createPopover(infId));

    return cont;
}

function createSelect(sltName, lblText, opts, enabled, infId) {
    // The input/label container
    var cont = createFieldContainer(sltName, lblText);
    // The input container
    var sltCont = createInpContainer();
    // The select
    var slt = document.createElement("select");
    slt.setAttribute("class", "form-control");
    slt.setAttribute("name", sltName);
    // The options
    for (var inc = 0; inc < opts.length; inc++) {
        var tempOpt = document.createElement("option");
        tempOpt.setAttribute("value", opts[inc].value);
        if (opts[inc].selected) {
            tempOpt.setAttribute("selected", "selected");
        }
        tempOpt.innerHTML = opts[inc].disp;
        slt.appendChild(tempOpt);
    }

    if (!enabled) {
        slt.setAttribute("disabled", "disabled");
    }
    sltCont.appendChild(slt);
    cont.appendChild(sltCont);
    cont.appendChild(createPopover(infId));

    return cont;
}


function createInput(inpName, lblText, inpValue, enabled, infId) {
    // The checkbox/label container
    var cont = createFieldContainer(inpName, lblText);
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

function createSearch(inpName, lblText, inpValue, enabled, searchFunc, infId) {
    // The checkbox/label container
    var cont = createFieldContainer(inpName, lblText);
    // The input container
    var inpCont = createInpContainer();
    // The input
    var searchCont = document.createElement("div");
    searchCont.setAttribute("class", "input-group");

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


    searchCont.appendChild(inp);

    var searchBtnCont = document.createElement("span");
    searchBtnCont.setAttribute("class", "input-group-btn");

    var searchBtn = document.createElement("button");
    searchBtn.setAttribute("class", "btn btn-link");
    searchBtn.setAttribute("type", "button");
    searchBtn.setAttribute("onclick", searchFunc);

    var icon = document.createElement("span");
    icon.setAttribute("class", "glyphicon glyphicon-search");
    searchBtn.appendChild(icon);

    searchBtnCont.appendChild(searchBtn);
    searchCont.appendChild(searchBtnCont);
    inpCont.appendChild(searchCont);
    cont.appendChild(inpCont);

    // The information associated with this field
    cont.appendChild(createPopover(infId));

    return cont;
}

function createMenuItem(sellerID) {
    var newItemNumber = numberOfItems[sellerID];
    var subMenu = document.getElementById("lnkitems" + sellerID);

    if (subMenu == undefined) {
        createSellerMenu(sellerID, newItemNumber);
    }
    else {
        var newListItem = createListItem("#" + sellerID + "item" + newItemNumber, "lnk" + sellerID + "item" + newItemNumber, "Item " + (newItemNumber + 1));
        var target = document.getElementById("lnkBA" + sellerID);
        var targetID = "lnkBA" + sellerID;
        if (target == null) {
            target = document.getElementById("lnktotals" + sellerID);
            targetID = "lnktotals" + sellerID;
        }
        addListItem("lnkitems" + sellerID, newListItem, targetID);
    }
}

function createSellerMenu(sellerID, itemNumber) {
    var newListItem = document.createElement("li");

    // Add link to seller
    newListItem.appendChild(createAnchor("#seller" + sellerID, "", "Seller " + (sellerID + 1)));

    // Add sublist
    var sublist = document.createElement("ul");
    sublist.setAttribute("class", "nav");
    sublist.setAttribute("id", "lnkitems" + sellerID);

    // Add link to item, totals and other
    if (itemNumber != -1) {
        sublist.appendChild(createListItem("#" + sellerID + "item" + itemNumber, "lnk" + sellerID + "item" + itemNumber, "Item " + (itemNumber + 1)));
    }

    sublist.appendChild(createListItem("#totals" + sellerID, "lnktotals" + sellerID, "Totals"));
    sublist.appendChild(createListItem("#other" + sellerID, "lnkother" + sellerID, "Other"));

    newListItem.appendChild(sublist);
    addListItem("sideMenu", newListItem, "lnkcustom");
}

function createBALink(sellerID) {
    var listItem = createListItem("#BA" + sellerID, "lnkBA" + sellerID, "Billing Agreement");
    addListItem("lnkitems" + sellerID, listItem, "lnktotals" + sellerID);

}

function createButton(contId, onClick, text, icon) {
    var contBtn = document.createElement("h4");
    if (contId != "") {
        contBtn.setAttribute("id", contId);
    }
    var btn = document.createElement("button");
    btn.setAttribute("class", "btn btn-primary add");
    btn.setAttribute("type", "button");
    btn.setAttribute("onclick", onClick);
    var span = document.createElement("span");
    span.setAttribute("class", "glyphicon glyphicon-" + icon);
    btn.innerHTML = text;
    btn.appendChild(span);

    contBtn.appendChild(btn);

    return contBtn;
}

function createSeller() {
    var sellerID = numberOfSellers;
    numberOfSellers++;
    numberOfItems[sellerID] = 0;
    var dispSellerID = sellerID + 1;
    var sellers = document.getElementById("sellers");
    /*if (sellerID == 2) {
     var btn = document.getElementById("addSeller");
     sellers.removeChild(btn);
     }*/

    // Set up the fieldset to hold all the details related to this seller
    var sellerCont = document.createElement("section");
    sellerCont.setAttribute("id", "seller" + sellerID);
    sellerCont.setAttribute("class", "sellerCont");

    // Create and add the title
    var title = document.createElement("h3");
    title.innerHTML = "Seller " + (dispSellerID) + " <small>Specify the details associated with seller " + (dispSellerID) + " </small>";
    if (sellerID != 0) {
        var removeBtn = document.createElement("button");
        removeBtn.setAttribute("class", "btn btn-small btn-danger");
        removeBtn.setAttribute("onclick", "removeSeller(" + sellerID + ")");

        var span = document.createElement("span");
        span.setAttribute("class", "glyphicon glyphicon-trash");

        removeBtn.appendChild(span);
        title.appendChild(removeBtn);
    }
    sellerCont.appendChild(title);

    // Create and add the option for line items.
    var sctLI = document.createElement("section");
    sctLI.setAttribute("id", "items" + sellerID);
    //sctLI.setAttribute("class", "hide");

    sellerCont.appendChild(sctLI);

    // Create and add the button to add more items
    sellerCont.appendChild(createButton("", "createItem(" + sellerID + ")", "Line Item ", "plus"));

    // Create and add the option for billing agreements
    sellerCont.appendChild(createButton("addBA" + sellerID, "createBA(" + sellerID + ")", "Billing Agreement ", "plus"));

    // Create and add the totals  
    var hdrTot = document.createElement("h4");
    hdrTot.setAttribute("id", "totals" + sellerID);
    hdrTot.innerHTML = "Totals";
    sellerCont.appendChild(hdrTot);

    var itemTotal = createInput("PAYMENTREQUEST_" + sellerID + "_ITEMAMT", "Item", "", true, "infPAYMENTREQUEST_n_ITEMAMT");
    itemTotal.setAttribute("class", "hide");
    sellerCont.appendChild(itemTotal);
    sellerCont.appendChild(createInput("PAYMENTREQUEST_" + sellerID + "_SHIPPINGAMT", "Shipping", "", true, "infPAYMENTREQUEST_n_SHIPPINGAMT"));
    sellerCont.appendChild(createInput("PAYMENTREQUEST_" + sellerID + "_HANDLINGAMT", "Handling", "", true, "infPAYMENTREQUEST_n_HANDLINGAMT"));
    sellerCont.appendChild(createInput("PAYMENTREQUEST_" + sellerID + "_TAXAMT", "Tax", "", true, "infPAYMENTREQUEST_n_TAXAMT"));
    sellerCont.appendChild(createInput("PAYMENTREQUEST_" + sellerID + "_AMT", "Total", "", true, "infPAYMENTREQUEST_n_AMT"));

    // Create and add the other information
    var hdrOth = document.createElement("h4");
    hdrOth.setAttribute("id", "other" + sellerID);
    hdrOth.innerHTML = "Other";
    sellerCont.appendChild(hdrOth);

    sellerCont.appendChild(createInput("PAYMENTREQUEST_" + sellerID + "_INVNUM", "Invoice Number", "", true, "infPAYMENTREQUEST_n_INVNUM"));
    sellerCont.appendChild(createInput("PAYMENTREQUEST_" + sellerID + "_CUSTOM", "Custom", "", true, "infPAYMENTREQUEST_n_CUSTOM"));
    var pymtAct = new Array();
    pymtAct[0] = {value: "Sale", selected: true, disp: "Sale"};
    pymtAct[1] = {value: "Authorisation", selected: false, disp: "Authorisation"};
    pymtAct[2] = {value: "Order", selected: false, disp: "Order"};
    sellerCont.appendChild(createSelect("PAYMENTREQUEST_" + sellerID + "_PAYMENTACTION", "Payment Action", pymtAct, true, "infPAYMENTREQUEST_n_PAYMENTACTION"));

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

    sellerCont.appendChild(createSelect("PAYMENTREQUEST_" + sellerID + "_CURRENCYCODE", "Currency", currency, true, "infPAYMENTREQUEST_n_CURRENCYCODE"));

    if (sellerID == 0) {
        sellers.appendChild(sellerCont);
        sellers.appendChild(createButton("PARALLEL", "createSeller()", "Seller ", "plus"));
    } else {
        var addBtn = document.getElementById("PARALLEL");
        if (addBtn != null) {
            sellers.insertBefore(sellerCont, addBtn);
        } else {
            sellers.appendChild(sellerCont);
        }


    }

    document.getElementsByName("PAYMENTREQUEST_" + sellerID + "_AMT")[0].setAttribute("readonly", "");
    //createSellerMenu(sellerID, -1);
    // Add items at the end.
//    /createItem(sellerID);
    getFields();
    // makePopovers();
}



function updateLogo(oldLogo) {
    var name = oldLogo.getAttribute("id");
    //alert(name);
    var logoType;
    var old;
    var value;
    if (name == "LOGOIMG") {
        logoType = "Logo";
        old = "HDRIMG";
        value = "https://www.paypalobjects.com/webstatic/mktg/logo/pp_cc_mark_37x23.jpg";
    } else {
        logoType = "Header";
        old = "LOGOIMG";
        value = "https://www.paypalobjects.com/webstatic/mktg/logo/bdg_now_accepting_pp_2line_w.png";
    }
    var logo = document.getElementsByName(old)[0];
    logo.setAttribute("name", name);
    logo.setAttribute("value", value);

    var logoBtn = document.getElementById("logo");
    logoBtn.innerHTML = logoType;
}

function updateItems(sellerID) {
    var listItems = document.getElementsByTagName("li");
    var itemNumber = 0;
    for (var inc = 0; inc < listItems.length; inc++) {
        var listItem = listItems[inc];
        if ((listItem.id != "") && (listItem.id != ("lnkother" + sellerID)) && (listItem.id != ("lnktotals" + sellerID)) && (listItem.id != ("lnkBA" + sellerID)) && (listItem.id != "lnkcustom")) {
            listItem.id = "lnk" + sellerID + "item" + itemNumber;
            var children = listItem.childNodes;
            
            children[0].innerHTML = "Item " + (itemNumber + 1);
            itemNumber++;
        }

    }
    var options = ["NAME", "DESC", "NUMBER", "QTY", "AMT"];
    for (var sellerID = 0; sellerID < numberOfSellers; sellerID++) {
        var itemsCont = document.getElementById("items" + sellerID);
        var items = itemsCont.childNodes;
        var numOfItems = 0;
        for (var itemID = 0; itemID < items.length; itemID++) {
            var itemCont = items[itemID];
            //alert(itemCont.textContent);
            //item = item.nextSibling;
            itemCont.setAttribute("id", sellerID + "item" + numOfItems);
            var children = itemCont.childNodes;
            var optionID = 0;
            for (var inc = 0; inc < children.length; inc++) {
                var child = children[inc];
                //alert(child.tagName);
                if (child.tagName == "DIV") {
                    var old = child.getAttribute("id");
                    child.setAttribute("id", "L_PAYMENTREQUEST_" + sellerID + "_" + options[optionID] + numOfItems);
                    var input = document.getElementsByName(old)[0];
                    input.setAttribute("name", "L_PAYMENTREQUEST_" + sellerID + "_" + options[optionID] + numOfItems);
                    optionID ++;
                } else if (child.tagName == "H4") {
                    var h4 = document.createElement("h4");
                    h4.innerHTML = "Item " + (numOfItems + 1);

                    var removeBtn = document.createElement("button");
                    removeBtn.setAttribute("class", "btn btn-small btn-danger pull-right");
                    removeBtn.setAttribute("onclick", "removeItem(" + sellerID + "," + numOfItems + ")");
                    
                    var span = document.createElement("span");
                    span.setAttribute("class", "glyphicon glyphicon-trash");

                    removeBtn.appendChild(span);

                    h4.appendChild(removeBtn);
                    itemCont.replaceChild(h4,child);
                    
                }
            }
            numOfItems++;
        }
    }
}


/*
 * Remove functions
 */
function removeItem(sellerID, itemID) {
    var item = document.getElementById(sellerID + "item" + itemID);
    var items = document.getElementById("items" + sellerID);
    items.removeChild(item);    
    numberOfItems[sellerID]--;
   // alert(numberOfItems[sellerID]);
    if (numberOfItems[sellerID] > 0) {
        updateItems(sellerID);
    } else {
        noItems(true);
    }
    updateTotal();
    createSideMenu();
}

function removeBA(sellerID) {
    var sellerCont = document.getElementById("seller" + sellerID);
    var BACont = document.getElementById("BA" + sellerID)
    sellerCont.removeChild(BACont);
    document.getElementById("addBA"+ sellerID).setAttribute("class","");
    
    createSideMenu();
}

function removeSA() {
    var cont = document.getElementById("paypal_pages");
    var SACont = document.getElementById("SACont")
    cont.removeChild(SACont);
    document.getElementById("addAddress").setAttribute("class","");
    
    overrideAddress(false);
    createSideMenu();
}


function updateTotal() {
    var total = 0;
    var sellerTotal = 0;
    var totalItems = 0;
    var item = true;

    for (var sellerID = 0; sellerID < numberOfSellers; sellerID++) {
        var paymentReq = "L_PAYMENTREQUEST_" + sellerID + "_";
        var items = numberOfItems[sellerID];
        if (items > 0) {
            for (var itemID = 0; itemID < items; itemID++) {
                var qty = document.getElementsByName(paymentReq + "QTY" + itemID)[0];
                if (qty == "") {
                    break;
                }
                qty = qty.value;
                var amt = document.getElementsByName(paymentReq + "AMT" + itemID)[0];
                amt = amt.value;
                if (amt == "") {
                    break;
                }
                var subTotal = parseFloat(amt) * parseInt(qty);
                sellerTotal += subTotal;
            }
            document.getElementsByName("PAYMENTREQUEST_" + sellerID + "_ITEMAMT")[0].value = parseFloat(sellerTotal).toFixed(2);
            var totals = ["SHIPPING", "HANDLING", "TAX"];
        } else {
            var totals = ["ITEM", "SHIPPING", "HANDLING", "TAX"];
        }
        for (var inc = 0; inc < totals.length; inc++) {
            //alert("PAYMENTREQUEST_" + sellerID + "_" + totals[inc] + "AMT");
            var tot = document.getElementsByName("PAYMENTREQUEST_" + sellerID + "_" + totals[inc] + "AMT")[0];
            tot = tot.value;
            if ((typeof (tot) == 'undefined') || (tot == null) || (tot == "")) {
                continue;
            }
            sellerTotal += parseFloat(tot);
        }
        document.getElementsByName("PAYMENTREQUEST_" + sellerID + "_AMT")[0].value = parseFloat(sellerTotal).toFixed(2);
        sellerTotal = 0;
    }
    getFields();
}


function update(elmt) {
    var errMsg = "";
    var name = elmt.name;
    var val = elmt.value;
    var len = val.length;
    var start = "<p id=\"" + name + "_ERR\">";
    switch (elmt.name) {
        case "GATEWAY":
            //supported(val);
            break;
        case "PAYMENTREQUEST_0_INVNUM":
            var patt = /[^0-9A-z]/;
            if ((patt.test(val)) || (len > 256)) {
                errMsg = start + "<strong>Invoice Number: </strong>";
                errMsg += "Invoice number must be made up of up to 256 single-byte alphanumeric characters</p>";
            }
            break;
        case "MAXAMT":
            var dotpos = val.lastIndexOf(".");
            var patt = /[^0123456789,.]/;
            if ((patt.test(val)) || (dotpos != len - 3)) {
                errMsg = start + "<strong>Maximum Amount: </strong>";
                errMsg += "Maximum Amount must only contain numbers and must be rounded to two decimal places</p>";
            }
            break;
        case "RETURNURL":
            if (val == "") {
                errMsg = start + "<strong>Return URL: </strong>";
                errMsg += "You must specify a URL to return to upon returning successfully from the PayPal pages</p>";
            }
            break;
        case "CANCELURL":
            if (val == "") {
                errMsg = start + "<strong>Cancel URL: </strong>";
                errMsg += "You must specify a URL to return to upon returning unsuccessfully from the PayPal pages";
            }
            break;
        default:
            break;
    }
    /*
     
     if (errMsg != "") {
     if(elmt.hasAttribute("class")) { 
     var curClass = elmt.getAttribute("class");
     var curClassArr = curClass.split(" ");
     for (var inc = 0; inc < curClassArr.length; inc ++) {
     
     }
     var fail = /(inputerror$ | inputsuccess$)/;
     if (fail.test(curClass)) {
     
     }
     }
     elmt.setAttribute("class","inputerror");
     setError(errMsg);
     } else {
     elmt.setAttribute("class","inputsuccess");
     removeError(name);	
     }*/
    updateTotal();
}