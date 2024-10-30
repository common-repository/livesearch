<?php

require("../../../wp-blog-header.php");

// check to see if the user has enabled gzip compression in the WordPress admin panel
if ( !get_settings('gzipcompression') && !ini_get('zlib.output_compression') ) { 
	ob_start('ob_gzhandler'); 
}

// The headers below tell the browser to cache the file and also tell the browser it is JavaScript.
header("Cache-Control: public");
header("Pragma: cache");
$offset = 60*60*24*60;
$ExpStr = "Expires: ".gmdate("D, d M Y H:i:s",time() + $offset)." GMT";
$LmStr = "Last-Modified: ".gmdate("D, d M Y H:i:s",filemtime(__FILE__))." GMT";
header($ExpStr);
header($LmStr);
header('Content-Type: text/javascript; charset: UTF-8');

load_plugin_textdomain('livesearch');

?>
/*
// +----------------------------------------------------------------------+
// | Copyright (c) 2004 Bitflux GmbH                                      |
// +----------------------------------------------------------------------+
// | Licensed under the Apache License, Version 2.0 (the "License");      |
// | you may not use this file except in compliance with the License.     |
// | You may obtain a copy of the License at                              |
// | http://www.apache.org/licenses/LICENSE-2.0                           |
// | Unless required by applicable law or agreed to in writing, software  |
// | distributed under the License is distributed on an "AS IS" BASIS,    |
// | WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or      |
// | implied. See the License for the specific language governing         |
// | permissions and limitations under the License.                       |
// +----------------------------------------------------------------------+
// | Author: Bitflux GmbH <devel@bitflux.ch>                              |
// +----------------------------------------------------------------------+

*/
var liveSearchReq = false;
var t = null;
var liveSearchLast = "";
var isIE = false;
// on !IE we only have to initialize it once
if (window.XMLHttpRequest) {
	liveSearchReq = new XMLHttpRequest();
}

var hasSearchBox = false;

// Define variable containing location of livesearch.php script - necessary for the xmlhttprequest
var livesearch_submit_url = "<?php bloginfo('home'); ?>/wp-content/plugins/livesearch/livesearch.php?s=";

function liveSearchInit() {
	if (document.getElementById('livesearch')) {
		hasSearchBox = true;
	} else {
		return;
	}
	
	if (navigator.userAgent.indexOf("Safari") > 0) {
		document.getElementById('livesearch').addEventListener("keydown",liveSearchKeyPress,false);
	} else if (navigator.product == "Gecko") {
		document.getElementById('livesearch').addEventListener("keypress",liveSearchKeyPress,false);
		
	} else {
		document.getElementById('livesearch').attachEvent('onkeydown',liveSearchKeyPress);
		isIE = true;
	}

}

//
// addLoadEvent()
// Adds event to window.onload without overwriting currently assigned onload functions.
// Function found at Simon Willison's weblog - http://simon.incutio.com/
//
if (typeof window.addLoadEvent != 'function') {
	function addLoadEvent(func) {
		var oldonload = window.onload;
		if (typeof window.onload != 'function'){
			window.onload = func;
		} else {
			window.onload = function(){
			oldonload();
			func();
			}
		}
	}
}

addLoadEvent(liveSearchInit);	// run liveSearchInit onLoad

function liveSearchKeyPress(event) {
	if (!hasSearchBox) {
		return;
	}
	
	var highlight;
	
	if (event.keyCode == 40 )
	//KEY DOWN
	{
		highlight = document.getElementById("LSHighlight");
		if (!highlight) {
			highlight = document.getElementById("LSResult").firstChild.firstChild.nextSibling.nextSibling.firstChild;
		} else {
			highlight.removeAttribute("id");
			highlight = highlight.nextSibling;
		}
		if (highlight) {
			highlight.setAttribute("id","LSHighlight");
		} 
		if (!isIE) { event.preventDefault(); }
	} 
	//KEY UP
	else if (event.keyCode == 38 ) {
		highlight = document.getElementById("LSHighlight");
		if (!highlight) {
			highlight = document.getElementById("LSResult").firstChild.firstChild.nextSibling.nextSibling.lastChild;
		} 
		else {
			highlight.removeAttribute("id");
			highlight = highlight.previousSibling;
		}
		if (highlight) {
				highlight.setAttribute("id","LSHighlight");
		}
		if (!isIE) { event.preventDefault(); }
	} 
	//ESC
	else if (event.keyCode == 27) {
		highlight = document.getElementById("LSHighlight");
		if (highlight) {
			highlight.removeAttribute("id");
		}
		resetSearch();
	} 
}

function liveSearchStart() {
	if (t) {
		window.clearTimeout(t);
	}
	t = window.setTimeout("liveSearchDoSearch()",200);
}

function liveSearchProcessReqChange() {
	if (!hasSearchBox) {
		return;
	}
	
	if (liveSearchReq.readyState == 4) {
		var  res = document.getElementById("LSResult");
		res.style.display = "block";
		/* res.firstChild.innerHTML = liveSearchReq.responseText; */
		res.firstChild.innerHTML = '<div id="searchcontrols" class="oddresult"><div class="alignleft"><small>arrows &amp; &crarr;</small></div><div class="alignright"><small><a href="javascript://" title="Close results" onclick="resetSearch()" onkeypress="this.click();">close (esc)</a></small></div><br /></div><div id="searchheader" style="display: none;"><strong><small>top 10 results</small></strong></div>'+liveSearchReq.responseText;
	}
}

function liveSearchSubmit() {
	if (!hasSearchBox) {
		return false;
	}
	
	var highlight = document.getElementById("LSHighlight");
	if (highlight && highlight.firstChild) {
		window.location = highlight.firstChild.getAttribute("href");
		return false;
	} 
	else {
		return true;
	}
}

function closeResults() {
	if (!hasSearchBox) {
		return;
	}
	
    document.getElementById("LSResult").style.display = "none";
}

function resetSearch() {
	if (!hasSearchBox) {
		return;
	}
	
	closeResults();
	document.getElementById('livesearch').value = "<?php echo livesearch_get_option('default_string'); ?>";
	document.getElementById('livesearch').style.color = "<?php echo livesearch_get_option('default_blur_color'); ?>";
}

function liveSearchDoSearch() {
	if (!hasSearchBox) {
		return false;
	}
	var highlight;
	
	if (liveSearchLast != document.forms.searchform.s.value) {
	if (liveSearchReq && liveSearchReq.readyState < 4) {
		liveSearchReq.abort();
	}
	if ( document.forms.searchform.s.value == "") {
		document.getElementById("LSResult").style.display = "none";
		highlight = document.getElementById("LSHighlight");
		if (highlight) {
			highlight.removeAttribute("id");
		}
		return false;
	}
	if (window.XMLHttpRequest) {
	// branch for IE/Windows ActiveX version
	} else if (window.ActiveXObject) {
		liveSearchReq = new ActiveXObject("Microsoft.XMLHTTP");
	}
	liveSearchReq.onreadystatechange= liveSearchProcessReqChange;
	
	liveSearchReq.open("GET", livesearch_submit_url + document.forms.searchform.s.value);
	
	liveSearchLast = document.forms.searchform.s.value;
	liveSearchReq.send(null);
	}
	return true;
}
