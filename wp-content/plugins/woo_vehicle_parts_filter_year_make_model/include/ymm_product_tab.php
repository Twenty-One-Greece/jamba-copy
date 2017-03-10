<?php

/* 

Class Name: Add Product Details Tab Module



Author: Bigboss555

Author URI: http://sakosys.com

Copyright sakosys.com. All rights reserved

*/



add_filter( 'woocommerce_product_tabs', 'bbymm_parts_tab' );

function bbymm_parts_tab( $tabs ) {

// Adds the new tab

	$title = "Parts Information";
	$tabs['bbymm_tab'] = array(

		'title' => __( $title, 'woocommerce' ),

		'priority' => 50,

		'callback' => 'bbymm_parts_content'

	);

	return $tabs;

}

function bbymm_parts_content() {

	
	$ymm_data = taxomoyArrangeView();

	echo '<table class="table"><thead><tr><th align="left">Make</th><th align="left">Model</th><th align="left">Year</th></tr></thead>';

	echo '<tbody>';

	if(count($ymm_data) > 0){

		foreach($ymm_data as $ymm_val){

			echo '<tr><td>'.$ymm_val['year'].'</td><td>'.$ymm_val['make'].'</td><td>'.$ymm_val['model'].'</td></tr>';

		}

	}

	echo '</tbody>';

	echo '</table>';

}

function ymm_get_term_children($term_id){

	$termc = array();

	$termchildren = get_term_children( $term_id, 'bb_YMM_Filter' );

	foreach ( $termchildren as $child ) {

		$term = get_term_by( 'id', $child, 'bb_YMM_Filter' );

		if(isset($term->parent) && $term->parent != '' && $term->parent == $term_id){

			$termc[] = array(

				'name'		=> $term->name,

				'term_id'	=> $term->term_id,

				'parent'	=> $term->parent

			);

		}

	}

	return $termc;

}

function getLabel2Child($terms,$parent_id){
	$label_2 = array();
	foreach($terms as $term){
		if($term->parent == $parent_id){
			$child = getLabel3Child($terms,$term->term_id);
			$label_2[] = array(
				'term_id'	=> $term->term_id,
				'child'		=> $child,
				'name'		=> $term->name
			);
		}
	}
	return $label_2;
}
function getLabel3Child($terms,$parent_id){
	$label_3 = array();
	foreach($terms as $term){
		if($term->parent == $parent_id){
			$label_3[] = array(
				'term_id'	=> $term->term_id,
				'name'		=> $term->name
			);
		}
	}
	return $label_3;
}

function taxomoyArrangeView(){

	$arrObj = array();

	global $product,$wpdb;
	global $post;
	
	$product_id = '';
	
	$product_id = $post->ID;
	
	if($product_id != ''){
		$terms = get_the_terms( $product_id, 'bb_YMM_Filter' );
		if($terms != ''){
			foreach($terms as $term){
	
				if($term->parent == 0){
					$ymm = getLabel2Child($terms,$term->term_id);
					if(count($ymm) > 0){
						foreach($ymm as $fchild){
							if(count($fchild['child']) > 0){
								foreach($fchild['child'] as $ffchild){
									$arrObj[] = array(
										'year'	=> $term->name,
										'make'	=> $fchild['name'],
										'model'	=> $ffchild['name']
									);	
								}
							}
							else{
								$arrObj[] = array(
									'year'	=> $term->name,
									'make'	=> $fchild['name'],
									'model'	=> ''
								);	
							}
						}
					}
					else{
						$arrObj[] = array(
							'year'	=> $term->name,
							'make'	=> '',
							'model'	=> ''
						);
					}
	
				}
	
			}
		}

	}

	return $arrObj;

}