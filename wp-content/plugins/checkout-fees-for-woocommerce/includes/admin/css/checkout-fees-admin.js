jQuery(document).ready(function() {
	jQuery("input[name='tabs']").click(function(){
		jQuery("label").each( function () {
			jQuery(this).removeClass('alg-clicked');
		});
		jQuery("label[for='"+this.id+"']").addClass('alg-clicked');
	});
});