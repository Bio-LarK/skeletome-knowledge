

var TagRecommendation = {};
var automaticTaggingFlag=true;
var previouslyAddedTags=new Array();

(function($) {
  
  TagRecommendation.toggleTag = function(tagElement) {
	  var tagText = tagElement.text().trim();
	  var tagField = $("#edit-field-cs-tags-und");

	  var currentTagText = tagField.val();
	  var currentTagTokens = currentTagText.split(",");

	  var flag = -1;
	  for ( var i = 0; i < currentTagTokens.length; i++ ) {
		  currentTagTokens[ i ] = currentTagTokens[ i ].trim();
		  if (tagText.toLowerCase() == currentTagTokens[ i ].toLowerCase()) {
			  flag=i;
			  break;
		  }
	  }
	  tagElement.css('background-color', 'transparent');
	  tagElement.css('color', '#9e2e7e');
	  if (flag >= 0) {
		  // remove tag
		  delete currentTagTokens[flag];
	  } else {
		  // add tag 
		  currentTagTokens.push(tagText);
		  tagElement.css('color', '#FFFFFF');
		  tagElement.css('background-color', '#9e2e7e');
	  }

	  var finalTagTokens = new Array();
	  var count = 0;
	  for (var i = 0; i < currentTagTokens.length; i++) {
	    if (currentTagTokens[i] != null) {
	      finalTagTokens[count++] = currentTagTokens[i].trim();
	    }
	  }
	  
	  tagField.val(finalTagTokens.join(", "));
  };

  //("#at-term-list")
  // - div: ("#at-term at-term-remove activeTagsRemove-processed")
  // -- span: ("#at-term-text")
  // -- span: ("#at-term-action-remove") - x
//    	  var tagField=$("div.at-term-list > div");
//	  activeTags.addTerm(this, "bla bla");

  // $("div.at-term-list > div")[0].children[0].childNodes[0].data
  //For each suggested tag if it is included in the list of annotation terms for the content, its background is changed
  TagRecommendation.updateTags = function() {
	  var automaticAddedCounter=0;

	  var tagField=$("#edit-field-cs-tags-und");
 
	  var currentTagText = tagField.val();
	  var tagTokens = currentTagText.split(",");

	  for (var i = 0; i < tagTokens.length; i++) {
	    tagTokens[i] = tagTokens[i].trim();
	  }
	  
	  // delete previously automatically added tags 
	  for ( var i = 0; i < previouslyAddedTags.length; i++ ) {
		  for ( var k = 0; k < tagTokens.length; k++ ) {
			  if (previouslyAddedTags[i] == tagTokens[k]) {
				  // must also delete tag item here? 
				  delete tagTokens[k] ; 
			  }
		  }
	  }

	  var currentTagTokens = new Array();
	  var count = 0;
	  for (var i = 0; i < tagTokens.length; i++) {
	    if (tagTokens[i] != null) {
	      currentTagTokens[count++] = tagTokens[i].trim();
	    }
	  }

	  var currentTags=currentTagTokens.join(", ");
	  tagField.val(currentTags);

	  $(".weighted_tag_suggestions").find('a').each(
		  function() {
			  var tagItem = $(this);
			  //if it is the tag link 
			  if (tagItem.hasClass('name')){
				  var tagHref = $(this).attr('href');
				  //Remove the link to the taxonomy term page in order for the term to be added in the term annotation field upon click
				  if (tagHref != ''){
					  tagItem.removeAttr('href');
				  }	
				  
				  tagItem.css('cursor','pointer');
				  //var tagId = tagItem.attr('id');
				  var tagText=tagItem.text().trim();
				  //var tagVid = tagId.substring(0,tagId.indexOf('_',0));

					  var flag=-1;
					  for ( var i = 0; i < currentTagTokens.length; i++ ) {
						  currentTagTokens[ i ] =currentTagTokens[ i ].trim();
						  if (tagText.toLowerCase() == currentTagTokens[ i ].toLowerCase()) {
							  flag=i;
							  break;
						  }	
					  }	
					  
					  if (flag>=0) {
//						  tagItem.css('background-color', '#99CC66');
						  tagItem.css('color', '#FFFFFF');
						  tagItem.css('background-color', '#9e2e7e');
					  }
					  else if (automaticTaggingFlag) {
						  previouslyAddedTags[automaticAddedCounter] = tagText;
						  TagRecommendation.toggleTag(tagItem);
						  currentTagTokens.push(tagText);
						  automaticAddedCounter++;
					  }
					  
					  tagItem.click(
						  function(event){
							  TagRecommendation.toggleTag(tagItem);
						  }
					  );
			  }
			  
			  
			  //if it is the link 
			  if (tagItem.hasClass('link')){
				  var tagLink= tagItem.attr('href');
				  previousTitle=currentTagItem.attr('title');
				  currentTagItem.attr('title', previousTitle + ' link is ' + tagLink );
				  tagItem.remove();
			  }
		  }
	  );
  };

  $(document).ready(
    function(){
	  if ($("#node-form").length>=0) {
	    TagRecommendation.updateTags();
	  }          
    }
  );
  
})(jQuery);
