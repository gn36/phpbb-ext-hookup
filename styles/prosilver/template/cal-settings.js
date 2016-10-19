// Calendar
$(function () {
    $('#hookup_datetimepicker').datetimepicker({
        inline: true,
        sideBySide: true,
        minDate: "now"
    });
    // Load moment script
    $.getScript($('#hookup_datetimepicker').data('localepath'))
     .success(function(){
    	 // Set locale
    	 $('#hookup_datetimepicker').data('DateTimePicker').locale($('#hookup_datetimepicker').data("locale"));
     });
    
    // Set some initial data:
    $('#hookup_datetimepicker').data('lastval', $('#hookup_datetimepicker').data('DateTimePicker').date().format('HH:mm'));
    
    // Hide the picker
    $('#hookup_datetimepicker').hide();
    
    // Set up event handler for button
    $(document).on("dp.change", '#hookup_datetimepicker', function() {
    	// Check what was changed
    	if ($('#hookup_datetimepicker').data('lastval') != $("#hookup_datetimepicker").data('DateTimePicker').date().format('HH:mm'))
		{
    		// Store new current value
        	$('#hookup_datetimepicker').data('lastval', $('#hookup_datetimepicker').data('DateTimePicker').date().format('HH:mm'));
    		return;
		}
        
    	// Add date to list
		$("#add_date").val(
			$("#add_date").val() + "\n" + 
			$("#hookup_datetimepicker").data('DateTimePicker').date().format('D.M.YYYY HH:mm')
		);
	});
    
});

$("#add_date_button").click(function(){
	// Toggle visibility
	$("#hookup_datetimepicker").toggle();
});





// Activedate
function toggle_hookup_table() {
	if(document.getElementById('hookup-table').style.display == 'block') {
		var link_text = '{LA_SHOW_ALL_DATES}';
		var set_display = 'none';
	}
	else {
		var link_text = '{LA_HIDE_ALL_DATES}';
		var set_display = 'block';
	}
	document.getElementById('show-all-dates-link').innerHTML = link_text;
	
	document.getElementById('hookup-table').style.display = set_display;
	if(document.getElementById('hookup-add-date')) 
	{
		document.getElementById('hookup-add-date').style.display = set_display;
	}
	if(document.getElementById('hookup-add-users'))
	{
		document.getElementById('hookup-add-users').style.display = set_display;
	}
	if(document.getElementById('hookup-add-groups'))
	{
		document.getElementById('hookup-add-groups').style.display = set_display;
	}
	if(document.getElementById('hookup-submit'))
	{
		document.getElementById('hookup-submit').style.display = set_display;
	}
	if(document.getElementById('tabs-boundary'))
	{
		document.getElementById('tabs-boundary').style.display = set_display;
	}
}