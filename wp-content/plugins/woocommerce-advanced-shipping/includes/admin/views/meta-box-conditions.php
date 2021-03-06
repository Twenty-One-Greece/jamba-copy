<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $post;
$condition_groups = get_post_meta( $post->ID, '_was_shipping_method_conditions', true );

wp_nonce_field( 'was_conditions_meta_box', 'was_conditions_meta_box_nonce' );

?><div class='wpc-conditions wpc-conditions-meta-box'>

	<p>
		<strong><?php _e( 'Match all of the following rules to allow this shipping method:', 'woocommerce-advanced-shipping' ); ?></strong>
	</p><?php

	if ( ! empty( $condition_groups ) ) :

		foreach ( $condition_groups as $condition_group => $conditions ) :

			?><div class='wpc-condition-group wpc-condition-group-<?php echo absint( $condition_group ); ?>' data-group='<?php echo absint( $condition_group ); ?>'>

			<p class='or-text'><?php _e( 'Or match all of the following rules to allow this shipping method:', 'woocommerce-advanced-shipping' );?></p><?php

			foreach ( $conditions as $condition_id => $condition ) :
				$wp_condition = new WAS_Condition( $condition_id, $condition_group, $condition['condition'], $condition['operator'], $condition['value'] );
				$wp_condition->output_condition_row();
			endforeach;

			?></div>

			<p class='or-text'><strong><?php _e( 'Or', 'woocommerce-advanced-shipping' ); ?></strong></p><?php

		endforeach;

	else :

		?><div class='wpc-condition-group wpc-condition-group-0' data-group='0'><?php
			$wp_condition = new WAS_Condition();
			$wp_condition->output_condition_row();
		?></div><?php

	endif;

?></div>

<a class='button wpc-condition-group-add' href='javascript:void(0);'><?php _e( 'Add \'Or\' group', 'woocommerce-advanced-shipping' ); ?></a>
