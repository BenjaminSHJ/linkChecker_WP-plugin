jQuery(document).ready(function($) {
	
	var field = acf.getField('field_5a7dc432d1999');
	
	field.on('change', function( e ){

    var vally = field.val();
		
	var data = {
		'action': 'my_action',
		'userno': vally    
	};
		
	$.ajax({
		url: ajax_object.ajax_url,
		type : 'post',
		data: data,
		dataType: 'json',
		success: function( data ) {
		
			$('#acf-field_5c3daeb8608ae').val(data.heightno);
			$('#acf-field_5c3daf8f608af').val(data.weightno);
			$('#acf-field_5c3dafc1608b1').val(data.ageno);
			$('#acf-field_5c3dafa5608b0').val(data.bodyfatno);
		}
	})
	
	
});
	
});