$(document).ready(function(e) {
	$("#gcse").submit(function(e) {
		e.preventDefault();
		var element = google.search.cse.element.getElement('searchresults-only');
    	if ($(this).find('#cse-search-input-box-id').val()=='')
      		element.clearAllResults();
    	else
      		element.execute($(this).find('#cse-search-input-box-id').val());
    	document.location="#"+$(this).serialize();
		return false;
    });
	google.setOnLoadCallback(gcseCallback);
});
function gcseCallback(){
	$("#gcse").submit();
}