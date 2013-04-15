(function($) {

  var tids;
  var currentIndex = 0;
  var currentPhenoSelection = 0;
  var currentPubSelection = 0;

  Drupal.behaviors.bdPhenotype = {
    attach: function (context, settings) {
      skeletomeBase = Drupal.settings.basePath  || [];
      
      noPhenos = Drupal.settings.noPhenos || 0;
      tids = Drupal.settings.tids || [];
      bdNodeId = Drupal.settings.bdNodeId || 0;
      
      var icons = {
	      header: "ui-icon-circle-arrow-e",
	      headerSelected: "ui-icon-circle-arrow-s"
      };
      
      $("#add_pheno_button").button().click(function() { $("#add_pheno_dialog").dialog( "open" ); });

      $('.accordion').accordion({
	autoHeight: false,
	collapsible: true,
	icons: icons,
      });
      $('.accordion').bind('accordionchange', function(event, ui) {
	currentIndex = $('.accordion').accordion('option', 'active');
      });
      
      for (i = 0; i < noPhenos; i++) {
	$("#add_citation_button_" + i).button().click(function() { $("#add_scc_dialog").dialog( "open" ); });
      }
      for (i = 0; i < noPhenos; i++) {
	$("#remove_button_" + i).button().click(function() { window.location.href = skeletomeBase + "node/" + bdNodeId + "/phenotype/delete/" + tids[currentIndex]; });
      }

      // ADD PHENOTYPE

      $("#add_pheno_dialog").dialog({
	autoOpen: false,
	height: 130,
	width: 550,
	modal: true,
	resizable: false,
	buttons: {
	  "Add": function() {
	    link = skeletomeBase + "node/" + bdNodeId + "/phenotype/add/" + currentPhenoSelection;
	    $.post(link, { phenoTid: currentPhenoSelection}, function(data){
	      $("#phenotype").val('');
	      $("#add_pheno_dialog").dialog( "close" );
	    });
	  },
	  Cancel: function() {
	    $("#phenotype").val('');
	    $("#add_pheno_dialog").dialog( "close" );
	  }
	},
	close: function() {
	  $("#phenotype").val('');
	  window.location.href = skeletomeBase + "node/" + bdNodeId + "/phenotype";
	}
      });

      $("#phenotype").autocomplete({
	source: function( request, response ) {
	  $.ajax({
	    url: skeletomeBase + "skeletome-util/get-phenotype",
	    data: {
	      max_count: 12,
	      term_startsWith: request.term
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
	      response( $.map( JSON_data.terms, function( item ) {
		return {
		  label: item.term,
		  value: item.term,
		  id: item.tid
		}
	      }));
	    }
	  });
	},
	minLength: 2,
	select: function( event, ui ) {
	  currentPhenoSelection = ui.item.id;
	},
	open: function() {
	  $("#phenotype").removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
	},
	close: function() {
	  $("#phenotype").removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
	}
      });
      
      // ADD CITATION
      
      $("#add_scc_dialog").dialog({
	autoOpen: false,
	height: 130,
	width: 550,
	modal: true,
	resizable: false,
	buttons: {
	  "Add": function() {
	    link = skeletomeBase + "node/" + bdNodeId + "/phenotype/citation/add";
	    $.post(link, { pubNid: currentPubSelection, phenoTid: tids[currentIndex]}, function(data){
	      $("#publication").val('');
	      $("#add_scc_dialog").dialog( "close" );
	    });
	  },
	  Cancel: function() {
	    $("#publication").val('');
	    $("#add_scc_dialog").dialog( "close" );
	  }
	},
	close: function() {
	  $("#publication").val('');
	  window.location.href = skeletomeBase + "node/" + bdNodeId + "/phenotype";
	}
      });
      
      $("#publication").autocomplete({
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
	  $("#publication").removeClass( "ui-corner-all" ).addClass( "ui-corner-top" );
	},
	close: function() {
	  $("#publication").removeClass( "ui-corner-top" ).addClass( "ui-corner-all" );
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

