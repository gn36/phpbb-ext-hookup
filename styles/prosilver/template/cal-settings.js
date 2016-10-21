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
    
    //Hide list if activedate is set:
    if ($("#show-all-dates-link").length != 0)
	{
    	$("#hookup-table").hide();
    	$("#hookup-add-date").hide();
    	$("#hookup-add-users").hide();
    	$("#hookup-add-groups").hide();
    	$("#hookup-submit").hide();
    	$("#tabs-boundary").hide();
	}
});

if ($("#add_date_button").length != 0)
{
	$("#add_date_button").click(function(){
		// Toggle visibility
		$("#hookup_datetimepicker").toggle();
		if ($('#hookup_datetimepicker').css('display') == 'block') 
		{
			$("#add_date_button").prop("value", $('#add_date_button').data('lhide'));
		}
		else
		{
			$("#add_date_button").prop("value", $('#add_date_button').data('lshow'));
		}
	});
}

// Activedate
function toggle_hookup_table() {
	if($('#hookup-table').css('display') == 'block') {
		var link_text = $("#show-all-dates-link").data('lshow');
	}
	else {
		var link_text = $("#show-all-dates-link").data('lhide');
	}
	$('#show-all-dates-link').html(link_text);
	
	$('#hookup-table').toggle();
	if($('#hookup-add-date').length != 0) 
	{
		$('#hookup-add-date').toggle();
	}
	if($('#hookup-add-users').length != 0)
	{
		$('#hookup-add-users').toggle();
	}
	if($('#hookup-add-groups').length != 0)
	{
		$('#hookup-add-groups').toggle();
	}
	if($('#hookup-submit').length != 0)
	{
		$('#hookup-submit').toggle();
	}
	if($('#tabs-boundary').length != 0)
	{
		$('#tabs-boundary').toggle();
	}
}