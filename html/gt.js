// keep track if we are on grannytack page, if so, refresh
var onGrannytrack=true;

// load map for individual spot
function load_map(gps){
	var src = "http://maps.google.com/maps/api/staticmap?markers=size:mid|color:red|label:A|" + gps + "&sensor=false&size=500x300";
	$("#map").attr("src", src);
	// set onGrannytrack to false so the page doesnt refresh
	onGrannytrack=false;
}

// setup page refresh
// first, refresh function
function refreshGranny(){
	if (onGrannytrack==true){
		if($("#granny").val() == "" || $("#num_view").val() == "" || $("#zoom_level").val() == ""){
		// if these arent defined, load default page
			$("$content").load("grannytrack.php");
		} else {
		// else load the page with these values
			var page = "checkins.php?user=" + $("#granny").val() + "&num_view=" + $("#num_view").val() + "&zoom=" + $("#zoom_level").val();
			$("#checkin_area").load(page);
		}
	} // else do nothing
}
$(document).ready( function(){
	// disable cache
	$.ajaxSetup({ cache: false });
	
	
	// set refresh interval
	var refreshInterval = setInterval("refreshGranny()", 10000);
	
	// jquery init
	var ajax_load = "<img src=\"ajax-loader.gif\" />";

	// load ajax for main content area
	$("#grannytrack").click( function(){
		$("#content").load("grannytrack.php", function(){
			$("#num_view").trigger("change");}
			);
		onGrannytrack=true;
	});
	$("#view_alerts").click( function(){
		$("#content").load("view_alerts.php");
		onGrannytrack=false;
	});
	$("#new_alert").click( function(){
		$("#content").load("new_alert.php");
		onGrannytrack=false;
	});
	$("#ajaxLoad").ajaxStart(function(){
		$(this).show();
	});
	$("#ajaxLoad").ajaxComplete(function(){
		$(this).hide();
	});
	// load grannytracker initially
	$("#grannytrack").trigger('click');
	
	$(":button").live("click", function(){
		$("#server_msg").empty();
	});
	
});

function load_alert_page(){
	var page = "checkins.php?user=" + $("#granny").val() + "&num_view=" + $("#num_view").val() + "&zoom=" + $("#zoom_level").val();
	$("#checkin_area").load(page);
}
		
		
// grannytrack.php handlers
$("#num_view").live('change', function(){
	load_alert_page();
});
$("#granny").live("change", function(){
	load_alert_page();
});
$("#zoom_level").live("change", function(){
	load_alert_page();
});


// dynamic button handlers
// handle edit alert buttons
$(".edit_btn").live("click", function(e){
	
	var prefix = "edit_alert_";
	var id = e.target.id.substr(prefix.length);
	var page = "edit_alert.php?alert_id=" + id;
	$("#content").load(page);
});

// handle save button
$("#save_btn").live("click", function(){
	
	// when user clicks to save an alert edit
	var alert_id = $("#alert_id").val();
	var username = $("#username").val();
	var name = $("#name").val();
	var bpm_thresh = $("#bpm_thresh").val();
	var bpm_range = $("#bpm_range").val();
	var emails = $("#emails").val();
	var postData = "save_data=true&alert_id=" + alert_id + "&username=" + username + "&name=" + name + "&bpm_thresh=" + bpm_thresh + "&bpm_range=" + bpm_range + "&emails=" + emails;
	$.ajax({  
		type: "POST",  
		url: "edit_alert.php",  
		data: postData,  
		success: function() {  
			//display message back to user here  
			$("#server_msg").html("<h2>Alert edited</h2>");
			$("#content").load("view_alerts.php");
		}  
	}); 
	
	//alert("savebtn");
});	

// handle delete alert button
$(".del_btn").live("click", function(e){
	var prefix = "del_alert_";
	var id = e.target.id.substr(prefix.length);
	var page="delete_alert.php";
	var postData = "delete_alert_id=" + id;
	$.ajax({  
			type: "POST",  
			url: page,  
			data: postData,  
			success: function() {  
				//display message back to user here  
				$("#server_msg").html("<h2>Alert deleted!</h2>");
				$("#content").load("view_alerts.php");
			}  
	}); 
});

// handle create new alert button
$("#new_alert_btn").live("click", function(){
	var alert_id = $("#alert_id").val();
	var username = $("#username").val();
	var name = $("#name").val();
	var bpm_thresh = $("#bpm_thresh").val();
	var bpm_range = $("#bpm_range").val();
	var emails = $("#emails").val();
	var postData = "new_alert=new_alert&username=" + username + "&name=" + name + "&bpm_thresh=" + bpm_thresh + "&bpm_range=" + bpm_range + "&emails=" + emails;
	$.ajax({
		type: "POST",
		url: "new_alert.php",
		data: postData,
		success: function(){
			$("#server_msg").html("<h2>Alert added!</h2>");
			$("#content").load("view_alerts.php");
		}
	});
});
