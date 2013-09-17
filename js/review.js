// Review.js used in Review.php to load order details
var numberOfItems = 1;
// Create a form with hidden inputs to post to DoEC
function postTableData(input) {
    var titles = Object.keys(input);
    var inputLength = titles.length;
    var form = document.getElementById("form");
   /* for (var inc = 0; inc < inputLength; inc++) {
        document.write(titles[inc] + " => " + input[titles[inc]] + "<br />");
    }*/
    for (var inc = 0; inc < inputLength; inc++) {
        var cur = titles[inc];
        form.appendChild(addHiddenInp(cur, input[cur]));
    }

}

// Used in postTableData to add a hidden input box
function addHiddenInp(name, val) {
    var inp = document.createElement("input");
    inp.setAttribute("name", name);
    inp.setAttribute("type", "hidden");
    inp.setAttribute("value", val);
    return inp;
}

function addElmt(name, val) {
    var items = document.getElementById("items");
    var elmt = document.getElementById(name);
    if (elmt != null) { elmt.innerHTML = val; }
    else {
        if ((name.match(/^L_PAYMENTREQUEST_/)) && (isItemDetail(name))) {
            var ids = name.match(/\d/);
            var sellerID  = ids[0];
            var itemID = name.slice(-1);
            items.appendChild(addItem(sellerID, itemID));
            numberOfItems++;
            addElmt(name, val);
        }
    }
    
}

function isItemDetail(name) {
    if(name.match(/_NAME/)) {return true;}
    else if(name.match(/_DESC/)) {return true;}
    else if(name.match(/_QTY/)) {return true;}
    else if(name.match(/_AMT/)) {return true;}
    else { return false;}
}

function addItem(sellerID, itemID) {
    var row = document.createElement("tr");
    row.appendChild(addColumn("", parseInt(sellerID) + 1));
    row.appendChild(addColumn("L_PAYMENTREQUEST_" + sellerID + "_NAME" + itemID, ""));
    row.appendChild(addColumn("L_PAYMENTREQUEST_" + sellerID + "_DESC" + itemID, ""));
    row.appendChild(addColumn("L_PAYMENTREQUEST_" + sellerID + "_QTY" + itemID, ""));
    row.appendChild(addColumn("L_PAYMENTREQUEST_" + sellerID + "_AMT" + itemID, ""));
    row.appendChild(addColumn("total" + numberOfItems, ""));
    return row;
}

function addColumn(id, val) {
    var col = document.createElement("td");
    if (val != "") { col.innerHTML = val; }
    if (id != "") {col.setAttribute("id", id); }
    return col;
}

function cancel() {
   window.location ="http://localhost/Gateways/index.php";
}
