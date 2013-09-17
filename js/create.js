/* 
 * Contains all the functions used to create HTML elements
 */

function addAddress() {
    var cont = document.getElementById("customise");
    var SACont = document.createElement("section");
    SACont.setAttribute("id", "SACont");
    var hdrSA = document.createElement("h4");
    hdrSA.innerHTML = "Delivery Address";
    
    var removeBtn = document.createElement("button");
    removeBtn.setAttribute("class", "btn btn-small btn-danger pull-right");
    removeBtn.setAttribute("onclick", "removeSA()");

    var span = document.createElement("span");
    span.setAttribute("class", "glyphicon glyphicon-trash");

    removeBtn.appendChild(span);

    hdrSA.appendChild(removeBtn);
    SACont.appendChild(hdrSA);
  
    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTONAME", "Name", "text", "form-control", "Customer Name", true));
    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTOSTREET", "Line 1", "text", "form-control", "Street 1", true));
    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTOSTREET2", "Line 2", "text", "form-control", "Street 2", true));
    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTOCITY", "City", "text", "form-control", "City", true));
    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTOSTATE", "State", "text", "form-control", "State", true));
    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTOZIP", "ZIP", "text", "form-control", "AB12 3CD", true));
    SACont.appendChild(createInput("PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE", "Country", "text", "form-control", "GB", true));
    
    var button = document.getElementById("addAddress");
    cont.insertBefore(SACont,button);
    button.setAttribute("class","hide");         
    
    overrideAddress(true);
}
function createBA (sellerID) {
    var sellerCont = document.getElementById("seller"+sellerID)
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

    sctBA.appendChild(createInput("L_BILLINGAGREEMENTDESCIPTION" + sellerID, "BA Desc", "text", "form-control", "", true));

    var button = document.getElementById("addBA" + sellerID);
    sellerCont.insertBefore(sctBA,button);
    button.setAttribute("class","hide");
    
    createBALink(sellerID);
    
}


function createItem(sellerID) {
    var numOfItems = numberOfItems[sellerID];
    var payReq = "L_PAYMENTREQUEST_" + sellerID + "_";
    var itemNumber = numOfItems + 1;
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
        itemCont.appendChild(createInput(payReq + details[inc] + numOfItems, labels[inc], "text", "form-control", values[inc], true));
    }
    items.appendChild(itemCont);
    createMenuItem(sellerID);
    noItems(false);
    numberOfItems[sellerID]++;
    
    updateTotal();

}

function createLabel(lblName, lblClass) {
    var lbl = document.createElement("label");
    lbl.setAttribute("class", lblClass);
    lbl.innerHTML = lblName;
    return lbl;
}

function createSelect(sltName, lblText, currency, enabled) {
    // The select/label container
    var cont = document.createElement("div");
    cont.setAttribute("id", sltName);
    cont.setAttribute("class", "form-group");

    // The label associated with the select
    var lbl = document.createElement("label");
    lbl.setAttribute("class", "col-lg-2 control-label");
    lbl.innerHTML = lblText;
    cont.appendChild(lbl);

    // The select container
    var sltCont = document.createElement("div");
    sltCont.setAttribute("class", "col-lg-10");

    // The select
    var slt = document.createElement("select");
    slt.setAttribute("class", "form-control");
    slt.setAttribute("name", sltName);
    // The options
    for (var inc = 0; inc < currency.length; inc++) {
        var tempOpt = document.createElement("option");
        tempOpt.setAttribute("value", currency[inc].value);
        if (currency[inc].selected) {
            tempOpt.setAttribute("selected", "selected");
        }
        tempOpt.innerHTML = currency[inc].disp;
        slt.appendChild(tempOpt);
    }

    if (!enabled) {
        slt.setAttribute("disabled", "disabled");
    }
    sltCont.appendChild(slt);
    cont.appendChild(sltCont);

    return cont;
}


function createInput(inpName, lblText, inpType, inpClass, inpValue, enabled) {
    // The input/label container
    var cont = document.createElement("div");
    cont.setAttribute("id", inpName);
    cont.setAttribute("class", "form-group");

    // The label associated with the input
    var lbl = document.createElement("label");
    lbl.setAttribute("class", "col-lg-2 control-label");
    lbl.innerHTML = lblText;
    cont.appendChild(lbl);

    // The input container
    var inpCont = document.createElement("div");
    inpCont.setAttribute("class", "col-lg-10");

    // The input
    var inp = document.createElement("input");
    if (inpType != "") {
        inp.setAttribute("type", inpType);
    }
    if (inpClass != "") {
        inp.setAttribute("class", inpClass);
    }
    if (inpName != "") {
        inp.setAttribute("name", inpName);
    }
    if (inpValue != "") {
        inp.setAttribute("value", inpValue);
    }
    if (!enabled) {
        inp.setAttribute("disabled", "disabled");
    }

    inp.setAttribute("onchange", "update(this)");
    inpCont.appendChild(inp);
    cont.appendChild(inpCont);

    return cont;
}

function createAnchor(href, id, text) {
    var anchor = document.createElement("a");
    anchor.setAttribute("href", href);
    if (id != "") {
        anchor.setAttribute("id", id);
    }
    anchor.innerHTML = text;

    return anchor;
}

function createListItem(href, id, text) {
    var itemCont = document.createElement("li");
    itemCont.setAttribute("id", id);
    itemCont.appendChild(createAnchor(href, "", text));
    return itemCont;
}

function createMenuItem(sellerID) {
    var newItemNumber = numberOfItems[sellerID];
    var subMenu = document.getElementById("lnkitems" + sellerID);

    if (subMenu == undefined) {
        var newListItem = document.createElement("li");

        // Add link to seller
        newListItem.appendChild(createAnchor("#seller" + sellerID, "", "Seller " + (sellerID + 1)));

        // Add sublist
        var sublist = document.createElement("ul");
        sublist.setAttribute("class", "nav");
        sublist.setAttribute("id", "lnkitems" + sellerID);

        // Add link to item, totals and other
        sublist.appendChild(createListItem("#" + sellerID + "item" + newItemNumber, "lnk" + sellerID + "item" + newItemNumber, "Item " + (newItemNumber + 1)));
        sublist.appendChild(createListItem("#totals" + sellerID, "lnktotals" + sellerID, "Totals"));
        sublist.appendChild(createListItem("#other" + sellerID, "lnkother" + sellerID, "Other"));

        newListItem.appendChild(sublist);
        addListItem("sideMenu", newListItem, "lnkcustom");
    }
    else {
        var newListItem = createListItem("#" + sellerID + "item" + newItemNumber, "lnk" + sellerID + "item" + newItemNumber, "Item " + (newItemNumber + 1));
        var target = document.getElementById("lnkBA"+sellerID);
        var targetID = "lnkBA" + sellerID;
        if (target == null) {
            target = document.getElementById("lnktotals" + sellerID);
            targetID = "lnktotals" + sellerID;
        }
        addListItem("lnkitems" + sellerID, newListItem, targetID);
    }
}

function createBALink (sellerID) {
    var listItem = createListItem("#BA" + sellerID, "lnkBA" + sellerID, "Billing Agreement");
    addListItem("lnkitems"+sellerID, listItem, "lnktotals" + sellerID);
    
}

function createSeller(sellerID) {
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

    // Create and add the title
    var title = document.createElement("h3");
    title.innerHTML = "Seller " + (dispSellerID) + " <small>Specify the details associated with seller " + (dispSellerID) + " </small>";
    sellerCont.appendChild(title);

    // Create and add the option for line items.
    var sctLI = document.createElement("section");
    sctLI.setAttribute("id", "items" + sellerID);
    //sctLI.setAttribute("class", "hide");

    sellerCont.appendChild(sctLI);

    // Create and add the button to add more items
    var contAddBtn = document.createElement("h4");
    var addBtn = document.createElement("button");
    addBtn.setAttribute("class", "btn btn-primary add");
    addBtn.setAttribute("type", "button");
    addBtn.setAttribute("onclick", "createItem(" + sellerID + ")");
    var span = document.createElement("span");
    span.setAttribute("class", "glyphicon glyphicon-plus");
    addBtn.innerHTML = "Line Item ";
    addBtn.appendChild(span);

    contAddBtn.appendChild(addBtn);
    sellerCont.appendChild(contAddBtn);
    
    // Create and add the option for billing agreements
    var contAddBtn = document.createElement("h4");
    contAddBtn.setAttribute("id", "addBA" + sellerID);
    
    var addBtn = document.createElement("button");
    addBtn.setAttribute("class", "btn btn-primary add");
    addBtn.setAttribute("type", "button");
    addBtn.setAttribute("onclick", "createBA(" + sellerID + ")");
    var span = document.createElement("span");
    span.setAttribute("class", "glyphicon glyphicon-plus");
    addBtn.innerHTML = "Billing Agreement ";
    addBtn.appendChild(span);

    contAddBtn.appendChild(addBtn);
    sellerCont.appendChild(contAddBtn);

    // Create and add the totals  
    var hdrTot = document.createElement("h4");
    hdrTot.setAttribute("id", "totals" + sellerID);
    hdrTot.innerHTML = "Totals";
    sellerCont.appendChild(hdrTot);

    var itemTotal = createInput("PAYMENTREQUEST_" + sellerID + "_ITEMAMT", "Item", "text", "form-control", "", true);
    itemTotal.setAttribute("class","hide");
    sellerCont.appendChild(itemTotal);
    sellerCont.appendChild(createInput("PAYMENTREQUEST_" + sellerID + "_SHIPPINGAMT", "Shipping", "text", "form-control", "", true));
    sellerCont.appendChild(createInput("PAYMENTREQUEST_" + sellerID + "_HANDLINGAMT", "Handling", "text", "form-control", "", true));
    sellerCont.appendChild(createInput("PAYMENTREQUEST_" + sellerID + "_TAXAMT", "Tax", "text", "form-control", "", true));
    sellerCont.appendChild(createInput("PAYMENTREQUEST_" + sellerID + "_AMT", "Total", "text", "form-control", "", true));
    
    // Create and add the other information
    var hdrOth = document.createElement("h4");
    hdrOth.setAttribute("id", "other" + sellerID);
    hdrOth.innerHTML = "Other";
    sellerCont.appendChild(hdrOth);

    sellerCont.appendChild(createInput("PAYMENTREQUEST_" + sellerID + "_INVNUM", "Invoice Number", "text", "form-control", "", true));
    var pymtAct = new Array();
    pymtAct[0] = {value: "Sale", selected: true, disp: "Sale"};
    pymtAct[1] = {value: "Authorisation", selected: false, disp: "Authorisation"};
    pymtAct[2] = {value: "Order", selected: false, disp: "Order"};
    sellerCont.appendChild(createSelect("PAYMENTREQUEST_" + sellerID + "_PAYMENTACTION", "Payment Action", pymtAct, true));

    var currency = new Array();
    currency[0] = {value: "GBP", selected: true, disp: "GBP"};
    currency[1] = {value: "USD", selected: false, disp: "USD"};
    currency[2] = {value: "EUR", selected: false, disp: "EUR"};
    currency[3] = {value: "AUD", selected: false, disp: "AUD"};
    currency[4] = {value: "BRL", selected: false, disp: "BRL"};
    currency[5] = {value: "CAD", selected: false, disp: "CAD"};
    currency[6] = {value: "CZK", selected: false, disp: "CZK"};
    currency[7] = {value: "DKK", selected: false, disp: "DKK"};
    currency[8] = {value: "HKD", selected: false, disp: "HKD"};
    currency[9] = {value: "ILS", selected: false, disp: "ILS"};
    currency[10] = {value: "JPY", selected: false, disp: "JPY"};
    currency[11] = {value: "MYR", selected: false, disp: "MYR"};
    currency[12] = {value: "MXN", selected: false, disp: "MXN"};
    currency[13] = {value: "NOK", selected: false, disp: "NOK"};
    currency[14] = {value: "NZD", selected: false, disp: "NZD"};
    currency[15] = {value: "PHP", selected: false, disp: "PHP"};
    currency[16] = {value: "PLN", selected: false, disp: "PLN"};
    currency[17] = {value: "SGD", selected: false, disp: "SGD"};
    currency[18] = {value: "SEK", selected: false, disp: "SEK"};
    currency[19] = {value: "CHF", selected: false, disp: "CHF"};
    currency[20] = {value: "TWD", selected: false, disp: "TWD"};
    currency[21] = {value: "THB", selected: false, disp: "THB"};
    currency[22] = {value: "TRY", selected: false, disp: "TRY"};

    sellerCont.appendChild(createSelect("PAYMENTREQUEST_" + sellerID + "_CURRENCYCODE", "Currency", currency, true));

    sellers.appendChild(sellerCont);
    
    document.getElementsByName("PAYMENTREQUEST_" + sellerID + "_AMT")[0].setAttribute("readonly","");
    // Add items at the end.
    createItem(sellerID);
    
    numberOfSellers ++;
    /*
     newHTML = "<div class=\"lineItemContainer\">";
     newHTML += "<div class=\"row\"><div class=\"span12\"><p class=\"lead\">Seller " + (sellerID + 1) +"</p></div></div>"
     newHTML += " <div class=\"row\"> <div class=\"span12\" id=\"seller" + sellerID + "\">";
     // Header
     newHTML += "<div class=\"row table-header\" id=\"seller" + sellerID + "header\">";
     newHTML += "<div class=\"span2\"id=\"itemDetails" + sellerID + "\"><em>Item Details</em></div>";
     newHTML += "<div class=\"span1\" id=\"add" + sellerID + "\">";
     newHTML += "<button class=\"btn btn-mini\" type=\"button\" onclick=\"addItem(" + sellerID + ")\"><i class=\"icon-plus\"></i> item</button></div>";
     newHTML += "<div class=\"span2\" style=\"padding-bottom:10px\"><em>Other Details</em></div>";
     newHTML += "</div>";
     // Item
     newHTML += "<div class=\"row\"> <div class=\"span3\" id=\"items" + sellerID + "\">";
     newHTML += "<div class=\"span2\" id=\"" + sellerID + "item0\">";
     newHTML += "<div class=\"row\"><p><strong>Item 1</strong></p></div>";
     newHTML += addInput(sellerID, 0, "NAME", "Name", "update(false," + sellerID + ")");
     newHTML += addInput(sellerID, 0, "DESC", "Description", "update(false," + sellerID + ")");
     newHTML += addInput(sellerID, 0, "QTY", "Quantity", "update(true," + sellerID + ")");
     newHTML += addInput(sellerID, 0, "AMT", "Amount", "update(true," + sellerID + ")");
     newHTML += "</div> </div>";
     // Totals
     newHTML += "<div class=\"span5\">  <div class=\"span2\">";
     newHTML += "<div class=\"row\"><p><strong>Totals</strong></p></div>";
     newHTML += "<div class=\"row\"><input class=\"money\" type=\"text\" id=\"PAYMENTREQUEST_" + sellerID +"_SHIPPINGAMT\" name=\"PAYMENTREQUEST_" + sellerID +"_SHIPPINGAMT\" onchange=\"update(true," + sellerID + ")\" placeholder=\"Shipping\" /></div>";
     newHTML += "<div class=\"row\"><input class=\"money\" type=\"text\" id=\"PAYMENTREQUEST_" + sellerID + "HANDLINGAMT\" name=\"PAYMENTREQUEST_" + sellerID + "_HANDLINGAMT\" onchange=\"update(true," + sellerID + ")\" placeholder=\"Handling\" /></div>";
     newHTML += "<div class=\"row\"><input class=\"money\" type=\"text\" id=\"PAYMENTREQUEST_" + sellerID + "_TAXAMT\" name=\"PAYMENTREQUEST_" + sellerID + "_TAXAMT\" onchange=\"update(true," + sellerID + ")\" placeholder=\"Tax\" /></div>";
     newHTML += "<div class=\"row\"><input class=\"money\" type=\"text\" id=\"PAYMENTREQUEST_" + sellerID +"_AMT\" name=\"PAYMENTREQUEST_" + sellerID +"_AMT\" placeholder=\"Total\" disabled/></div>";
     newHTML += "</ div></div>";
     // The Rest
     newHTML += "<div class=\"span2\"> <div class=\"row\"><p><strong>The Rest</strong></p></div>";
     newHTML += "<div class=\"row\"><input type=\"text\" id=\"PAYMENTREQUEST_" + sellerID + "_INVNUM\" name=\"PAYMENTREQUEST_" + sellerID + "_INVNUM\" placeholder=\"Invoice Number\" /></div>";
     newHTML += "<div class=\"row\"> <div class=\"controls\" style=\"display: inline-block\">";
     newHTML += addCurrency(sellerID);
     newHTML += "<div class=\"row\"> <div class=\"controls\" style=\"display: inline-block\">";
     newHTML += "<select class=\"small\"name=\"PAYMENTREQUEST_" + sellerID + "_LOCALECODE\">";
     newHTML += "<option value=\"GB\" selected=\"selected\">GB</option> <option value=\"DE\">DE</option></select>";
     // Final divs
     newHTML += "</div> </div> </div> </div> </div> <div> </div> </div> ";
     
     sellers.innerHTML += newHTML;*/

}

function addListItem(menuID, listItem, targetID) {
    var menu = document.getElementById(menuID);
    var target = document.getElementById(targetID);
    menu.insertBefore(listItem, target);
    $('[data-spy="scroll"]').each(function () {
        var $spy = $(this).scrollspy('refresh')
    })
}




