//ajax execute...
var eAjaxData = '';
function eAjax(url, parameters, callback) {
	if (!confirm('请再次确定')) {
		return false;
	}

	$.post(url, parameters, function(data) {
		eAjaxData = data;		
		var func = callback + "()";
		eval(func);
	}, "json");
}

function deleteRow(){	
	if(eAjaxData.status=='true'){
		$('#row'+eAjaxData.id).fadeTo("slow",0.01).slideUp("slow");
	}
	else{
		alert(eAjaxData.status);
	}
}


//show form fields
function showFields(action) {
	$("#item_action").val(action);
	$("#approve_item, #unapprove_item").slideUp();
	$("#area3").val('');
	if(action == 'approve') {
		$("#approve_item").slideDown();
	}
	else {
		$("#unapprove_item").slideDown();
	}
	if($("#submit_form").css('display') == 'none') {
		$("#submit_form").css('display', 'block');
	}
}