tinyMCEPopup.requireLangPack();

var BiblioRefDialog = {
	init : function() {
		var f = document.forms[0];

//		f.someval.value = tinyMCEPopup.editor.selection.getContent({format : 'text'});
//		f.somearg.value = tinyMCEPopup.getWindowArg('some_custom_arg');
	},

	insert : function() {
		// Insert the contents from the input into the document
		var pubmedID = document.forms[0].pubmedid.value;
		
		var xmlhttp;
		if (window.XMLHttpRequest) {
		  // code for IE7+, Firefox, Chrome, Opera, Safari
		  xmlhttp=new XMLHttpRequest();
		} else {
		  // code for IE6, IE5
		  xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
		}
		xmlhttp.open("GET", "http://skeletome.metadata.net/skelprof/skeletome-util/get-biblio?pubmedID=" + pubmedID, false);
		xmlhttp.send(null);
		resp = xmlhttp.responseText;
		nid = JSON.parse(resp).nid;
		
		if (nid == "null") {
		  alert('Unable to locate or retrieve the provided PubMed ID.');
		} else {
		  tinyMCEPopup.editor.execCommand('mceInsertContent', false, "[bib]" + nid + "[/bib]");
		}
		
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(BiblioRefDialog.init, BiblioRefDialog);
