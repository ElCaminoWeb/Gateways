/* 
 * Functions no longer in use but may be useful in the future
 */

function setError(errorMessage) {
    document.getElementById("errorBox").setAttribute("style", "visibility:visible");
    document.getElementById("errorBox").innerHTML += decodeURIComponent(errorMessage);
    window.location.hash = "errorBox";
}

function removeError(id) {
    var errID = id + "_ERR";
    var err = document.getElementById(errID);
    if (err != null) {
        var errBox = document.getElementById("errorBox")
        errBox.removeChild(err);
        if (errBox.innerHTML == "") {
            errBox.setAttribute("style", "visibility:hidden; display:none;");
        }
    }
    window.location.hash = document.getElementById(id).parentNode.getAttribute("id");
}

/*var menu = document.getElementById("sideMenu");
    var menuItems = menu.childNodes;
    
    var target = "lnkitems" + sellerID;
    var realTarget = sellerID + "item" + itemID;
    
    
    for (var inc = 0; inc < menuItems.length; inc++) {
        var menuItem = menuItems[inc];
        var children = menuItem.childNodes;
        for (var child = 0; child < children.length; child++) {
            var id = children[child].id;
            // alert(id);
            if (id == target) {
                //alert(children[child]);
                var gchildren = children[child].childNodes;
                //alert(gchildren.length);
                for (var gchild = 0; gchild < gchildren.length; gchild++) {
                    // alert(gchildren[gchild].textContent);
                    if (gchildren[gchild].textContent == realTarget) {
                        children[child].removeChild(gchildren[gchild]);
                    }
                }

            }
        }
    }
    */



function checkEmpty() {
    var paymentReq = "L_PAYMENTREQUEST_" + sellerID + "_";
    var errorBox = document.getElementById("errorBox");
    var cont = true;
    var error = false;
    var itemNo = 0;
    var options = ["NAME", "DESC", "QTY", "AMT"];
    var reply = ["name", "description", "quantity", "amount"];
    var errorMessage = "<p><strong>Seller " + (sellerID + 1) + ": </strong></p>";
    var errorNo = 0;
    while (cont) {
        var item = document.getElementById(sellerID + "item" + itemNo);
        if ((typeof (item) == 'undefined') || (item == null)) {
            cont = false;
            itemNo++;
            break;
        }
        item.innerHTML = updateHTML(itemNo, sellerID);
        errorMessage += "You need to specifiy the ";
        for (var inc = 0; inc < 4; inc++) {
            var ID = paymentReq + options[inc] + itemNo;
            var option = document.getElementById(ID);
            if (option.value == "") {
                error = true;
                if (errorNo == 0) {
                    errorMessage += reply[inc];
                }
                else {
                    errorMessage += ", " + reply[inc];
                }
                errorNo++;
            }
        }
        if (error) {
            errorMessage += " for item number " + (itemNo + 1).toString() + "<br />";
        }
        itemNo++;
        errorNo = 0;
    }
    if (error) {
        errorBox.setAttribute("style", "visibility:visible;");
        errorBox.innerHTML = errorMessage;
        return -1;
    } else {
        errorBox.setAttribute("style", "visibility:hidden; display:none;");
        errorBox.innerHTML = "";
        return itemNo;
    }
}

function updateHTML(itemNo, sellerID) {
    //  alert(oldHTML);
    var item = document.getElementById(sellerID + "item" + itemNo);
    var oldHTML = item.innerHTML;
    var options = ["NAME", "DESC", "QTY", "AMT"];
    var paymentReq = "L_PAYMENTREQUEST_" + sellerID + "_";
    var start = [-1, -1, -1, -1];
    var newHTML = new Array();
    for (var inc = 0; inc < 4; inc++) {
        var ID = paymentReq + options[inc] + itemNo;
        var value = document.getElementById(ID).value;
        if ((typeof (value) == 'undefined') || (value == null)) {
            break;
        }
        var pos = oldHTML.search(ID);
        start[inc] = pos + ID.length + 1;

        newHTML[inc] = " value=\"" + value + "\" ";
    }

    var html = "";

    for (var inc = 0; inc < oldHTML.length; inc++) {
        switch (inc) {
            case start[0]:
                html += newHTML[0];
                break;
            case start[1]:
                html += newHTML[1];
                break;
            case start[2]:
                html += newHTML[2];
                break;
            case start[3]:
                html += newHTML[3];
                break;
            default:
                html += oldHTML[inc];
        }
    }
    return html;
}


