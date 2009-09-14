/* Ad Network Optimizer written in Javascript */

if (typeof Liftium == "undefined" ) { // No need to do this twice

var Liftium = {
	baseurl		: "http://delivery.liftium.com/",
	chain 		: [],
	errors 		: [],
	geoUrl 		: "http://geoiplookup.wikia.com/",
        loadDelay       : 250
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


/*  Set up chain for slot
 *    a) See if there is a "sampled ad" to try
 *    b) Check against targeting criteria
 *    c) Check against frequency/rejection capping criteria
 *    d) Consider the maximum number of hops, have the last one be a garaunteed fill
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
                throw "Unrecognized size in Liftium: " + size;
        }

        // Sort the chain. Done client side for better caching and randomness
        Liftium.config.sizes[size].sort(Liftium.chainSort);

	// Build the chain
        for (var i = 0, l = Liftium.config.sizes[size].length; i < l; i++){
                var t = Liftium.clone(Liftium.config.sizes[size][i]);
                if (!Liftium.in_array(slotname, t.slotnames)){
                        continue;
                }

                if (Liftium.isValidCriteria(t)){
                        Liftium.config.sizes[size][i]['inChain'] = true;
                        Liftium.chain[slotname].push(t);
                        networks.push(t["network_name"] + ", #" + t["tag_id"]);

                        if (t['guaranteed_fill'] === 'Yes'){
                                Liftium.d("Chain complete - last ad is garaunteed.", 2, networks);
                                return true;
                        } else if (Liftium.chain[slotname].length == Liftium.maxHops - 1){
                                // Chain is full
                                break;
                        }
                }
        }

        // AlwaysFill ad.
        var gAd = Liftium.getAlwaysFillAd(size);
        Liftium.chain[slotname].push(gAd);
        networks.push("AlwaysFill: " + gAd["network_name"] + ", #" + gAd["tag_id"]);

        // Sampled ad
        var sampledAd = Liftium.getSampledAd(size);
        // Business rule: Don't do sampling if a tier 10 ad is present (exclusive)
        if (sampledAd !== false && Liftium.isValidCriteria(sampledAd) && Liftium.chain[slotname][0]['tier'] != "10"){
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


Liftium.callAd = function (slotname, iframe) {
	// Write out a _load div and call the ad
	var loadid = Liftium.getUniqueSlotId(slotname); 
	document.write('<div id="' + loadid + '">');
	Liftium._callAd(slotname);
	document.write("</div>");
};


/* Do the work of calling the ad tag */
Liftium._callAd = function (slotname, iframe) {
	Liftium.d("Calling ad for " + slotname, 1);
        var t = Liftium.getNextTag(slotname);

        var loadDivId = slotname + '_' + t["tag_id"];

        // Clear other load divs for the current slot
        Liftium.clearPreviousIframes(slotname);

        Liftium.d("Ad #" + t["tag_id"] + " for " + t['network_name'] + " called in " + slotname);
        Liftium.d("Config = ", 6, t);

        try { // try/catch block to isolate ad tag errors

                if (!Liftium.e(iframe)){
                        Liftium.callIframeAd(slotname, t);
                } else {
                        Liftium.slotname = slotname; // Capture this for ads that need the slotname
                        document.write('<div id="' + loadDivId + '">' + t["tag"] + "</div>");
                }
        } catch (e) {
                Liftium.tagErrors = Liftium.tagErrors || [];
                Liftium.tagErrors.push([t['tag_id'], Liftium.print_r(e)]);
                Liftium.d("Error loading tag: ", 0, e);
        }

        return true;

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
        if (a_tier > b_tier){
                return -1;
        } else if (a_tier < b_tier){
                return 1;
        } else {
                // Same tier, sort by weighted random
                var a_weight = Math.random() + (parseFloat(a['value']) || 0);
                var b_weight = Math.random() + (parseFloat(b['value']) || 0);
                return b_weight - a_weight;
        }
};


Liftium.clearPreviousIframes = function(slotname){
        var loadDiv = Liftium.getSlotLoadDiv(slotname);
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

/* Look through the list of ads in the potential chain, and return the best guaranteed fill */
Liftium.getAlwaysFillAd = function(size){

        for (var i = 0, l = Liftium.config.sizes[size].length; i < l; i++){
                var t = Liftium.config.sizes[size][i];

                if (t['guaranteed_fill'] === 'Yes' && Liftium.isValidCriteria(t)){
                        return Liftium.clone(t);
                }
        }

        // Rut roh
        return false;
};


/* Iterate through the chain and deliver the next ad tag to be called */
Liftium.getNextTag = function(slotname){
        // Do we need to build the chain?
        if (Liftium.e(Liftium.chain[slotname])){
                if ( Liftium.buildChain(slotname) === false){
			throw ("Unrecognized slotname" + slotname);
                }
        }

        var now = new Date();

        for (var i = 0, l = Liftium.chain[slotname].length; i < l; i++){
                if (!Liftium.e(Liftium.chain[slotname][i]['started'])){
                        continue;
                // Do we have enough time left?
                } else if ((now.getTime() - Liftium.slotTimer[slotname]) > Liftium.maxHopTime){
                        Liftium.d("Hop Time of " + Liftium.maxHopTime + " exceeded. Using the guaranteed fill", 2);
                        Liftium.slotTimer[slotname] = "exceeded";
                        var lastOne = l-1;
                        Liftium.chain[slotname][i]['started'] = now.getTime();
                        return Liftium.chain[slotname][lastOne];
                } else {
                        // Win nah!
                        Liftium.chain[slotname][i]['started'] = now.getTime();
                        return Liftium.chain[slotname][i];
                }

        }
        // Rut roh
        Liftium.d("Something went wrong, using the garaunteed ad for " + slotname);
        return Liftium.getAlwaysFillAd(Liftium.getSizeForSlotname(slotname));
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




Liftium.getSlotLoadDiv = function (slotname){
        return Liftium._(slotname + '_load') || Liftium._(slotname);
};


/* Return the available slots called on this page */
Liftium.getSlotNames = function (){
        var out = [];

        for (var chainslot in Liftium.chain){
                if (typeof Liftium.chain[chainslot] == "function"){
                        // Prototype js library overwrites the array handler and adds crap. EVIL.
                        continue;
                }
                out.push(chainslot);
        }

        for (var slot in Liftium.config.slotnames){
                if (typeof Liftium.config.slotnames[slot] == "function"){
                        // Prototype js library overwrites the array handler and adds crap. EVIL.
                        continue;
                }
                if (!Liftium.in_array(slot, out)){
                        out.push(slot);
                }
        }
        return out;
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
	// See if the slotname *is* the size
	if (slotname.match(/^[0-9]{1,3}x[0-9]{1,3}$/)){
		return slotname;
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

        var statMatch = Liftium.tagStats.toString().match(Liftium.getStatRegExp(tag_id));
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



Liftium.getUniqueSlotId = function(slotname) {
	for (var i = 0; i < 10; i++ ) {
		if (Liftium._(slotname + "_" + i) === null){
			return slotname + "_" + i;
		}
	}

	throw ("Error in Liftium.getUniqueSlotId. More than 10 ads of the same size?");
};


Liftium.iframeOnload = function(e) {

        var iframe = e.target || e;

        // Different browsers do/do not set the readyState. For the ones that don't set it here to normalize
        try { // Supress permission denied errors for cross domain iframes
                if (typeof iframe.readyState == "undefined" ) {
                        iframe.readyState = "complete";
                }
        } catch (e) {}
};


/* Emulate PHP's in_array, which will return true/false if a key exists in an array */
Liftium.in_array = function (needle, haystack){
    for (var key in haystack) {
        if (haystack[key] == needle) {
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
		throw("LiftiumOptions.pubid must be set");
	}

	Liftium.pullConfig();
	
	// Call the beacon on page load. Exclude browsers that we don't care about that misbehave
        //if (window.navigator.vendor != "Camino" && Liftium.getBrowser() != "opera") {
	Liftium.addEventListener(window, "load", Liftium.onLoadHandler);

        if (typeof document.readyState != "undefined") {
                // onunload only works reliably on browsers that have document.readyState, because we can't check to see if iframes are loaded
                Liftium.addEventListener(window, "unload", Liftium.sendBeacon);
        } else {
                // Fire an event when the iframe content loads for browsers that support it (firefox)
                Liftium.addEventListener(window, "DOMFrameContentLoaded", Liftium.iframeOnload);

        }

};


/* Browsers handle the "onload" event differently. Am I sure?
* Firefox - when everything is loaded
* Safari - when the bottom html tag is encountered
* IE - when page is loaded, not counting iframes
*/
Liftium.isCompletelyLoaded = function(e){
        if (document.readyState == "complete" ){
                // Everything is done. Now only if all browsers had this...
                return true;
        }

        var iframes = e.getElementsByTagName("iframe");
        for (var i = 0, l = iframes.length; i < l; i++){
                if (iframes[i].style.display == "none" || iframes[i].clientWidth < 50){
                        // It's either a pixel or hidden iframe, not an ad.
                        continue;
                }

                if (typeof iframes[i].readyState == "undefined" ){
                        return false;
                } else if (typeof iframes[i].readyState != "undefined" && iframes[i].readyState != "complete"){
                        return false;
                }
        }
        return true;
};


/* Check to see if the user from the right geography */
Liftium.isValidCountry = function (countryList){

        var ac = Liftium.getCountry();

        Liftium.d("Checking if " + ac + " is in:", 8, countryList);

        if (Liftium.in_array("row", countryList) &&
                  !Liftium.in_array(ac, ['us','uk','ca'])){
                Liftium.d("ROW targetted, and country not in us, uk, ca", 6);
                return true;
        }
        if (Liftium.in_array(ac, countryList)){
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
        } else if (Liftium.e(t['criteria'])){
                t['isValidCriteria'] = true;
                return t['isValidCriteria'];
        }

        try {
          for (var key in t.criteria){
                switch (key){
                  case 'Geography':
                        if ( ! Liftium.isValidCountry(t.criteria.Geography)){
                                Liftium.d("Ad #" + t["tag_id"] + " rejected because of Invalid Geography", 8);
                                t['isValidCriteria'] = false;
                                return t['isValidCriteria'];
                        }
                        break;
                  default:
                        // If it is not predefined, assume it is a page var. 
                        var list = eval("t.criteria." + key);
                        if (! Liftium.in_array(Liftium.getPageVar(key), list)){

                                Liftium.d("Ad #" + t["tag_id"] + " rejected because " + key + " not found in ", 8, list);
                                t['isValidCriteria'] = false;
                                return t['isValidCriteria'];
                        }
                        break; // Shouldn't be necessary, but silences a jslint error 
                }
          }

        } catch(e){
                var emsg = "Javascript Error while checking criteria: " + Liftium.print_r(e);
                Liftium.d(emsg);
                return false;
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

        // Rejection capping
        if (!Liftium.e(t["rej_cap"])){
                var r = Liftium.getTagStat(t["tag_id"], "r");
                if (r >= parseInt(t["rej_cap"], 10)){
                        Liftium.d("Ad #" + t["tag_id"] + " from " + t["network_name"] +
                                " invalid: " + r + " rejects is greater then rej_cap of " +
                                t["rej_cap"], 3);
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


/* Different browsers handle the onload event differently. Handle that here */
Liftium.onLoadHandler = function (){
        Liftium.beaconTries = Liftium.beaconTries || 0;
        if ( Liftium.isCompletelyLoaded(document)) {
                window.setTimeout("Liftium.sendBeacon()", Liftium.loadDelay);
        } else if (Liftium.beaconTries < 10){
                // Check again in a bit. 
                Liftium.loadDelay += 500;
                Liftium.beaconTries++;
                window.setTimeout("Liftium.onLoadHandler()", 500);
        } else {
                // Enough waiting, take whatever we have at this point
                Liftium.sendBeacon();
        }
};


/* Pull the configuration data from our servers */
Liftium.pullConfig = function (){

        var p = {
		"pubid" : window.LiftiumOptions.pubid,
                "v": 1.2 // versioning for config
        };

        // Allow for us to work in a dev environment
        if (! Liftium.e(Liftium.getRequestVal('liftium_dev_hosts') ||
              window.location.hostname.indexOf("delivery.dev.liftium.com") > -1)){
                // overwrite
                Liftium.baseUrl = "http://delivery.dev.liftium.com/";
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


/* Send a beacon back to our server so we know if it worked */
Liftium.sendBeacon = function (){
	// TODO
};


// Gentlemen, Start your optimization!
Liftium.init();

} // \if (typeof Liftium == "undefined" ) 
