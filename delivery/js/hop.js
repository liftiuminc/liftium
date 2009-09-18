/********* Start of real hop.js ***************/
function XDM_onload (){
	window.XDM.send(top, "Liftium.iframeHop", [window.location]);
}
if ( top != self ) {
	// Tell the top window to hop 
	if (self.attachEvent){
		self.attachEvent("onload",XDM_onload); // Use onload for IE, which won't let you append to body until it's complete	
	} else {
		XDM_onload();
	}
} else {
	// not in an iframe, call the next ad
	window.Liftium.debug("Liftium.hop() called from hop.js", 3);
	window.Liftium.hop();
}

