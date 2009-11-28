if (typeof LiftiumInspector == "undefined"){
	var LiftiumInspector = (function (){
		function LiftiumInspector () {
		}

		function findAds (){
			var divs = document.getElementsByTagName("div");
			var ads = [];
			for (var i=0, l=divs.length; i<l; i++) {
				var d = divs[i];
				if (Liftium.e(d.id) || !d.id.match(/^Liftium_/)){
					// Its not a Liftium ad, skip it.
					continue;
				}
				ads.push(d);
			}
			return ads;
		}
		LiftiumInspector.findAds = findAds;

		function createOverlays (ads){
			if (Liftium.e(ads)){
				// Nothing to do.
				return;
			}
			for (var i=0; i<ads.length; i++){
				var ad = ads[i];
				var slotname = ad.id; //.replace(/^Liftium_/, "");
				if (Liftium.e(Liftium.chain[slotname])){
					// No chain found for it the slot.
					continue;
				}

				var overlay = document.createElement("div");
				overlay.id = ad.id.replace(/^Liftium/, "LiftiumInspector");
				overlay.style.visibility = "hidden";
				overlay.style.position = "absolute";
				overlay.style.left = "0px";
				overlay.style.top = "0px";
				overlay.style.border = "1px solid #000";
				overlay.style.backgroundColor = "#fff";
				overlay.style.opacity = 0.85;

				var div = document.createElement("div");
				div.appendChild(document.createTextNode("Liftium Inspector"));
				overlay.appendChild(div);

				div = document.createElement("div");
				div.appendChild(document.createTextNode("Slot name: " + slotname));
				overlay.appendChild(div);

				// Add the tags from the chain.
				for (var j=0; j<Liftium.chain[slotname].length; j++){
					var tag = Liftium.chain[slotname][j];
					div = document.createElement("div");
					div.appendChild(document.createTextNode(tag.network_name + " tagid#" + tag.tag_id));
					overlay.appendChild(div);
				}

				// Add the scripts.
				var scripts = ad.getElementsByTagName("script");
				for (j=0; j<scripts.length; j++){
					var script = scripts[j];
					div = document.createElement("div");
					div.appendChild(document.createTextNode("Script " + script.src));
					overlay.appendChild(div);
				}

				// Show the overlay.
				ad.appendChild(overlay);
				overlay.style.visibility = "visible";
			}
		}
		LiftiumInspector.createOverlays = createOverlays;

		function init (){
			var ads = findAds();
			createOverlays(ads);
		}
		LiftiumInspector.init = init;

		return LiftiumInspector;
	})();

	LiftiumInspector.init();
}
