// Discarded functions
/* function addInput(type, name, id, placeholder, onchange) {
var inp = document.createElement("input")
inp.setAttribute("type", type);
inp.setAttribute("name", name);
inp.setAttribute("id", id);
inp.setAttribute("placeholder", placeholder);
inp.setAttribute("class", "itemDetail");
if (onchange != "") { inp.setAttribute("onchange", onchange); }
return inp;
}

function addItem(sellerNumber) {
var numberOfItems = checkEmpty(sellerNumber);
if (numberOfItems == -1) { return; }
var itemNumber = numberOfItems - 1;
var items = document.getElementById("items" + sellerNumber);
var item = document.getElementById("newItem" + sellerNumber);
item.setAttribute("id", sellerNumber + "item" + itemNumber);
item.setAttribute("style", "visibility:visible;");
item.setAttribute("class", "itemContainer");
item.innerHTML = "";
     
var title = document.createElement("p");
title.innerHTML = "<strong>Item " + (itemNumber + 1) + "</strong>";
item.appendChild(title);

var paymentReq = "L_PAYMENTREQUEST_" + sellerNumber + "_";
var options = ["NAME", "DESC", "QTY", "AMT"];
var placeholders = ["Name", "Description", "Quantity", "Amount"];
var onchanges = ["", "", "updateTotal()", "updateTotal()"];
for (var inc = 0; inc < 4; inc ++) {
var name = paymentReq + options[inc] + itemNumber;
item.appendChild(addInput("text", name, name, placeholders[inc], onchanges[inc]));
}
var newItem = document.createElement("div");
newItem.setAttribute("id", "newItem" + sellerNumber);
newItem.setAttribute("style", "visibility:hidden;");
items.appendChild(newItem);
} */