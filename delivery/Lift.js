/* Ad Network Optimizer written in Javascript */

var Lift = {

};

/* ####### Methods are in alphabetical order. ####### */

/* Simple convenience function for getElementById */
Lift._ = function(id){
        return document.getElementById(id);
};


/* Simple abstraction layer for event handling across browsers */
Lift.addEventListener = function(item, eventName, callback){
        // TODO: use jQuery if it's available
        if (window.addEventListener) { // W3C
                return item.addEventListener(eventName, callback, false);
        } else if (window.attachEvent){ // IE 
                return item.attachEvent("on" + eventName, callback);
        }
};

Lift.beaconCall = function (url){
        // Create an image and call the beacon
        var img = new Image(0, 0);
        // Append a cache buster
        img.src = url + '&cb=' + Math.random().toString().substring(2,8);
};


/* By default, javascript passes by value, UNLESS you are passing a javascript
 * object, then it passes by reference.
 * Yes, I could have extended object prototype, but I hate it when people do that */
Lift.clone = function (obj){
        if (typeof obj == "object"){
                var t = new obj.constructor();
                for(var key in obj) {
                        t[key] = Lift.clone(obj[key]);
                }

                return t;
        } else {
                // Some other type (null, undefined, string, number)
                return obj;
        }
};


/* Emulate php's empty(). Thanks to:
 * http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_empty/
 * Nick wrote: added the check for empty arrays
 * Nick wrote: added the check for number that is NaN
 */
Lift.empty = function ( v ) {
    if (v === "" ||
        v === 0 ||
        v === null ||
        v === false ||
        typeof v === "undefined" ||
        (typeof v === "number" && isNaN(v))){
        return true;
    } else if (typeof v === 'object') {
        for (var key in v) {
              return false;
        }
        return true;
    } else if (typeof v === 'array' && v.length === 0) {
        return true;
    }
    return false;
};
Lift.e = Lift.empty; // Shortcut to make the Javascript smaller


/* Javascript equivalent of php's print_r. 
 * http://www.openjs.com/scripts/others/dump_function_php_print_r.php
 */
Lift.print_r = function (arr,level) {
        var text = ["\n"], padding = "";
        if(!level) { level = 0; }

        // saving a crash if you try to do something silly like print_r(top);
        if (level > 6) { return false; }

        //The padding given at the beginning of the line.
        for(var j = 0; j < level+1 ; j++) {
                padding += "    ";
        }

        if(typeof arr  == 'object') { //Array/Hashes/Objects 
                for(var item in arr) {
                        var value = arr[item];

                        if(typeof value == 'object') { //If it is an array,
                                text.join(padding + "'" + item + "' ...");
                                text.join(Lift.print_r(value,level+1));
                        } else {
                                text.join(padding + "'" + item + "' => \"" + value + "\"\n");
                        }
                }
        } else { //Stings/Chars/Numbers etc.
                text = ["===>"+arr+"<===("+typeof(arr)+")"];
        }
        return text.join("");
};


