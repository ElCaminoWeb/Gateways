/* 
 * Contains all the functions used to create HTML elements
 */

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
    searchCont.setAttribute("class","input-group");
    
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
    searchBtnCont.setAttribute("class","input-group-btn");
    
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


function createAnchor(href, id, text) {
    var anchor = document.createElement("a");
    anchor.setAttribute("href", href);
    if (id != "") {
        anchor.setAttribute("id", id);
    }
    anchor.innerHTML = text;

    return anchor;
}

function createPopover(infId) {
    var infCont = document.createElement("div");
    infCont.setAttribute("class", "col-lg-1");

    var inf = document.createElement("button");
    inf.setAttribute("class", "btn btn-link");
    inf.setAttribute("type", "button");
    inf.setAttribute("name", infId);
    inf.setAttribute("rel", "popover");

    var icon = document.createElement("span");
    icon.setAttribute("class", "glyphicon glyphicon-info-sign");

    inf.appendChild(icon);
    infCont.appendChild(inf);

    return infCont;
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

function makePopovers(desc) {
    var names = Object.keys(desc);
    for (var inc = 0; inc < names.length; inc++) {
        $("[name=inf" + names[inc] + "]").popover({
            title: names[inc],
            content: desc[names[inc]],
            placement: 'left',
            html: 'true',
            trigger: 'hover'
        });
    }
}

function getFields() {
    /*var url = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20html%20where%20url%3D%22https%3A%2F%2Fdeveloper.paypal.com%2Fwebapps%2Fdeveloper%2Fdocs%2Fclassic%2Fapi%2Fmerchant%2FSetExpressCheckout_API_Operation_NVP%2F%22%20and%20xpath%3D'%2F%2Ftable%2Ftr'&format=json&diagnostics=true&callback=";
     $.getJSON(url, cbfunc);
     //document.write(JSON.stringify(fields)); */
    var lines = [];
    var txtFile = new XMLHttpRequest();
    txtFile.open("GET", "https://localhost/Gateways/js/fields.txt", true);
    txtFile.onreadystatechange = function() {
        if (txtFile.readyState === 4) {  // Makes sure the document is ready to parse.
            if (txtFile.status === 200) {  // Makes sure it's found the file.
                var allText = txtFile.responseText;
                lines = txtFile.responseText.split("\n"); // Will separate each line into an array
                createDescArray(lines);
            }
        }
    }
    txtFile.send(null);
}

function createDescArray(lines) {
    // alert(lines.length);
    var lineInc = 0;
    var desc = new Object();
    var first = true;
    while (lineInc < lines.length) {
        // alert(lines[lineInc]);
        if (first) {
            var name = lines[lineInc].replace(/\s/g, "");
            first = false;
            var currDesc = "";
            lineInc++;
        }
        else if (lines[lineInc].search("__") == "0") {

            desc[name] = currDesc;
            // alert(desc[name]);
            first = true;
            lineInc += 2;
        } else {
            currDesc += "<p>" + lines[lineInc] + "</p>";
            lineInc++;
        }
    }
    makePopovers(desc);
}