/* Include of XDM.js */

/* This Toolkit allows for you to send messages between windows, including cross domain.
 * Ideas borrowed from http://code.google.com/p/xssinterface/, but rewritten from scratch.
 *
 * XDM has two methods for sending messages cross domain. The first of which uses
 * postMessages(), an HTML 5 javascript method.
 * As I write this, the following * browsers support postMessage
 * Firefox 3.0+
 * IE 8+
 * Safari 4+
 * Chrome 3+
 * Opera 9+
 *
 * For the rest of the browsers, we use a backward compatible hack that utilizes an
 * external html file that acts as a conduit for information - XDM.iframeUrl
 * This file is expected to be able to parse the query string and act upon
 * the parameters.
 */

var XDM = {
	allowedMethods : [],
	debugOn	   : false, // Print debug messages to console.log

	// These options only needed for the iframe based method,
	// for browsers that don't support postMessage
	iframeUrl      : "/liftium_iframe.html",
	postMessageEnabled : true // Set to false to force fallback method
};


/*
 * @param frame - the window object to execute the code in. Example: top, window.parent
 * @param method - the method to execute in the parent window. Note the other window has to be listening for it with XDMListen(), and the method must be in XDM.allowedMethods
 */ 
XDM.send = function (destWin, method, args){ 
	XDM.debug("XDM.send called from " + document.location.hostname);
	// Sanity checks
	if (typeof method != "string") {
		XDM.debug("Bad argument for XDM.send, 'method' is not a string, (" + typeof method + ")");
		return false;
	}
	if ( typeof args == "undefined" ){
		// Just set it to an empty array
		args = [];
	}

	if (XDM.canPostMessage()){
		return XDM._postMessage(destWin, method, args);
	} else {
		return XDM._postMessageWithIframe(destWin, method, args);
	}

};


XDM.getDestinationDomain = function(destWin){
	if (destWin == top){
		// Pull domain from referrer. 
		if (document.referrer.toString() !== ''){
			var m = document.referrer.toString().match(/https*:\/\/([^\/]+)/);
			XDM.debug("Hostname for destWin set to " + m[1] + " using referrer");
			return m[1];
		} else {
			return false;
		}
	} else {
		return destWin.location.hostname;
	}
};


XDM._postMessage = function(destWin, method, args) {
	XDM.debug("Sending message using postMessage()");
	var d = XDM.getDestinationDomain(destWin), targetOrigin;
	if (d === false){
		targetOrigin = '*';
	} else {
		targetOrigin = 'http://' + d;
	}
	

	var msg = XDM.serializeMessage(method, args);
	
	if(destWin.postMessage) { // HTML 5 Standard
		return destWin.postMessage(msg, targetOrigin);
	} else if(destWin.document.postMessage) { // Opera 9
		return destWin.document.postMessage(msg, targetOrigin);
	} else {
		throw ("No supported way of using postMessage");
	}
};


XDM._postMessageWithIframe = function(destWin, method, args) {
	XDM.debug("Sending message using iframe");
	if (XDM.iframeUrl === null) {
		XDM.debug("Iframe method called, but no html file is specified");
		return false;
	}
		

	var d = XDM.getDestinationDomain(destWin), targetOrigin;
	if (d === false){
		// No where to send 
		return false;
	} else {
		targetOrigin = 'http://' + d;
	}

        // Special hacks for different placements of the html file 
        if (d.match(/brighthub/)){
                XDM.iframeUrl = "/liftium_iframe.htm";
        }

	var iframeUrl = targetOrigin + XDM.iframeUrl + '?' + XDM.serializeMessage(method, args);
	XDM.debug("Calling iframe dispatch url: " + iframeUrl);
	
	if (typeof XDM.iframe == "undefined"){
		XDM.iframe = document.createElement("iframe");
		XDM.iframe.style.display = "none";
		XDM.iframe.width = 0;
		XDM.iframe.height = 0;
                if (document.body === null){
                        document.firstChild.appendChild(document.createElement("body"));
                }
		document.body.appendChild(XDM.iframe);
	}
	XDM.iframe.src = iframeUrl;
	
	return false;
};


XDM.serializeMessage = function(method, args){
	var out = 'method=' + escape(method.toString());
        var x;
        for (var i = 0; i < args.length; i++){
                x = i+1;
                out += ';arg' + x + '=' + escape(args[i]);
        }
	XDM.debug("Serialized message: " + out);
	return out;
};


XDM.canPostMessage = function(){
	if (XDM.postMessageEnabled === false){
		return false;
	} else if( window.postMessage || window.document.postMessage) {
		return true;
	} else {
		return false;
	}
};


XDM.debug = function(msg){
        if (XDM.debugOn && typeof console != "undefined" && typeof console.log != "undefined"){
                console.log("XDM debug: " +  msg);
        }
};


XDM.listenForMessages = function(handler){
	if (XDM.canPostMessage()){
		if (window.addEventListener) { // W3C
			return window.addEventListener("message", handler, false);
		} else if (window.attachEvent){ // IE 
			return window.attachEvent("onmessage", handler);
		}
	} else {
		// Remote iframe will execute the messages
		return true;
	}
};


XDM.isAllowedMethod = function(method){
	var found = false;
	for (var i = 0; i < XDM.allowedMethods.length; i++){
		if (method.toString() === XDM.allowedMethods[i]){
			found = true;
			break;
		}
        }
	return found;
};


XDM.executeMessage = function(serializedMessage){
	var nvpairs = XDM.parseQueryString(serializedMessage);
	if ( XDM.isAllowedMethod(nvpairs["method"])){

		var functionArgs = [], code = nvpairs["method"];
		// Build up the argument list
		for (var prop in nvpairs){
			if (prop.substring(0, 3) == "arg"){
				functionArgs.push(nvpairs[prop].replace(/"/g, '\\"'));
			}
		}

		// Why hard code this? To prevent stupid shit.
		if (functionArgs.length > 0){
			code += '("' + functionArgs.join('","') + '");';
		} else {
                	code += "();";
		}
		if (top != self ){
			nvpairs.destWin = nvpairs.destWin || "top";
			code = nvpairs.destWin + "." + code;
		}

		XDM.debug("Evaluating " + code);
		return eval(code);
	} else {
                XDM.debug("Invalid method from XDM: " + nvpairs["method"]);
                return false;
	}
};


/* This code looks at the supplied query string and parses it.
 * It returns an associative array of url decoded name value pairs
 */
XDM.parseQueryString = function (qs){
        var ret = [];
        if (typeof qs != "string") { return ret; }

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

/********* Start of real hop.js ***************/
function XDM_onload (){
	if (top == window.parent ){
		XDM.send(top, "Liftium.iframeHop", [window.location]);
	} else {
		// Nested iframe
		XDM.send(top, "Liftium.iframeHop", [document.referrer]);
	}
}
if ( top != self ) {
	if (document.referrer && document.referrer.match(/(liftium.com|liftium.wikia-inc.com)/)){
		document.write("<h3>Tag successfully called Liftium's hop.js. On the live site, it would have called the next ad in the chain.</h3>");
	} else {
		// Tell the top window to hop 
		if (self.attachEvent){
			// Use onload for IE, which won't let you append to body until it's complete	
			self.attachEvent("onload",XDM_onload);
		} else {
			XDM_onload();
		}
	}
} else {
	// not in an iframe, call the next ad
	window.Liftium.debug("Liftium.hop() called from hop.js", 3);
	window.Liftium.hop();
}

