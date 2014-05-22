/* 
 * Contains all the functions used to create HTML elements
 */
function createSideMenu() {
    var toc = document.getElementById("toc");
    if (toc.hasChildNodes()) {
        toc.removeChild(toc.childNodes[0]);
    }
    // Set up the skeleton
    var sideMenu = document.createElement("div");
    sideMenu.setAttribute("class", "sidebar affix");
    var nav = document.createElement("ul");
    nav.setAttribute("class", "nav sidenav");
    nav.setAttribute("id", "sideMenu");
    //var mainForm = document.getElementsByTagName("form")[0];
    var h3s = document.getElementsByTagName("h3");
    var firstParent = true;
    for (var inc = 0; inc < h3s.length; inc++) {
        var h3 = h3s[inc];
        var id = h3.getAttribute("id");
        var lstParent = createListItem(id, getMainTitle(h3));
        if (firstParent) {
            lstParent.setAttribute("class", "active");
            firstParent = false;
        }
        var parent = h3.parentNode;
        var children = parent.childNodes;
        var lstChild = h4Search(children, true);

        if (lstChild != -1) {
            lstParent.appendChild(lstChild);
        }
        nav.appendChild(lstParent);
    }
    sideMenu.appendChild(nav);
    sideMenu.appendChild(document.createElement("br"));

    var btn = document.createElement("button");
    btn.setAttribute("id", "checkout");
    btn.setAttribute("class", "btn btn-link");
    btn.setAttribute("onclick", "submitForm()");
    btn.setAttribute("type", "button");

    var img = document.createElement("img");
    img.setAttribute("src", "./img/PP_Buttons_CheckOut_195x37_v3.png");

    btn.appendChild(img);

    sideMenu.appendChild(btn);

    toc.appendChild(sideMenu);
    $('[data-spy="scroll"]').each(function() {
        var $spy = $(this).scrollspy('refresh')
    })
}

function getMainTitle(elmt) {
    var child = elmt.firstChild.textContent;
    return child;
}

function h4Search(children, first) {
    // Regex for item containers
    var itemCont = /^items/;
    // Regex for items
    var item = /\ditem\d/;
    for (var inc = 0; inc < children.length; inc++) {
        var child = children[inc];
        if (child.nodeType == 1) {
            var id = child.getAttribute("id");
            //alert(id + ": " + itemCont.test(id));
            if (itemCont.test(id)) {
                var items = child.childNodes;
                //alert(items.length);
                for (var innerInc = 0; innerInc < items.length; innerInc++) {
                    var item = items[innerInc];
                    var id = item.getAttribute("id");
                    
                    var heading = item.childNodes[0];
                    var childClass = heading.firstChild;
                    
                    if (childClass == "[object Text]") {
                        var text = heading.textContent;
                        if (first) {
                            var lstChild = document.createElement("ul");
                            lstChild.setAttribute("class", "nav");
                            first = false;
                        }
                        lstChild.appendChild(createListItem(id, text));
                    }
                }
            }
            var tag = child.tagName;
            if (tag == "H4") {
                var childClass = child.childNodes[0];
                //alert(id + ": " + childClass);
                if (childClass == "[object Text]") {
                    var text = child.textContent;
                    if (first) {
                        var lstChild = document.createElement("ul");
                        lstChild.setAttribute("class", "nav");
                        first = false;
                    }
                    lstChild.appendChild(createListItem(id, text));
                }
            }
        }
    }

    if (!first) {
        return lstChild;
    } else {
        return -1;
    }

}

function createListItem(href, text) {
    var itemCont = document.createElement("li");
    itemCont.appendChild(createAnchor(href, text));
    return itemCont;
}

function createAnchor(href, text) {
    var anchor = document.createElement("a");
    anchor.setAttribute("href", "#" + href);
    anchor.innerHTML = text;
    return anchor;
}

function addListItem(menuID, listItem, targetID) {
    var menu = document.getElementById(menuID);
    var target = document.getElementById(targetID);
    menu.insertBefore(listItem, target);
    $('[data-spy="scroll"]').each(function() {
        var $spy = $(this).scrollspy('refresh')
    })
}