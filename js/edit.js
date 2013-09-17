/* 
 * Contains all the functions used to update and remove HTML elements
 */

/*
 * Update functions
 */

function update(elmt) {
    var errMsg = "";
    var name = elmt.name;
    var val = elmt.value;
    var len = val.length;
    var start = "<p id=\"" + name + "_ERR\">";
    switch (elmt.name) {
        case "GATEWAY":
            supported(val);
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
}
function updateValue(name, value) {
    var inp = document.getElementsByName(name)[0];
    inp.setAttribute("value", value);
}


function updateLogo(oldLogo) {
    var name = oldLogo.getAttribute("id");
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
        value = "https://www.paypal-business.co.uk/merchantservices/toolkit/images/bnr_merchant_468x60.gif";
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

    removeListItem("lnkitems" + sellerID,"lnk" + sellerID + "item" + itemID);
      
    numberOfItems[sellerID]--;
   // alert(numberOfItems[sellerID]);
    if (numberOfItems[sellerID] > 0) {
        updateItems(sellerID);
    } else {
        noItems(true);
    }
    updateTotal();
}

function removeBA(sellerID) {
    var sellerCont = document.getElementById("seller" + sellerID);
    var BACont = document.getElementById("BA" + sellerID)
    sellerCont.removeChild(BACont);
    document.getElementById("addBA"+ sellerID).setAttribute("class","");
    
    removeListItem("lnkitems" + sellerID, "lnkBA" + sellerID);
    
}

function removeSA() {
    var cont = document.getElementById("customise");
    var SACont = document.getElementById("SACont")
    cont.removeChild(SACont);
    document.getElementById("addAddress").setAttribute("class","");
    
    overrideAddress(false);
}

function removeListItem(menuID, listItemID) {
    var menu = document.getElementById(menuID);
    var listItem = document.getElementById(listItemID);
    
    menu.removeChild(listItem);
    
    $('[data-spy="scroll"]').each(function () {
        var $spy = $(this).scrollspy('refresh')
    })
    
}
