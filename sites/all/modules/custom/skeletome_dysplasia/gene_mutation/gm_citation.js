(function($) {

  var gmNid;
  var bdNid;
  var currentPubSelection = 0;
  var currentIndex;
  var biblioItems;
  
  Drupal.behaviors.gmCitation = {
    attach: function (context, settings) {
      skeletomeBase = Drupal.settings.basePath || [];
      gmNid = Drupal.settings.gmNid || [];
      bdNid = Drupal.settings.bdNid || [];
      biblioItems = Drupal.settings.biblioItems || [];

      var n_icons = {
	header: "ui-icon-circle-arrow-e",
	headerSelected: "ui-icon-circle-arrow-s"
      };
      $('.pub_accordion').accordion({
	autoHeight: false,
	collapsible: true,
	icons: n_icons,
      });
      $('.pub_accordion').bind('accordionchange', function(event, ui) {
	currentIndex = $('.pub_accordion').accordion('option', 'active');
      });

      for (i = 0; i < biblioItems.length; i++) {
	$("#delete_button_" + biblioItems[i]).button();
      }

      $("#add_gm_ssc_button").button().click(function() { $("#add_gm_scc_dialog").dialog( "open" ); });

      $("#add_gm_scc_dialog").dialog({
	autoOpen: false,
	height: 130,
	width: 550,
	modal: true,
	resizable: false,
	buttons: {
	  "Add": function() {
	    link = skeletomeBase + "node/" + gmNid + "/citation/add";
	    $.post(link, { gmNid: gmNid, pubNid: currentPubSelection, bdNid: bdNid}, function(data){
	      $("#gm_pub").val('');
	      $("#add_gm_scc_dialog").dialog( "close" );
	    });
	  },
	  Cancel: function() {
	    $("#gm_pub").val('');
	    $("#add_gm_scc_dialog").dialog( "close" );
	  }
	},
	close: function() {
	  $("#gm_pub").val('');
	  window.location.href = skeletomeBase + "node/" + gmNid;
	}
      });
      
      $("#gm_pub").autocomplete({
	source: function( request, response ) {
	  $.ajax({
	    url: skeletomeBase + "skeletome-util/get-biblio",
	    data: {
	      max_count: 10,
	      biblio_startsWith: request.term
	    },
	    success: function( data ) {
	      var actualData;
	      idx = data.indexOf("<div");
	      if (idx != -1) {
		actualData = data.substring(0, idx);
	      } else {
		actualData = data;
	      }
	      var JSON_data = eval('(' + actualData + ')');
	      response( $.map( JSON_data.pubs, function( item ) {
		return {
		  label: item.pub,
		  value: item.pub,
		  desc: item.auth,
		  id: item.nid
		}
	      }));
	    }
	  });
	},
	minLength: 2,
	select: function( event, ui ) {
	  currentPubSelection = ui.item.id;
	},
	open: function() {
	  $("#gm_pub").removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
	},
	close: function() {
	  $("#gm_pub").removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
	}
      })
      .data( "autocomplete" )._renderItem = function( ul, item ) {
	return $( "<li></li>" )
	  .data( "item.autocomplete", item )
	  .append( "<a>" + item.label + "<br>" + item.desc + "</a>" )
	  .appendTo( ul );
      };
      
    }
  };
})(jQuery);
