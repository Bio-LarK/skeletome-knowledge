(function($) {
 
  var gms;
  var phenos;
  var currentGMIndex = 0;
  var currentPhenoIndex = 0;

  Drupal.behaviors.biblioCitations = {
    attach: function (context, settings) {
      skeletomeBase = Drupal.settings.basePath  || [];
      gms = Drupal.settings.gms || [];
      phenos = Drupal.settings.phenos || [];

      var icons = {
	      header: "ui-icon-circle-arrow-e",
	      headerSelected: "ui-icon-circle-arrow-s"
      };
      
      $('.gm_accordion').accordion({
	autoHeight: false,
	collapsible: true,
	icons: icons,
      });
      $('.gm_accordion').bind('accordionchange', function(event, ui) {
	currentGMIndex = $('.gm_accordion').accordion('option', 'active');
      });

      $('.pheno_accordion').accordion({
	autoHeight: false,
	collapsible: true,
	icons: icons,
      });
      $('.pheno_accordion').bind('accordionchange', function(event, ui) {
	currentPhenoIndex = $('.pheno_accordion').accordion('option', 'active');
      });

      for (i = 0; i < phenos.length; i++) {
	$("#delete_pheno_button_" + i).button();
      }

      for (i = 0; i < gms.length; i++) {
	$("#delete_gm_button_" + i).button();
      }
    }
  };
  
})(jQuery);

