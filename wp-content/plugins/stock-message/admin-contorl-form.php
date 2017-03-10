<div class='container-fluid'>

	<div class='row-fluid'>

		<div class='xs-col-12 sm-col-12 md-col-6 lg-col-6 text-center page-header'>
			<h3 class=''>WooCommerce Custom Stock Message</h3>
		</div>

		<div class='xs-col-12 sm-col-12 md-col-6 lg-col-6 text-center'>
			<?php $this->save_admin_form_data(); ?>
		</div>



		<div class='xs-col-12 sm-col-12 md-col-6 lg-col-6'>

			<form class='form-inline'method='post' action=''>

				<div class="form-group">
					<label for="<?php echo $this->__('out-of-stock-message');?>">Out Of Stock Message : </label>
					<input type="text" class="form-control" id="<?php echo $this->__('out-of-stock-message');?>" name='<?php echo $this->__('out-of-stock-message');?>' placeholder='<?php echo get_option($this->__('out-of-stock-message')); ?>'>
					<p class="help-block">This message will be shown when a product gets out of stock. You can add simple text or html code here</p>
				</div>

				<br />
				<br />

				<div class="form-group">
					<label for="<?php echo $this->__('in-stock-message');?>">In Stock Message : </label>
					<input type="text" class="form-control" id="<?php echo $this->__('in-stock-message');?>" name='<?php echo $this->__('in-stock-message');?>' placeholder='<?php echo get_option($this->__('in-stock-message')); ?>'>
					<p class="help-block">This message will be shown after the number of available products. You can add simple text or html here.</p>
				</div>

				<br />
				<br />

				<button type="submit" class="btn btn-primary">Save Settings</button>

			</form>

		</div>

		<div class='xs-col-12 sm-col-12 md-col-6 lg-col-6 text-center page-header'>
			<h6>Thanks for creating with <a href="https://wordpress.org/plugins/woocommerce-custom-stock-message">WooCommerce Custom Stock Message</a>.</h6>
			<p>If you like our plugin please leave us a <a href="https://wordpress.org/support/view/plugin-reviews/woocommerce-custom-stock-message">review</a>.</p>
		</div>

	</div>

</div>
