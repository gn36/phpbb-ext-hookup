// Calendar
$(function () {
    $('#hookup_datetimepicker').datetimepicker({
        inline: true,
        sideBySide: true
    });
    $('#hookup_datetimepicker').hide();
});

$("#add_date_button").click(function(){
	//TODO: Uhrzeitänderungen ignorieren
	
	//$("#hookup_datetimepicker").css("display", "block");
	$("#hookup_datetimepicker").data('DateTimePicker').format('D.M.YYYY HH:mm:ss');
	$("#hookup_datetimepicker").toggle();
	$(document).on("dp.change", '#hookup_datetimepicker', function() {
		$("#add_date").val(
			$("#add_date").val() + "\n" + 
			$("#hookup_datetimepicker").data('DateTimePicker').date().format('D.M.YYYY HH:mm:ss')
		);
	})
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