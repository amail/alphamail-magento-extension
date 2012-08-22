// AJAX GET
function ajaxGET(url, fn) {
    var AJAX = null;
    if (window.XMLHttpRequest) { AJAX = new XMLHttpRequest(); } else { AJAX = new ActiveXObject("Microsoft.XMLHTTP"); }
    if (AJAX == null) { return false; }

    AJAX.onreadystatechange = function() {
        if (AJAX.readyState == 4 || AJAX.readyState == "complete") {
            if (AJAX.status == 200) {
                fn(AJAX.responseText);
            }
        }
    }

    AJAX.open("GET", url, true);
    AJAX.send(null);
}

// AJAX POST
function ajaxPOST(url, params, fn) {
    var AJAX = null;
    if (window.XMLHttpRequest) { AJAX = new XMLHttpRequest(); } else { AJAX = new ActiveXObject("Microsoft.XMLHTTP"); }
    if (AJAX == null) { return false; }

    AJAX.onreadystatechange = function() {
        if (AJAX.readyState == 4 || AJAX.readyState == "complete") {
            if (AJAX.status == 200) {
                fn(AJAX.responseText);
            }
        }
    }
     
    AJAX.open("POST", url, true);
    
    //Send the proper header information along with the request
    AJAX.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    AJAX.setRequestHeader("Content-length", params.length);
    AJAX.setRequestHeader("Connection", "close");
    
    AJAX.send(params);
}
