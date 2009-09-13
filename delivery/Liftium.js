/* Ad Network Optimizer written in Javascript */

if (typeof Liftium == "undefined" ) { // No need to do this twice

var Liftium = {
	chain 		: [],
	errors 		: []
};

/* ##### Methods are in alphabetical order, with a call to Liftium.init at the bottom */


/* Simple convenience function for getElementById */
Liftium._ = function(id){
        return document.getElementById(id);
};


/* Simple abstraction layer for event handling across browsers */
Liftium.addEventListener = function(item, eventName, callback){
        // TODO: use jQuery if it's available
        if (window.addEventListener) { // W3C
                return item.addEventListener(eventName, callback, false);
        } else if (window.attachEvent){ // IE 
                return item.attachEvent("on" + eventName, callback);
        }
};

Liftium.beaconCall = function (url){
        // Create an image and call the beacon
        var img = new Image(0, 0);
        // Append a cache buster
        img.src = url + '&cb=' + Math.random().toString().substring(2,8);
};


/* Do the work of calling the ad tag */
Liftium.callAd = function (slotname, iframe) {
	Liftium.d("Calling ad for " + slotname, 1);
	document.write("This is my rifle: " + slotname);
};


/* Send a message to the debug console if available, otherwise alert */
Liftium.debug = function (msg, level){
        if (Liftium.e(Liftium.debugLevel)){
                return false;
        } else if (level > Liftium.debugLevel){
                return false;
        }

        // Firebug enabled
        if (typeof console == "object" && console.firebug){
                console.log("Liftium: " + msg);
                if (arguments.length > 2){
                        console.dir(Liftium.d.arguments[2]);
                }
        // Yahoo logging console
        } else if (typeof YAHOO == "object" && YAHOO.log){
                YAHOO.log(msg, "info", "Liftium");
                if (arguments.length > 2){
                        YAHOO.log(Liftium.print_r(Liftium.d.arguments[2]), "info", "Liftium");
                }
	// Default console, available on IE 8+, FF 3+ Safari 4+
        } else if (typeof console == "object" && console.log){
                console.log("Liftium: " + msg);
                if (arguments.length > 2){
                        console.log(Liftium.print_r(Liftium.d.arguments[2]));
                }
        } else {
                alert("Liftium debug: " + msg);
        }

        return true;
};
Liftium.d = Liftium.debug; // Shortcut to reduce size of JS

Liftium.getRequestVal = function(varName, defaultVal, qstring){
        var nvpairs = Liftium.parseQueryString(qstring || document.location.search);
        if (typeof nvpairs[varName] != "undefined"){
                return nvpairs[varName];
        } else if (typeof defaultVal != "undefined" ) {
                return defaultVal;
        } else {
                return '';
        }
};


Liftium.getUniqueSlotname = function(slotname) {
	for (var i = 0; i < 10; i++ ) {
		if (Liftium._(slotname + "_" + i) === null){
			return slotname + "_" + i;
		}
	}

	throw ("Error in Liftium.getUniqueSlotname. More than 10 ads of the same size?");
};

/* By default, javascript passes by value, UNLESS you are passing a javascript
 * object, then it passes by reference.
 * Yes, I could have extended object prototype, but I hate it when people do that */
Liftium.clone = function (obj){
        if (typeof obj == "object"){
                var t = new obj.constructor();
                for(var key in obj) {
                        t[key] = Liftium.clone(obj[key]);
                }

                return t;
        } else {
                // Some other type (null, undefined, string, number)
                return obj;
        }
};


Liftium.cookie = function(name, value, options) {
    if (arguments.length > 1) { // name and value given, set cookie
        options = options || {};
        if (Liftium.e(value)) {
            value = '';
            options.expires = -1;
        }
        var expires = '';
        if (options.expires && (typeof options.expires == 'number' || options.expires.toUTCString)) {
            var d;
            if (typeof options.expires == 'number') {
                d = new Date();
                d.setTime(d.getTime() + (options.expires));
            } else {
                d = options.expires;
            }
            expires = '; expires=' + d.toUTCString(); // use expires attribute, max-age is not supported by IE
        }
        // CAUTION: Needed to parenthesize options.path and options.domain
        // in the following expressions, otherwise they evaluate to undefined
        // in the packed version for some reason...
        var path = options.path ? '; path=' + (options.path) : '';
        var domain = options.domain ? '; domain=' + (options.domain) : '';
        var secure = options.secure ? '; secure' : '';
        return document.cookie = [name, '=', encodeURIComponent(value), expires, path, domain, secure].join('');
    } else { // only name given, get cookie
        var cookieValue = null;
        if (!Liftium.e(document.cookie)){
            var cookies = document.cookie.split(';');
            for (var i = 0, l = cookies.length; i < l; i++) {
                var cookie = cookies[i].replace( /^\s+|\s+$/g, "");
                // Does this cookie string begin with the name we want?
                if (cookie.substring(0, name.length + 1) == (name + '=')) {
                    cookieValue = decodeURIComponent(cookie.substring(name.length + 1));
                    break;
                }
            }
        }
        return cookieValue;
    }
};


/* Emulate php's empty(). Thanks to:
 * http://kevin.vanzonneveld.net/techblog/article/javascript_equivalent_for_phps_empty/
 * Nick wrote: added the check for number that is NaN
 */
Liftium.empty = function ( v ) {
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
    }
    return false;
};
Liftium.e = Liftium.empty; // Shortcut to make the Javascript smaller

Liftium.init = function () {
        Liftium.now = new Date();
        Liftium.startTime = Liftium.now.getTime();
        Liftium.debugLevel = Liftium.getRequestVal('liftium_debug', 0);
};


/* This code looks at the supplied query string and parses it.
 * It returns an associative array of url decoded name value pairs
 */
Liftium.parseQueryString = function (qs){
        var ret = [];
        if (Liftium.e(qs)) { return ret; }

        if (qs.charAt(0) === '?') { qs = qs.substr(1); }


        qs=qs.replace(/\;/g, '&', qs);

        var nvpairs=qs.split('&');

        for (var i = 0, intIndex; i < nvpairs.length; i++){
                if (nvpairs[i].length === 0){
                        continue;
                }

                var varName = '', varValue = '';
                if ((intIndex = nvpairs[i].indexOf('=')) != -1) {
                        varName = decodeURIComponent(nvpairs[i].substr(0, intIndex));
                        varValue = decodeURIComponent(nvpairs[i].substr(intIndex + 1));
                } else {
			// No value, but it's there
                        varName = nvpairs[i];
                        varValue = true;
                }

                ret[varName] = varValue;
        }

        return ret;
};      

Liftium.print_r = function (data, level) {
	
	if (data === null) { return "<<null>>"; }

        // Sanity check against too much recursion
	level = level || 0;
        if (level > 6) { return false; }

        //The padding given at the beginning of the line.
	var padding = '';
        for(var j = 1; j < level+1 ; j++) {
                padding += "    ";
        }
	switch (typeof data) {
	  case "string" : return data === "" ? "<<empty string>>" : data;
	  case "undefined" : return "<<undefined>>";
	  case "boolean" : return data === true ? "<<true>>" : "<<false>>";
	  case "function" : return "<<" + "function" + ">>";
	  case "object" : // The fun one

		var out = [];
                for(var item in data) {

                        if(typeof data[item] == 'object') { 
                                out.push(padding + "'" + item + "' ..." + "\n");
                                out.push(Liftium.print_r(data[item],level+1));
                        } else {
                                out.push(padding + "'" + item + "' => \"" + Liftium.print_r(data[item]) + "\"\n");
                        }
                }
		if (Liftium.e(out)){
			return "<<empty object>>";
		} else {
			return out.join("");
		}

	  default : return data.toString();
	}
};

// Start your optimization!
Liftium.init();

} // \if (typeof Liftium == "undefined" ) 
