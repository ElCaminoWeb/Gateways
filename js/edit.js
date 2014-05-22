/* 
 * Contains all the functions used to update and remove HTML elements
 */

/*
 * Update functions
 */

function updateValue(name, value) {
    var inp = document.getElementsByName(name)[0];
    inp.setAttribute("value", value);
}

