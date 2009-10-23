/* Ad Network Optimizer written in Javascript */

if (typeof Liftium == "undefined" ) { // No need to do this twice

var Liftium = {
	baseUrl		: "http://delivery.liftium.com/",
	chain 		: [],
	geoUrl 		: "http://geoiplookup.wikia.com/",
	calledSlots 	: [],
        rejTags         : []

};


/* ##### Methods are in alphabetical order, call to Liftium.init at the bottom */


/* Simple convenience function for getElementById */
Liftium._ = function(id){
        return document.getElementById(id);
};


/* Simple abstraction layer for event handling across browsers */
Liftium.addEventListener = function(item, eventName, callback){
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


/*  Set up chain for slot
 *    a) See if there is a "sampled ad" to try
 *    b) Check against targeting criteria
 *    c) Check against frequency/rejection capping criteria
 *    d) Consider the maximum number of hops, have the last one be a always_fill
 */
Liftium.buildChain = function(slotname) {

        Liftium.slotTimer = Liftium.slotTimer || [];

        var size = Liftium.getSizeForSlotname(slotname);

        // Start the timer;
        var now = new Date();
        Liftium.slotTimer[slotname] = now.getTime();

        var networks = [];
        Liftium.chain[slotname] = [];

        // Do we have this slot?
        if (Liftium.e(Liftium.config.sizes[size])){
		Liftium.reportError("Unrecognized size in Liftium: " + size, "publisher");
		return false;
        }

        // Sort the chain. Done client side for better caching and randomness
        Liftium.config.sizes[size].sort(Liftium.chainSort);

	// Build the chain
        for (var i = 0, l = Liftium.config.sizes[size].length; i < l; i++){
                var t = Liftium.clone(Liftium.config.sizes[size][i]);

                if (Liftium.isValidCriteria(t)){
                        Liftium.config.sizes[size][i]['inChain'] = true;
                        Liftium.chain[slotname].push(t);
                        networks.push(t["network_name"] + ", #" + t["tag_id"]);

                        if (t['always_fill'] == 1){
                                Liftium.d("Chain complete - last ad is always_fill", 2, networks);
                                return true;
                        } else if (Liftium.chain[slotname].length == Liftium.maxHops - 1){
                                // Chain is full
                                break;
                        }
                }
        }

        // AlwaysFill ad.
        var gAd = Liftium.getAlwaysFillAd(size);
	if ( gAd !== false) {
		Liftium.chain[slotname].push(gAd);
		networks.push("AlwaysFill: " + gAd["network_name"] + ", #" + gAd["tag_id"]);
	}

        // Sampled ad
        var sampledAd = Liftium.getSampledAd(size);
        // Business rule: Don't do sampling if a tier 1 ad is present (exclusive)
        if (sampledAd !== false && Liftium.isValidCriteria(sampledAd) && Liftium.chain[slotname][0]['tier'] != "1"){
                // HACK: No easy way to put an element on to the beginning of an array in javascript, so reverse/push/reverse
                Liftium.chain[slotname].reverse();
                Liftium.chain[slotname].push(sampledAd);
                Liftium.chain[slotname].reverse();
                networks.push("Sampled: " + sampledAd["network_name"] + ", #" + sampledAd["tag_id"]);
        }


        Liftium.d("Chain for " + slotname + " = ", 3, networks);
        return true;
};


/* Build up a query string from the supplied array (nvpairs). Optional separator, default ';' */
Liftium.buildQueryString = function(nvpairs, sep){
        if (Liftium.e(nvpairs)){
                return '';
        }
        if (typeof sep == "undefined"){
                sep = '&';
        }

        var out = '';
        for(var name in nvpairs){
                if (Liftium.e(nvpairs[name])){
                        continue;
                }
                out += sep + escape(name) + '=' + escape(nvpairs[name]);
        }

        return out.substring(sep.length);
};


Liftium.callAd = function (sizeOrSlot, iframe) {

	// FIXME. Seems wrong to do this config check every time an add is called.
	// Catch config errors
        if (Liftium.e(Liftium.config)){
                Liftium.reportError("Error downloading config");
		var t = Liftium.fillerAd(sizeOrSlot, "Error downloading config");
		document.write(t.tag);
                return false;
        } else if (Liftium.config.error){
                Liftium.reportError("Config error " + Liftium.config.error);
		var t2 = Liftium.fillerAd(sizeOrSlot, Liftium.config.error);
		document.write(t2.tag);
                return false;
        }

	// Now that config has downloaded, set up the XDM config.
	XDM.iframeUrl = Liftium.config.xdm_iframe_path;

	// Write out a _load div and call the ad
	var slotname = Liftium.getUniqueSlotname(sizeOrSlot); 
	document.write('<div id="' + slotname + '" style="overflow: hidden">');
	Liftium._callAd(slotname);
	document.write("</div>");
	return true;
};


/* Do the work of calling the ad tag */
Liftium._callAd = function (slotname, iframe) {
	Liftium.d("Calling ad for " + slotname, 1);
        var t = Liftium.getNextTag(slotname);
	if (t === false) {
		Liftium.fillerAd(slotname, "getNextTag returned false");
		if (iframe) {
			Liftium.clearPreviousIframes(slotname);
			// TODO write PSA in iframe
		} else {
                        document.write(t["tag"]);
		}
		return false;
	}

	// Network Options
	Liftium.handleNetworkOptions(t);

        Liftium.d("Ad #" + t["tag_id"] + " for " + t['network_name'] + " called in " + slotname);
        Liftium.d("Config = ", 6, t);

        try { // try/catch block to isolate ad tag errors

                if (!Liftium.e(iframe)){
        		// Clear other load divs for the current slot
			Liftium.clearPreviousIframes(slotname);
                        Liftium.callIframeAd(slotname, t);
                } else {
			// Capture the current tag for error handling
			Liftium.d("Tag :" + t["tag"], 5);
                        Liftium.lastTag = t;
			Liftium.lastSlot = slotname;
                        document.write(t["tag"]);
                        Liftium.lastTag = null;
                }
        } catch (e) {
		// This is probably never called, because the document.write hides it...
                Liftium.reportError("Error loading tag #" + t.tag_id + ": " + Liftium.print_r(e), "tag");
        }

        return true;

};


Liftium.callIframeAd = function(slotname, tag, adIframe){

        var iframeUrl = Liftium.getIframeUrl(slotname, tag);
        if (Liftium.e(iframeUrl) || iframeUrl == "about:blank"){
                Liftium.d("Skipping No iframe ad called for No Ad for " + slotname, 3);
                return;
        }

        if (typeof adIframe == "object"){
                // Iframe passed in, use it
                adIframe.src = iframeUrl;
        } else {
                // Otherwise, create one and append it to load dive
                adIframe = document.createElement("iframe");
                var s = tag["size"].split("x");
                adIframe.src = iframeUrl;
                adIframe.width = s[0];
                adIframe.height = s[1];
                adIframe.scrolling = "no";
                adIframe.frameBorder = 0;
                adIframe.marginHeight = 0;
                adIframe.marginWidth = 0;
                adIframe.allowTransparency = true; // For IE
                adIframe.id = slotname + '_' + tag["tag_id"];
		Liftium._(slotname).appendChild(adIframe);
        }

};

/* Handle Javascript errors with window.onerror */
Liftium.catchError = function (msg, url, line) {
	try {
		var jsmsg;
		if (typeof msg == "object"){
			jsmsg = "Error object: " + Liftium.print_r(msg);
		} else {
			jsmsg = "Error on line #" + line + " of " + url + " : " + msg;
		}

		Liftium.d("ERROR! " + jsmsg);

		if (Liftium.e(Liftium.lastTag)){
			Liftium.reportError(jsmsg, "onerror");
		} else {
			Liftium.reportError("Tag error for tag " + Liftium.print_r(Liftium.lastTag) + "\n" + jsmsg, "tag");
		}
		// If being called from the unit testing suite, mark it as a failed test
		if (! Liftium.e(window.failTestOnError)) { // Set in LiftiumTest
			window.LiftiumTest.testFailed();
			alert(msg);
		}
	} catch (e) {
		// Oh no. Error in the error handler. 
	}
	return false; // Make sure we let the default error handling continue
};

/* Sort the chain based on the following criteria:
 * tier, weighted_random
 * The idea behind weighted_random is that we want to sort items
 * within the same tier randomly (to take advantage of cream skimming)
 * But we also want to favor the higher paying ads
 */
Liftium.chainSort = function(a, b){
        var a_tier = parseInt(a['tier'], 10) || 0;
        var b_tier = parseInt(b['tier'], 10) || 0;
        if (a_tier < b_tier){
                return -1;
        } else if (a_tier > b_tier){
                return 1;
        } else {
                // Same tier, sort by weighted random
                var a_weight = Math.random() + (parseFloat(a['value']) || 0);
                var b_weight = Math.random() + (parseFloat(b['value']) || 0);
                return b_weight - a_weight;
        }
};


Liftium.clearPreviousIframes = function(slotname){
        var loadDiv = Liftium._(slotname);
        if (loadDiv === null){
                return false;
        }

        var iframes = loadDiv.getElementsByTagName("iframe");
        for (var i = 0, l = iframes.length; i < l; i++){
                iframes[i].style.display = "none";
        }

        return true;
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


/* Handler for messages from XDM */
Liftium.crossDomainMessage = function (message){
	XDM.allowedMethods = ["Liftium.iframeHop", "LiftiumTest.testPassed"];
	XDM.executeMessage(message.data);
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


/* Filler ad when we don't have anything better to display. Usually means an error, either with the code
 * or the chain 
 * http://www.peacecorps.gov/index.cfm?shell=resources.media.psa.webbanners
 */
Liftium.fillerAd = function(size, message){
	// Pull the height/width out of size
	size = size || "300x250"; // TODO/FIXME: figure out size by looking at containing div

	var tag = '';
	if (!Liftium.e(message)){
                tag += '<div class="LiftiumError" style="display:none">Liftium message: ' + message + "</div>";
	}

	if (size.match(/300x250/)){
		tag += '<a href="http://www.peacecorps.gov/psa/webbanners/click?cid=psa15" target="_blank"><img src="http://www.peacecorps.gov/images/webbanners/full/300x250_legacy.gif" width="300" height="250" border="0" alt="Public Service Announcement"/></a>';
	} else if (size.match(/728x90/)){
                tag += '<a href="http://www.peacecorps.gov/psa/webbanners/click?cid=psa1" target="_blank"><img src="http://www.peacecorps.gov/images/webbanners/full/728x90_thinklocal.gif" width="728" height="90" border="0" alt="Public Service Announcement"/></a>';
	} else if (size.match(/160x600/)){
		tag += '<a href="http://www.peacecorps.gov/psa/webbanners/click?cid=psa14" target="_blank"><img src="http://www.peacecorps.gov/images/webbanners/full/160x600_legacy.gif" width="160" height="600" border="0" alt="Public Service Announcement"/></a>';
	}
	tag += "Public Service Announcement";
	return {tag_id: 'psa', network_name: "Internal Error PSA", tag: tag, size: size};
};

/* Look through the list of ads in the potential chain, and return the best always_fill */
Liftium.getAlwaysFillAd = function(size){

        for (var i = 0, l = Liftium.config.sizes[size].length; i < l; i++){
                var t = Liftium.config.sizes[size][i];

                if (t['always_fill'] == 1 && Liftium.isValidCriteria(t)){
                        return Liftium.clone(t);
                }
        }

        // Rut roh
        return false;
};


/* Get the users country */
Liftium.getCountry = function(){
        if (!Liftium.e(Liftium.getCountryFound)){
                return Liftium.getCountryFound;
        }

        var ac;
        if (!Liftium.e(Liftium.getRequestVal('liftium_country'))){
                ac = Liftium.getRequestVal('liftium_country');
                Liftium.d("Using liftium_country for geo targeting (" + ac + ")", 8);
        } else if (typeof window.Geo == "undefined") {
                // sometimes Geo isn't available because geoiplookup hasn't returned
                Liftium.reportError("Geo country not downloaded properly, defaulting to US for now", "geoiplookup");
                return "us"; // Bail here so Liftium.getCountryFound doesn't get set
        } else if (typeof window.Geo.country == "undefined" ) {
                // It downloaded, but it's empty, because we were unable to determine the country
                Liftium.d("Unable to find a country for this IP, defaulting to US");
                ac = "us";
        } else {
                // Everything worked
                ac = window.Geo.country.toLowerCase();
        }

        if (ac === "gb"){
                // Wankers.
                ac = "uk";
        }

        Liftium.getCountryFound = ac;
        return ac;
};


/* Normalize the language of the browser.
 * FF, Safari, Chrome, Camino use 'language'
 * Opera uses browserLanguage and userLanguage
 * IE uses 'systemLanguage', and 'userLanguage'
 * May vary depending on platform based on what the OS exposes
 */
Liftium.getBrowserLang = function () {
	var n = window.navigator;
	var l = n.language || n.systemLanguage || n.browserLanguage || n.userLanguage || "";
	return l.substring(0,2);
};


/* When an ad does a document.write and we are already passed that point on the page,
 * we need to call it in an lframe (document.write can only be executed inline)
 * We handle this by calling the iframe from Athena. This function returns the iframe url */
Liftium.getIframeUrl = function(slotname, tag) {

        // Check to see if the tag is already an iframe. 
        var m = tag["tag"].match(/<iframe[\s\S]+src="([^"]+)"/), iframeUrl;

        if ( m !== null ){
                iframeUrl = m[1].replace(/&amp;/g, "&");
                Liftium.d("Found iframe in tag, using " + iframeUrl, 3);
	/* Nick wrote: Do we need this? 
        // Handle noad.gif here so it doesn't get called by iframe 
        } else if (tag["network_name"] == "No Ad"){
                Liftium.d("Using about:blank for 'No Ad' to avoid iframe", 3);
                iframeUrl = "about:blank";
	*/
        } else {
                var p = { "tag_id": tag["tag_id"], "size": tag["size"], "slotname": slotname};
                iframeUrl = Liftium.baseUrl + "tag/?" + Liftium.buildQueryString(p);
                Liftium.d("No iframe found in tag, using " + iframeUrl, 3);
        }
        return iframeUrl;
};


/* Returns the number of minutes that have elapsed since midnight, according to the users clock */
Liftium.getMinutesSinceMidnight = function(){
        var now = new Date();
        return (now.getHours() * 60) + now.getMinutes();
};

/* Return the number of minutes since the last reject for the supplied tag id.
 * null if there hasn't been a reject
 */
Liftium.getMinutesSinceReject = function(tag_id){
        var m = Liftium.getTagStat(tag_id, "m");
        if (m === null){
                return null;
        } else {
                return Liftium.getMinutesSinceMidnight() - m;
        }
};



/* Iterate through the chain and deliver the next ad tag to be called */
Liftium.getNextTag = function(slotname){
        // Do we need to build the chain?
        if (Liftium.e(Liftium.chain[slotname])){
                if ( Liftium.buildChain(slotname) === false){
			Liftium.reportError("Error building chain " + slotname, "chain");
			return false;
                }
        }

	// Belt and suspenders to prevent too many hops
	Liftium.chain[slotname].numHops = Liftium.chain[slotname].numHops || 0;
	Liftium.chain[slotname].numHops++;
	if (Liftium.chain[slotname].numHops > 10){
		Liftium.reportError("Maximum number of hops exceeded: 10", "chain");
		return false;
	}
	
	// \suspenders

        var now = new Date();

        var length = Liftium.chain[slotname].length;
        var current = Liftium.chain[slotname].current = Liftium.chain[slotname].current || 0;
        
        if ((now.getTime() - Liftium.slotTimer[slotname]) > (Liftium.config.maxHopTime || Liftium.maxHopTime)){
                // Maximum fill time has been exceeded, jump to the always_fill
                Liftium.d("Hop Time of " + Liftium.config.maxHopTime + " exceeded. Using the always_fill", 2);
                Liftium.chain[slotname][current]['exceeded'] = true;
                
                // Return the always_fill
                var lastOne = length - 1;
                Liftium.chain[slotname].current = lastOne;
                Liftium.chain[slotname][lastOne]['started'] = now.getTime();
                return Liftium.chain[slotname][lastOne];
        } else {
                for (var i = current, l = length; i < l; i++){
                        if (!Liftium.e(Liftium.chain[slotname][i]['started'])){
                                continue;
                        } else {
                                // Win nah!
                                Liftium.chain[slotname][i]['started'] = now.getTime();
                                Liftium.chain[slotname].current = i;
                                return Liftium.chain[slotname][i];
                        }

                }
        }

        // Rut roh
        Liftium.reportError("No more tags left in the chain", "chain");
        return Liftium.fillerAd(slotname, "No more tags left in the chain");
};


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

/* Look through the list of ads in the potential chain, and find one that is sample-able */
Liftium.getSampledAd = function(size){
        // Build up an array of the sample stats.
        var sArray = [], total = 0, myRandom = Liftium.rand * 100;
        for (var i = 0, l = Liftium.config.sizes[size].length; i < l; i++){
                var sample_rate = parseFloat(Liftium.config.sizes[size][i]['sample_rate']);
                if (Liftium.e(sample_rate)){
                        continue;
                }
                total += sample_rate;

                Liftium.d("Sample Rate for " + Liftium.config.sizes[size][i]['tag_id'] + " is " + sample_rate, 7);
                sArray.push( { "upper_bound": total, "index": i });

        }
        Liftium.d("Sample Array = ", 7, sArray);

        // Now check to see if the random number is in sArray
        for (var j = 0, l2 = sArray.length; j < l2; j++){
                if (myRandom < sArray[j]["upper_bound"]){
                        var f = sArray[j]["index"];
                        return Liftium.clone(Liftium.config.sizes[size][f]);
                }
        }

        return false;
};


Liftium.getSlotnameFromElement = function(element){
	if (typeof element != "object") {
		return false;
	}

	// Walk up the dom and find which slot div it's in 
	var tempElement = element, tries = 0;
	while(tempElement && tries < 10){
		if (tempElement.id && tempElement.id.match(/^Liftium_/)){
			return tempElement.id;
		} else {
			tempElement = tempElement.parentNode;
		}
		tries++;
	}
	return false;
};


/* Format is $day_{$tag_id}l{$loads}r{$rejects}m{$lastrejecttime}
 * r and m are optional, only if there is a reject
 * $day -- 0 to 6, where 0 is Sunday
 * $tag_id -- you better know what this is 
 * $loads -- number of loads today
 * $rejects -- number of rejects today
 * $lastrejecttime -- minutes since midnight of the last reject
 */
Liftium.getStatRegExp = function(tag_id){
        return new RegExp(Liftium.now.getDay() + '_' + tag_id + 'l([0-9]+)r*([0-9]*)m*([0-9]*)' );
};


/* Figure out what size a slot is by looking at the config.
 * Should we pass a map of sizes->slotnames in the config? Maybe, but it would make it bigger...
 */
Liftium.getSizeForSlotname = function (slotname){
	var match = slotname.match(/[0-9]{1,3}x[0-9]{1,3}/);
	if (match !== null){
		return match[0];
	}

        for (var slot in Liftium.config.slotnames){
                if (typeof Liftium.config.slotnames[slot] == "function"){
                        // Prototype js library overwrites the array handler and adds crap. EVIL.
                        continue;
                }
                if (slot == slotname){
                        return Liftium.config.slotnames[slot];
                }
        }

        return false;
};


/* Get loads/rejects for a tag. Type = "l" for loads,  "r" for rejects,
 * and "m" for the minutes since midnight of the last rejection */
Liftium.getTagStat = function (tag_id, type){
        var stat = null;

        if (Liftium.tagStats === undefined || Liftium.tagStats === null) {
                var tagStats = Liftium.cookie("ATS");
                if (tagStats === null) {
                    tagStats = '';
                }
                Liftium.tagStats = tagStats;
        }

        var statMatch = Liftium.tagStats.match(Liftium.getStatRegExp(tag_id));
        if (!Liftium.e(statMatch)){
                var len = statMatch.length;
                if (type === "l" && len >= 2){
                        stat = statMatch[1];
                } else if (type === "r" && len >= 3){
                        stat = statMatch[2];
                } else if (type === "m" && len >= 4){
                        stat = statMatch[3];
                } else if (type === "a"){
                        var l = parseInt(statMatch[1], 0) || 0;
                        var r = parseInt(statMatch[2], 0) || 0;
                        stat = l + r; // attempts are loads + rejects
                }
        }

        if (Liftium.e(stat)) {
                // For type = m, we return null if not found. Otherwise, return 0
                if ( type == "m" ){
                        stat = null;
                } else {
                        stat = 0;
                }
        } else {
                stat = parseInt(stat, 10); // convert to number for numerical comparison
        }

        Liftium.d("Stats for " + tag_id + " type " + type + " = " + stat, 7);
        return stat;
};



Liftium.getUniqueSlotname = function(sizeOrSlot) {
	Liftium.slotnames = Liftium.slotnames || [];

	var s = "Liftium_" + sizeOrSlot;
	if (Liftium.in_array(s, Liftium.slotnames)){
		// This size already called on the page, so make up a new one.
		s += "_" + Math.random().toString().substring(3,9);
	}
	Liftium.slotnames.push(s);

	return s;
};


/* Pass options from LiftiumOptions through to the tag */
Liftium.handleNetworkOptions = function (tag) {

	switch (tag.network_id){
	  case "1": /* Google */

	    for (var opt in window.LiftiumOptions){
		if (opt.match(/^google_/)){
			Liftium.d(opt + " set to " +  window.LiftiumOptions[opt], 5);
			window[opt] = window.LiftiumOptions[opt];
		}
	    }
	    return true;

	  default:
	    return true;
	}
};


/* This is the backup tag used to go to the next ad in the configuration */
Liftium.hop = function (slotname){
	// Use the slotname from the last called ad. 
	if (Liftium.e(slotname)){
		slotname = Liftium.lastSlot;
	}
        Liftium.d("Liftium.hop() called for " + slotname);

        return Liftium._callAd(slotname);
};
// Some networks let you hop with a javascript function and that's it (VideoEgg)
var LiftiumHop = Liftium.hop;


/* Hop called from inside an iframe. This part is tricky */
Liftium.iframeHop = function(iframeUrl){
	Liftium.d("Liftium.iframeHop() called from " + iframeUrl, 3);
	var slotname;

	// Go through all the irames to find the matching src
        var iframes = document.getElementsByTagName("iframe");
	for (var i = 0, len = iframes.length; i < len; i++){
		// IE doesn't prepend the host name if you call a local iframe
		if (iframeUrl.indexOf(iframes[i].src) >=  0){
			// Found match
			slotname = Liftium.getSlotnameFromElement(iframes[i]);
			break;
		}
	}
        if ( Liftium.e(slotname)){
		Liftium.reportError("Unable to find iframe for " + iframeUrl);
	} else {
		Liftium._callAd(slotname, true);
	}
};


/* Set / Get an iframes contents, depending on the number of arguments */
Liftium.iframeContents = function(iframe, html){
	if (typeof iframe != "object"){
		return false;
	}

	// Get the dom object
	// IE does one way, W3C is another. Sooprise!
	// Thanks to: http://bindzus.wordpress.com/2007/12/24/adding-dynamic-contents-to-iframes/
	if (! iframe.doc) {
		if(iframe.contentDocument) {
			// Firefox, Opera
			iframe.doc = iframe.contentDocument;
		} else if(iframe.contentWindow) {
			// IE
			iframe.doc = iframe.contentWindow.document;
		} else if(iframe.document) {
			// Others?
			iframe.doc = iframe.document;
		}

		// Trick to set up the document. See url above for info
		iframe.doc.open();
		iframe.doc.close();
	}
 

	if (typeof html != "undefined" ){
		// Set
		iframe.doc.body.style.backgroundColor="blue";
		var div = iframe.doc.createElement("div");
		div.id = "div42";
		div.innerHTML = html;
		iframe.doc.body.appendChild(div);
		return true;
	} else {
		// Get
		return iframe.doc.getElementById("div42").innerHTML;
	}
};



/* Emulate PHP's in_array, which will return true/false if a key exists in an array */
Liftium.in_array = function (needle, haystack, ignoreCase){
    for (var key in haystack) {
        if (haystack[key] == needle) {
            return true;
        } else if (ignoreCase && haystack[key].toString().toLowerCase() == needle.toString().toLowerCase()){
            return true;
	}
    }

    return false;
};



Liftium.init = function () {
        Liftium.now = new Date();
        Liftium.startTime = Liftium.now.getTime();
        Liftium.debugLevel = Liftium.getRequestVal('liftium_debug', 0);

	if (Liftium.e(window.LiftiumOptions) || Liftium.e(window.LiftiumOptions.pubid)){
		Liftium.reportError("LiftiumOptions.pubid must be set", "publisher"); // TODO: provide a link to documentation
	}

	Liftium.pullConfig();
	
	Liftium.addEventListener(window, "load", Liftium.onLoadHandler);

	// Tell the parent window to listen to hop messages 
	if (window.LiftiumOptions.enableXDM !== false ){
		XDM.listenForMessages(Liftium.crossDomainMessage);
	}
};

/* Different browsers handle iframe load state differently. For once, IE actually does it best.
 * IE - document.readyState *and* iframes.readyState is "interactive" until all iframes loaded, then it is "complete"
 * Chrome/Safari - loading|loaded|complete, DOMFrameContentLoaded supported, but won't allow you to change iframe.readyState
 *      Unfortunately, nested iframes will be called "loaded"
 */
Liftium.iframesLoaded = function(){
        var iframes = document.getElementsByTagName("iframe"); 
	if (iframes.length === 0){ return true; }

	var b = BrowserDetect.browser;
	if (Liftium.in_array(b, ["Firefox", "Gecko", "Mozilla"]) && Liftium.pageLoaded){
 		// Firefox/Seamonkey/Camino - no document.readyState, but load event is *after* iframes
		return true;
	} else if (Liftium.in_array(b, ["Explorer","Opera"]) && document.readyState == "complete") {
		// We also need to check the document.readyState for each iframe
		for (var i = 0; i < iframes.length; i++){
			if (iframes[i].document.readyState != "complete"){
				return false;
			}
		}
		return true;
	} else { 
		// All other browsers will send the beacon when the time runs up.
		return false;
	}
};
/* Opera can be handled by setting the readyState when the iframe loads */
if (BrowserDetect.browser == "Opera"){
  Liftium.iframeOnload = function (e){
        var iframe = e.target || e;

        // Different browsers do/do not set the readyState. For the ones that don't set it here to normalize
        try { // Supress permission denied errors for cross domain iframes
                if (typeof iframe.readyState == "undefined" ) {
                        iframe.readyState = "complete";
                }
        } catch (e) {}
  };
  Liftium.addEventListener(window, "DOMFrameContentLoaded", Liftium.iframeOnload);
}



/* Check to see if the user from the right geography */
Liftium.isValidCountry = function (countryList){

        var ac = Liftium.getCountry();

        Liftium.d("Checking if " + ac + " is in:", 8, countryList);

        if (Liftium.in_array("row", countryList, true) &&
                  !Liftium.in_array(ac, ['us','uk','ca'])){
                Liftium.d("ROW targetted, and country not in us, uk, ca", 6);
                return true;
        }
        if (Liftium.in_array(ac, countryList, true)){
                return true;
        }

        return false;
};

/* Does the criteria match for this tag? */
Liftium.isValidCriteria = function (t){
        if (Liftium.in_array(t['tag_id'], Liftium.rejTags)){
                Liftium.d("Ad #" + t["tag_id"] + " rejected because of already rejected on this page", 3, Liftium.rejTags);
                t['isValidCriteria'] = false;
                return t['isValidCriteria'];
        }

        // For ads that have a frequency cap, don't load them more than once per page
        if (!Liftium.e(t["inChain"]) && !Liftium.e(t["freq_cap"])) {
                Liftium.d("Ad #" + t["tag_id"] + " from " + t["network_name"] +
                        " invalid: it has a freq cap and is already in another chain", 3);
                t['isValidCriteria'] = false;
                return t['isValidCriteria'];
        }

        if (!Liftium.e(t['isValidCriteria'])){
                return t['isValidCriteria'];
        }

	// Frequency
        if (!Liftium.e(t["freq_cap"])){
                var a = Liftium.getTagStat(t["tag_id"], "a");
                if (a >= parseInt(t["freq_cap"], 10)){
                        Liftium.d("Ad #" + t["tag_id"] + " from " + t["network_name"] +
                                " invalid: " + a + " attempts is >= freq_cap of " +
                                t["freq_cap"], 3);
                        t['isValidCriteria'] = false;
                        return t['isValidCriteria'];
                }

        }

        // Rejection time
        if (!Liftium.e(t["rej_time"])){
                var elapsedMinutes = Liftium.getMinutesSinceReject(t["tag_id"]);

                if (elapsedMinutes !== null){
                        Liftium.d("Ad #" + t["tag_id"] + " from " + t["network_name"] +
                                        " rej_time = " + t["rej_time"] + " elapsed = " + elapsedMinutes, 7);
                        if (elapsedMinutes < parseInt(t["rej_time"], 10)){
                                Liftium.d("Ad #" + t["tag_id"] + " from " + t["network_name"] +
                                        " invalid:  tag was rejected sooner than rej_time of " +
                                        t["rej_time"], 3);
                                t['isValidCriteria'] = false;
                                return t['isValidCriteria'];
                        }
                }

        }

        if (!Liftium.e(t['criteria'])){
                for (var key in t.criteria){
                        switch (key){
                          case 'country':
                                if ( ! Liftium.isValidCountry(t.criteria.country)){
                                        Liftium.d("Ad #" + t["tag_id"] + " rejected because of Invalid country", 8);
                                        t['isValidCriteria'] = false;
                                        return t['isValidCriteria'];
                                }
                                break;
                          default:
				/* TODO support arbitrary key values 
                                // If it is not predefined, assume it is a page var.
                                var list = eval("t.criteria." + key);
                                if (! Liftium.in_array(Liftium.getPageVar(key), list)){

                                        Liftium.d("Ad #" + t["tag_id"] + " rejected because " + key + " not found in ", 8, list);
                                        t['isValidCriteria'] = false;
                                        return t['isValidCriteria'];
                                }
				*/
				
                                break; // Shouldn't be necessary, but silences a jslint error
                        }
                }
        }

        // All criteria passed 
        Liftium.d("Targeting criteria passed for tag #" + t["tag_id"], 8);
        t['isValidCriteria'] = true;
        return t['isValidCriteria'];

};





/* Load the supplied url inside a script tag  */
Liftium.loadScript = function(url, noblock) {
        if (typeof noblock == "undefined"){
                // This method blocks
                document.write('\x3Cscript type="text/javascript" src="' + url + '">\x3C\/sc' + 'ript>');
        } else {
                // This method does not block
                var h = document.getElementsByTagName("head").item(0);
                var s = document.createElement("script");
                s.src = url;
                h.appendChild(s);
        }
};


/* Clean up the chain. Mark loads/rejects where we know what happened. 
Liftium.markChain = function (slotname){
        var attemptFound = false, len = Liftium.chain[slotname].length;
        // If an attempt was found, then everything else "started" was rejected
        Liftium.d("Marking chain for " + slotname, 5);
	if (Liftium.e(Liftium.chain[slotname])){
		Liftium.debug("Skiping Marking chain, chain was empty");
		return false;
	}
        for (var i = len - 1; i >= 0; i--){
                if (attemptFound && !Liftium.e(Liftium.chain[slotname][i]['started'])){
                        Liftium.chain[slotname][i]['rejected'] = true;
                        Liftium.chain[slotname][i]['loaded'] = false;
                        Liftium.rejTags.push(Liftium.chain[slotname][i]['tag_id']);
                } else if (!Liftium.e(Liftium.chain[slotname][i]['started'])){
                        attemptFound = true;
                }
        }

        // If a garaunteed ad was filled, mark it as loaded
        for (var j = 0 ; j < len; j++){
                if (!Liftium.e(Liftium.chain[slotname][j]['started']) &&
                     Liftium.chain[slotname][j]['guaranteed_fill'] == 'Yes' ){
                        Liftium.chain[slotname][j]['loaded'] = true;
                        return true;
                }
        }

        // If the slot/document is completely loaded, the last one called must be the one loaded
	var k = Liftium.chain[slotname].current;
	Liftium.chain[slotname][k]['loaded'] = true;
        return true;

};
*/


Liftium.markChain = function (slotname){
        Liftium.d("Marking chain for " + slotname, 5);
	if (Liftium.e(Liftium.chain[slotname])){
		Liftium.debug("Skiping Marking chain, chain was empty");
		return false;
	}
        for (var i = 0, len = Liftium.chain[slotname].length; i < len; i++){
		if (i < Liftium.chain[slotname].current){
			Liftium.chain[slotname][i]["rejected"] = true;
		} else if (i == Liftium.chain[slotname].current){
			Liftium.chain[slotname][i]["loaded"] = true;
			break;
		}
	}	
	return i;
};


Liftium.onLoadHandler = function () {
	Liftium.pageLoaded = true;
	Liftium.loadDelay = Liftium.loadDelay || 100;
        if ( Liftium.iframesLoaded()) {
		Liftium.sendBeacon();
	} else if (Liftium.loadDelay < 5000){
                // Check again in a bit. Keep increasing the time
                Liftium.loadDelay += Liftium.loadDelay;
                window.setTimeout("Liftium.onLoadHandler()", Liftium.loadDelay);
	} else {
		Liftium.d("Gave up waiting for ads to load, sending beacon now");
		Liftium.sendBeacon();
	}
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


/* Pull the configuration data from our servers */
Liftium.pullConfig = function (){

        var p = {
		"pubid" : window.LiftiumOptions.pubid,
                "v": 1.2 // versioning for config
        };

	// Simulate a small delay (used by unit tests
	if (!Liftium.e(window.LiftiumOptions.config_delay)){
		p.config_delay = window.LiftiumOptions.config_delay;
		p.cb = Math.random();
	}

        // Allow for us to work in a dev environment
        if (! Liftium.e(Liftium.getRequestVal('liftium_dev_hosts') ||
              window.location.hostname.indexOf(".dev.liftium.com") > -1)){
                // overwrite
                Liftium.baseUrl = '/';
        }

        var u = Liftium.baseUrl  + 'config?' + Liftium.buildQueryString(p);
        Liftium.d("Loading config from " + u, 2);
        Liftium.loadScript(u);


        Liftium.d("Loading geo data from " + Liftium.geoUrl, 3);
        Liftium.loadScript(Liftium.geoUrl);
};


/* Javascript equivalent of php's print_r.  */
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
                                out.push(padding + "'" + item + "' => \"" +
					Liftium.print_r(data[item]) + "\"\n");
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

/* Record the loads/rejects, and return a string of events to be sent by the beacon */
Liftium.recordEvents = function(slotname){

        var e = '';
        for (var i = 0, l = Liftium.chain[slotname].length; i < l; i++){
                var t = Liftium.chain[slotname][i];
                if ( Liftium.e(t['started'])){
                        // There can't be a load or a reject if it wasn't started.
                        continue;
                }

                var loads = Liftium.getTagStat(t['tag_id'], "l");

                // Load
                if (!Liftium.e(Liftium.chain[slotname][i]['loaded'])){
                        Liftium.d("Recording Load for " + t["network_name"] + ", #" + t["tag_id"] + " in " + slotname, 4);
                        Liftium.setTagStat(t['tag_id'], "l");
                        e += ',l' + t['tag_id'] + 'pl' + loads;

                // Reject
                } else if (! Liftium.e(t['rejected'])){
                        e += ',r' + t['tag_id'] + 'pl' + loads;
                        Liftium.d("Recording Reject for " + t["network_name"] + ", #" + t["tag_id"] + " in " + slotname, 5);
                        Liftium.setTagStat(t['tag_id'], "r");
                        continue;

                }
        }

        return e.replace(/^,/, ''); // Strip off first comma
};



Liftium.reportError = function (msg, type) {
  // wrapped in a try catch block because if this function is reporting an error,
  // all hell breaks loose
  try { 
	Liftium.d("Liftium ERROR: " + msg);

	// Note that the Unit tests also track the number of errors
	if (typeof Liftium.errorCount != "undefined") {
		Liftium.errorCount++;
	} else {
		Liftium.errorCount = 1;
	}

	if (Liftium.errorCount > 5){
		// Don't overwhelm our servers if the browser is stuck in a loop.
		return;
	}

	// Ignore certain errors that we can't do anything about
	var ignores = [
		"Error loading script", // This is when a user pushes "Stop"
		"Script error.", // This is when a user pushes "Stop"
		"GA_googleFillSlot is not defined", // They probably have AdBlock on.
	        "translate.google",
	        "quantserve",
		"urchin",
		"greasemonkey",
		"Permission denied" // Ads trying to get the window location, which isn't allowed
	];
	for (var i = 0; i < ignores.length; i++){
		if (msg.indexOf(ignores[i]) >= 0){
			return;
		}
	}

	var p = {
		'msg' : msg,
		'type': type || "general",
		'pubid' : window.LiftiumOptions.pubid,
		'lang' : Liftium.getBrowserLang()
	};

	Liftium.beaconCall(Liftium.baseUrl + "error?" + Liftium.buildQueryString(p));

  } catch (e) {
	Liftium.d("Yikes. Liftium.reportError has an error");
  }
};
if (window.LiftiumOptions && window.LiftiumOptions.error_beacon !== false ){
	window.onerror = Liftium.catchError;
}


Liftium.errorMessage = function (e) {
	// e can be a native javascript error or a message. Figure out which
	if (typeof e == "object" ){
		// For now, so I can see what the format is for all browsers
		return Liftium.print_r(e);
	} else if (typeof e == "string"){
		return e;
	}
};


/* Send a beacon back to our server so we know if it worked */
Liftium.sendBeacon = function (){

	// This is called a second time from the *un*load handler, so make sure we don't call the beacon twice.
        if (!Liftium.e(Liftium.beaconCalled)){
                return true;
        }
        Liftium.beaconCalled = true;

        // Throttle the beacon
        var throttle;
		// Missing config, throttle undefined, or no value from the DB needs to be defaulted (0 is OK and means no beacons should be sent.)
        if (Liftium.e(Liftium.config) || throttle === undefined || throttle === null){
                Liftium.d("No throttle defined, using 1.0");
                throttle = 1.0;
        } else {
                throttle = Liftium.config.throttle;
        }
        if (Math.random() > throttle){
                Liftium.d("Beacon throttled at " + throttle);
                return true;
        }

	var events = '', numSlots = 0;
        for(var slotname in Liftium.chain){
                if (typeof Liftium.chain[slotname] == "function"){
                        // Prototype js library overwrites the array handler and adds crap. EVIL.
                        continue;
                }
                numSlots++;
                // Clean up the chain
                Liftium.markChain(slotname);
                // Set tag stats and get a string of events
                events += ',' + Liftium.recordEvents(slotname);
        }

        events = events.replace(/^,/, ''); // Strip off first comma

        Liftium.storeTagStats();

        var b = {};
        b.numSlots = numSlots;
        b.events = events;

	var now = new Date();

        // Pass along other goodies
        b.country = Liftium.getCountry();
        if (!Liftium.e(window.wgUserName)){
              b.loggedIn = true;
        }

        // Timeouts
        var slotTimeouts = 0;
        for (var s in Liftium.slotTimer){
              if (typeof Liftium.slotTimer[s] == "function"){
                      // Prototype js library overwrites the array handler and adds crap. EVIL.
                      continue;
              }
              if (Liftium.slotTimer[s] == "exceeded"){
                      slotTimeouts++;
              }
        }
        if (slotTimeouts > 0) {
                b.slotTimeouts = slotTimeouts;
        }

        Liftium.d ("Beacon: ", 7, b);

       
        // Not all browsers support JSON
        var p;
        if (! window.JSON) {
		p = { "events": b.events };
        } else {
		p = { "beacon": window.JSON.stringify(b) };
        }
 
        Liftium.beaconCall(Liftium.baseUrl + 'beacon?' + Liftium.buildQueryString(p));
 
        Liftium.d ("Liftium done, beacon sent");


        // Call the unit tests
        if (window.LiftiumTest && typeof window.LiftiumTest.afterBeacon == "function"){
                window.LiftiumTest.afterBeacon();
        }
	return true;
};

/* Set loads/rejects for a tag. type is "l" or "r" */
Liftium.setTagStat = function (tag_id, type){
        Liftium.d("Setting a " + type + " stat for " + tag_id, 6);

        var pieces = Liftium.tagStats.split(','), holder = [], found=false;
        if (pieces.length > Liftium.statMax){
                // If too may, take off the first one
                pieces.shift();
        }

        // Get the current stats and rebuild
        var loads = Liftium.getTagStat(tag_id, "l");
        var rejects = Liftium.getTagStat(tag_id, "r");
        var rejectMinutes = 0;

        if (type === "l"){
                loads++;
                rejectMinutes = Liftium.getTagStat(tag_id, "m") || 0;
        } else if (type === "r"){
                rejects++;
                rejectMinutes = Liftium.getMinutesSinceMidnight();
        }

        // Tack on the rejects/rejectMinutes
        var piece = Liftium.now.getDay() + '_' + tag_id + "l" + loads;
        if (rejects > 0){
                piece = piece + "r" + rejects;
                piece = piece + "m" + rejectMinutes;
        }

        var ts = Liftium.tagStats.replace(Liftium.getStatRegExp(tag_id), piece);
        if (ts === Liftium.tagStats){
                // tagid not found in stats, Append it to the end.
                Liftium.tagStats = Liftium.tagStats + ',' + piece;
        } else {
                Liftium.tagStats = ts;
        }

        Liftium.tagStats = Liftium.tagStats.replace(/^,/, ''); // Strip off first comma

        Liftium.d("Tag Stats After Set = " + Liftium.tagStats, 6);
};


/* Store accepts/rejections in a cookie
 * Keep this as small as possible! 
 */
Liftium.storeTagStats = function (){
        Liftium.d("Stored Tag Stats = " + Liftium.tagStats, 4);
        Liftium.cookie("ATS", Liftium.tagStats, {
		  // FIXME for Wikia
                  //domain: Liftium.getCookieDomain(),
                  path: "/",
                  expires: Liftium.now.getTime() + 86400
                 }
        );
};


/* Why do we even have this lever!? Because we need to test error handling (see test_jserror.php) */
Liftium.throwError = function () {
	return window.LiftiumthrowError.UndefinedVar;
};


/* Browser Detect 
http://www.quirksmode.org/js/detect.html
*/
var BrowserDetect = {
	init: function () {
		this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
		this.version = this.searchVersion(navigator.userAgent) ||
			this.searchVersion(navigator.appVersion) ||
			"an unknown version";
		this.OS = this.searchString(this.dataOS) || "an unknown OS";
	},
	searchString: function (data) {
		for (var i=0;i<data.length;i++)	{
			var dataString = data[i].string;
			var dataProp = data[i].prop;
			this.versionSearchString = data[i].versionSearch || data[i].identity;
			if (dataString) {
				if (dataString.indexOf(data[i].subString) != -1) {
					return data[i].identity;
				}
			} else if (dataProp) {
				return data[i].identity;
			}
		}
		return null;
	},
	searchVersion: function (dataString) {
		var index = dataString.indexOf(this.versionSearchString);
		if (index == -1) { return null; }
		return parseFloat(dataString.substring(index+this.versionSearchString.length+1));
	},
	dataBrowser: [
		{ string: navigator.userAgent, subString: "Chrome", identity: "Chrome" },
		{ string: navigator.userAgent, subString: "OmniWeb", versionSearch: "OmniWeb/", identity: "OmniWeb" },
		{ string: navigator.vendor, subString: "Apple", identity: "Safari", versionSearch: "Version" },
		{ prop: window.opera, identity: "Opera" },
		{ string: navigator.vendor, subString: "iCab", identity: "iCab" },
		{ string: navigator.vendor, subString: "KDE", identity: "Konqueror" },
		{ string: navigator.userAgent, subString: "Firefox", identity: "Firefox" },
		{ string: navigator.vendor, subString: "Camino", identity: "Camino" },
		{ string: navigator.userAgent, subString: "Netscape", identity: "Netscape"},
		{ string: navigator.userAgent, subString: "MSIE", identity: "Explorer", versionSearch: "MSIE" },
		{ string: navigator.userAgent, subString: "Gecko", identity: "Mozilla", versionSearch: "rv" },
		{ string: navigator.userAgent, subString: "Mozilla", identity: "Netscape", versionSearch: "Mozilla" }
	],
	dataOS : [
		{ string: navigator.platform, subString: "Win", identity: "Windows" },
		{ string: navigator.platform, subString: "Mac", identity: "Mac" },
		{ string: navigator.userAgent, subString: "iPhone", identity: "iPhone/iPod" },
		{ string: navigator.platform, subString: "Linux", identity: "Linux" }
	]

};
BrowserDetect.init();




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


if (window.Liftium){
  XDM.debug = window.Liftium.debug;
} else {
  XDM.debug = function(msg){
        if (XDM.debugOn && typeof console != "undefined" && typeof console.log != "undefined"){
                console.log("XDM debug: " +  msg);
        }
  };
}


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
		throw("Invalid method: " + nvpairs["method"]);
	}
};


/* This code looks at the supplied query string and parses it.
 * It returns an associative array of url decoded name value pairs
 */
if (window.Liftium){
  XDM.parseQueryString = window.Liftium.parseQueryString;
} else {
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
} // using Liftium parse query string

} // \if (typeof Liftium == "undefined" ) 


// Gentlemen, Start your optimization!
Liftium.init();
