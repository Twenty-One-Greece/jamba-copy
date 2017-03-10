<div id="main" style="width:97%;">
  <div class="ips-dashboard-title">Smart Popup - <span class="second-text">Dashboard Overall</span></div>
	<div class="row-fluid">
		<div class="span3">
		   <div class="home_count">
			<i class="icon-stat1 isp_bkcolor3"></i>
			<div class="stats">
				<h4>
					<strong>
						<?php 
							$num = ips_get_number_of_popups();
							if (!$num) echo 0;
							else echo ips_format_num_for_dashboard($num);
						?>
					</strong>
				</h4>
				<span>Total PopUps</span>
			</div>
		  </div>
		</div>
		<div class="span3">
		   <div class="home_count">
			<i class="icon-stat2 isp_bkcolor10"></i>
			<div class="stats">
				<h4>
					<strong>
						<?php 
							$num = ips_get_number_of_popups(false);
							if (!$num) echo 0;
							else echo ips_format_num_for_dashboard($num);
						?>					
					</strong>
				</h4>
				<span>Active PopUps</span>
			</div>
		  </div>
		</div>
		<div class="span3">
		   <div class="home_count">
			<i class="icon-stat3 isp_bkcolor5"></i>
			<div class="stats">
				<h4>
					<strong>
						<?php 
							$num = ips_get_number_of_visitors();
							if (!$num) echo 0;
							else echo ips_format_num_for_dashboard($num);
						?>
					</strong>
				</h4>
				<span>Total Visitors</span>
			</div>
		  </div>
		</div>
		<div class="span3">
		   <div class="home_count">
			<i class="icon-stat4 isp_bkcolor4"></i>
			<div class="stats">
				<h4>
					<strong>
						<?php 
							$num = ips_get_average_hits();
							if (!$num) echo 'N/A';
							else echo ips_format_num_for_dashboard($num);
						?>
					</strong>
				</h4>
				<span>Average Hits</span>
			</div>
		  </div>
		</div>
	</div>
	
		<div class="row-fluid">
		<div class="span8">
			<div class="box" style="background-color:#FFF;">
				<div class="box-title">
					<h3><i class="glyphicon-google_maps"></i>Visitor's Statistics</h3>
				</div>
				<div class="box-content">
					<div id="vmap"></div>
				</div>
			</div>
		</div>
		<div class="span4">
			<div class="box isp_bkcolor1 isp_home_last_popups">
				<div class="top-header ">
					<div class="icons-wrapper"><i class="icon-last"></i></div>
					<div class="title-wrapper"><h4>Last PopUps</h4> The newest 5 Popups</div>
					<div class="clear"></div>
				</div>
				<div class="pops-list">
				<ul>
					<?php 
						$popups = ips_get_last_five_popups();
						if ($popups && count($popups) && is_array($popups)){
							for ($i=0;$i<5;$i++){
								if (isset($popups[$i]->name)){							
									?>
									<li>
										<i class="icon-pop-list"></i>
										<div class="list-cont">
											<a href="">
												<?php echo $popups[$i]->name;?>
											</a>
										</div>
										<span>Set on Event Type: <span style="color:#fff; display:inline-block; padding-left: 0px;"><?php echo ips_get_popup_type_dashboard($popups[$i]->id);?></span></span>
									</li>
									<?php 
								}	
							}							
						}
					?>					
				</ul>
				</div>
			</div>
		</div>					
	</div>
	<div class="row-fluid">
		<div class="span4">
			<div class="home_info isp_bkcolor6">
			<i class="icon-home_info1"></i>
			<div class="info-title">Best OverAll PopUp</div>
			<div class="info-details">
				<?php 
					$best = ips_get_best_overall_popup();
					if ($best) echo $best;
					else echo 'N/A';
				?>
			</div>
			</div>
		</div>
		<div class="span2">
			<div class="home_info blacked">
			<i class="icon-home_info2"></i>
			<div class="info-title" style="font-size:20px;">
				<?php 
					$num = ips_get_popup_under_total_num();
					if (!$num) echo 0;
					else echo ips_format_num_for_dashboard($num);
				?> 
			 	PopUp Under
			</div>
			<div class="info-details">activated on website</div>
			</div>
		</div>
		<div class="span2">
			<div class="home_info isp_bkcolor5">
			<i class="icon-home_info3"></i>
			<div class="info-title">Top Browser</div>
			<div class="info-details">
				<?php 
					$browser_data = ips_get_most_popular_browser();
					if ($browser_data && is_array($browser_data) && isset($browser_data[0]) && isset($browser_data[1])){
						echo $browser_data[1] . ' Visits on ' . $browser_data[0];	
					} else {
						echo 'N/A';	
					}
				?>	
			</div>
			</div>
		</div>
		<div class="span2">
			<div class="home_info isp_bkcolor9">
			<i class="icon-home_info4"></i>
			<div class="info-title">Mobile</div>
			<div class="info-details">
				<?php 
					$num = ips_get_visits_num_from_mobile();
					if (!$num) echo 0;
					else echo ips_format_num_for_dashboard($num);					
				?>
				Visits
			</div>
			</div>
		</div>
		<div class="span2">
			<div class="home_info isp_bkcolor7">
			<i class="icon-home_info5"></i>
			<div class="info-title">Opt-In</div>
			<div class="info-details">
				<?php 
					$num = ips_get_count_submissions();
					if ($num) echo ips_format_num_for_dashboard($num);
					else echo 0;
				?>
				Saved Emails				
			</div>
			</div>
		</div>
	</div>
</div>
<script>
/* vMap */

<?php

	$mapVisits = ips_getMapVisits_for_dashboard();
	$display = 'var sample_data = {';  
	if(isset($mapVisits) && count($mapVisits) > 0){
		 	foreach($mapVisits as $key=> $value){
				$display .= '"'.strtolower($value->country).'":"'.$value->counts.'"';
				if($key == count($mapVisits)-1){
					 break;
				}else{
					$display .= ',';
				}
			}
		 }
		 $display .= '};';
		 echo $display;
		?>
		jQuery("#vmap").length > 0 && jQuery("#vmap").vectorMap({
			map : "world_en",
			backgroundColor : null,
			color : "#aaaaaa",
			hoverColor: "#e94937",
			selectedColor : "#2d91ef",
			enableZoom : true,
			showTooltip : true,
			values : sample_data,
			scaleColors : [ "#fce6e3", "#fb6b5b" ],
			normalizeFunction : "polynomial",
		});

</script>			