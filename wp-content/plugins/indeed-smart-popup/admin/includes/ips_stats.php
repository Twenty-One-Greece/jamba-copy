<?php
global $wpdb;
if(isset($_REQUEST['p_id']) && $_REQUEST['p_id']!='') $id = $_REQUEST['p_id'];
else{
	//getting the most visited popup as default
	$popups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}popup_windows
								ORDER BY {$wpdb->prefix}popup_windows.id DESC;");
	//VISITS AND VISITORS
	foreach($popups as $v){
    	$visit_arr = $wpdb->get_results("SELECT count(distinct visitor) as visitors, count(visit) as visits
                                     FROM {$wpdb->prefix}popup_visits
                                     WHERE popup_id = {$v->id};");
	    if(isset($visit_arr) && count($visit_arr)>0){
		      if(isset($visit_arr[0]->visits)) $visits[$v->id] = $visit_arr[0]->visits;
		      else $visits[$v->id] = 0;
		      if(isset($visit_arr[0]->visitors))  $visitors[$v->id] = $visit_arr[0]->visitors;
		      else $visitors[$v->id] = 0;
	    }
	}
	$init_value = 0;
	if(isset($visits) && count($visits)>0){
		foreach($visits as $key=>$value){
			if($value>$init_value){
				$init_value = $value;
				$id = $key;
			}
		}		
	}	
}

if(isset($id)){
	

$display_interval = 3;
if (isset($_REQUEST['display_interval'])) $display_interval = $_REQUEST['display_interval'];
$now = time();
switch ($display_interval) {
	case 1 :						//today
		$start_time = mktime(0, 0, 0, date('n'), date('j'));
		$end_time = time();
		$division = 'hour';
		$increment = 60*60;
		$multiplier = 24;
		break;
	case 2 :						//yesterday
		$start_time = mktime(0, 0, 0, date('n'), date('j')-1);
		$end_time = mktime(23, 59, 59, date('n'), date('j')-1);
		$division = 'hour';
		$increment = 60*60;
		$multiplier = 24;
		break;
	case 3 :						//last week
		$end_time = time();
		$start_time = $end_time - (7*24*60*60);
		$division = 'day';
		$increment = 60*60*24;
		$multiplier = 7;
		break;
	case 4 :						//last month
		$end_time = time();
		$start_time = $end_time - (30*24*60*60);
		$division = 'day';
		$increment = 60*60*24;
		$multiplier = 30;
		break;	
	default:
		$start_time = mktime(0, 0, 0, date('n'), date('j'));
		$end_time = time();
		$division = 'hour';
		$increment = 60*60;
		$multiplier = 24;
}
$start_date = strftime("%F %T",$start_time);
$end_date = strftime("%F %T",$end_time);
$prev_start_date = strftime("%F %T",($start_time - $multiplier * $increment));
$prev_end_date = strftime("%F %T",$start_time);
$visits = $wpdb->get_results("SELECT *
		FROM {$wpdb->prefix}popup_visits
		WHERE {$wpdb->prefix}popup_visits.visit >=  '$start_date' AND  {$wpdb->prefix}popup_visits.visit <=  '$end_date' AND popup_id = '$id' order by {$wpdb->prefix}popup_visits.visit asc");
if (count($visits)) {
	foreach($visits as $visit) {
		$visit_array = explode(' ', $visit->visit);
		$date_array  = explode('-', $visit_array[0]); 
		$time_array = explode(':', $visit_array[1]);
		$visit->visit = mktime($time_array[0], $time_array[1], $time_array[2], $date_array[1], $date_array[2], $date_array[0]);
	}
}

$mapVisits = getMapVisits($wpdb,$start_date,$end_date,$id);
$countsDevice = getDevices($visits);
$countsBrowser = getBrowsers($visits);
?>
<div id="main" style="width:97%;">
<div class="ips-dashboard-title">Smart Popup - <span class="second-text">Statistics</span></div>
	<div class="row-fluid">
		<div class="span6">
			<?php 
				$popups_arr = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}popup_windows
												ORDER BY {$wpdb->prefix}popup_windows.id DESC;");
				if(isset($popups_arr) && count($popups_arr)){
					?>
					<div class="input-medium" style="width:70%; margin-top:20px; margin-left:5px;">
						Select PopUp: 
						<select name="category" class='chosen-select' onchange="window.location = '<?php echo admin_url('admin.php');?>?page=ips_admin&tab=ips_stats&p_id='+this.value;" style="width:100%;height: 3em;background-color: #f7f7f7;">
							<?php 
								foreach($popups_arr as $popup_obj){
									$selected = '';
									if($popup_obj->id==$id) $selected = 'selected="selected"';
									$shortcode_str = 'indeed_popups';
									if(is_Popop_Under($popup_obj->id)) $shortcode_str = 'indeed_popup_under';
									?>
										<option value="<?php echo $popup_obj->id;?>" <?php echo $selected;?> ><?php echo $popup_obj->name;?> ( <?php echo $shortcode_str;?> id=<?php echo $popup_obj->id;?> )</option>
									<?php 	
								}
							?>
						</select>
					</div>
					<?php 	
				}
			?>

			<div class="input-medium" style="width:70%; margin-top:20px; margin-left:5px;">
				Select The Interval:
				<select name="category" class='chosen-select' data-nosearch="true" onchange="window.location = '<?php echo admin_url('admin.php');?>?page=ips_admin&tab=ips_stats&p_id=<?php echo $id;?>&display_interval='+this.value;" style="width:100%;height: 3em;background-color: #f7f7f7;">
					<option value="1" <?php if ($display_interval == 1) echo 'selected';?>>Today</option>
					<option value="2" <?php if ($display_interval == 2) echo 'selected';?>>Yesterday</option>
					<option value="3" <?php if ($display_interval == 3) echo 'selected';?>>Last week</option>
					<option value="4" <?php if ($display_interval == 4) echo 'selected';?>>Last month</option>
				</select>
			</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span6">
			<div class="box box-color box-bordered">
				<div class="box-title">
					<h3>
						<i class="icon-bar-chart"></i>
						Visits
					</h3>
				</div>
				<div class="box-content">
					<div class="statistic-big">
						<div class="top">
							<div class="left">
								
							</div>
							<div class="right">  
								<?php 
								echo (int)(count($visits));
								?> <span>
								<?php 
								$prev_visits = $wpdb->get_results("SELECT *
										FROM {$wpdb->prefix}popup_visits
										WHERE {$wpdb->prefix}popup_visits.visit >=  '$prev_start_date' AND  {$wpdb->prefix}popup_visits.visit <=  '$prev_end_date' AND popup_id = '$id'");
								
								if (count($visits) == count($prev_visits)) {
									echo '<i class="icon-circle-arrow-right"></i>';	
								}
								if (count($visits) > count($prev_visits)) {
									echo '<i class="icon-circle-arrow-up"></i>';
								}
								if (count($visits) < count($prev_visits)) {
									echo '<i class="icon-circle-arrow-down"></i>';
								}
								?>
								</span>
							</div>
						</div>
						<div class="bottom">
							<div class="flot medium" id="flot-audience"></div>
						</div>
			
					</div>
				</div>
			</div>
		</div>
<!-- 	</div>	 -->
<!-- 	<div class="row-fluid"> -->
		<div class="span6">
			<div class="box box-color lightred box-bordered">
				<div class="box-title">
					<h3>
						<i class="icon-bar-chart"></i>
						Visitors
					</h3>
				</div>
				<div class="box-content">
					<div class="statistic-big">
						<div class="top">
							
							<div class="right">
								<?php 
								$visitors = $wpdb->get_results("SELECT distinct(visitor)
										FROM {$wpdb->prefix}popup_visits
										WHERE {$wpdb->prefix}popup_visits.visit >=  '$start_date' AND  {$wpdb->prefix}popup_visits.visit <=  '$end_date' AND popup_id = '$id' order by {$wpdb->prefix}popup_visits.visit asc");
								echo (int)(count($visitors) );
								?> <span>
								<?php 
								$prev_visitors = $wpdb->get_results("SELECT distinct(visitor)
										FROM {$wpdb->prefix}popup_visits
										WHERE {$wpdb->prefix}popup_visits.visit >=  '$prev_start_date' AND  {$wpdb->prefix}popup_visits.visit <=  '$prev_end_date' AND popup_id = '$id' order by {$wpdb->prefix}popup_visits.visit asc");
								
								if (count($visitors) == count($prev_visitors)) {
									echo '<i class="icon-circle-arrow-right"></i>';	
								}
								if (count($visitors) > count($prev_visitors)) {
									echo '<i class="icon-circle-arrow-up"></i>';
								}
								if (count($visitors) < count($prev_visitors)) {
									echo '<i class="icon-circle-arrow-down"></i>';
								}
								?>
								</span>
							</div>
						</div>
						<div class="bottom">
							<div class="flot medium" id="flot-hdd"></div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</div>
	
		<div class="row-fluid">
		<div class="span6">
			<div class="box">
				<div class="box-title">
					<h3><i class="glyphicon-google_maps"></i>User Location</h3>
				</div>
				<div class="box-content">
					<div id="vmap"></div>
				</div>
			</div>
		</div>
		<div class="span6">
			<div class="box">
				<div class="box-title">
					<h3><i class="icon-cog"></i>Tiles Stats</h3>
				</div>
				<div class="box-content">
					<ul class="boxes">
						<li class="orange high long">
							<a href="#"><span class="count" style="padding-top:45px;"><i class="icon-eye-open"></i><span style="padding-top:10px; font-size:60px"><?php echo count($visits); ?></span></span><span class="name">Visits</span></a>
						</li>
						<li class="lime long">
							<a href="#"><span class="count"><i class="icon-user" style="margin-right:0px;"></i><span style="padding-top:0px; display: inline; font-size:40px"> <?php echo (int)(count($visitors) ); ?></span></span><span class="name">Visitors</span></a>
						</li>
						<li class="blue">
							<a href="#"><span class="count" style="padding-top:15px; font-size:45px"><i class="icon-globe" style="margin-right:0px;"></i><span style="padding-top:5px; font-size:30px">  <?php $all_devices = getDevices($visits); echo $all_devices['Computer'];?></span></span><span class="name">Web Visits</span></a>
						</li>
						<li class="pink">
							<a href="#"><span class="count" style="padding-top:15px; font-size:45px"><i class="icon-mobile-phone" style="margin-right:0px;"></i><span style="padding-top:5px; font-size:30px">
							<?php $all_devices = getDevices($visits); unset($all_devices['Computer']); rsort($all_devices); echo array_sum($all_devices);?></span></span><span class="name">Mobile Visits</span></a>
						</li>
						<li class="teal">
							<a href="#"><span class="count" style="padding-top:15px; font-size:45px"><i class="icon-trophy" style="margin-right:0px;"></i><span style="padding-top:5px; font-size:30px">
							<?php 
							$all_devices = getBrowsers($visits); 
							$max = 0; $name = 'N/A';
							foreach($all_devices as $k=> $v) {
								if ($v > $max) { $max = $v; $name = $k;}
							}
							echo $max;
							?></span><span class="name"><?php echo $name; ?></span></span></a>
						</li>
						<li class="magenta">
							<a href="#"><span class="count" style="padding-top:15px; font-size:45px"><i class="icon-phone" style="margin-right:0px;"></i><span style="padding-top:5px; font-size:30px">
							<?php 
							$all_devices = getDevices($visits); 
							unset($all_devices['Computer']); 
							$max = 0; $name = 'N/A';
							foreach($all_devices as $k=> $v) {
								if ($v > $max) {
									$max = $v; $name = $k;
								}
							}
							echo $max;
							?></span><span class="name"><?php echo $name; ?></span></span></a>
						</li>
						<li class="red long">
							<a href="#"><span class="count" style="padding-top:40px;"> <?php if (count($visitors))  echo number_format(count($visits) / count($visitors), 2); else  echo 'N/A'; ?></span><span class="name">Average hits</span></a>
						</li>
						
					</ul>	
					<div class="clear"></div>
				</div>
			</div>
		</div>					
	</div>
	
	<div class="row-fluid">
		<div class="span6">
			<div class="box">
				<div class="box-title">
					<h3>
						<i class="glyphicon-pie_chart"></i>
						Mobile Devices
					</h3>
				</div>
				<div class="box-content">
					<div id="flot-5" class='flot'></div>
				</div>
			</div>
		</div>
		<div class="span6">
			<div class="box">
				<div class="box-title">
					<h3>
						<i class="icon-globe"></i>
						Browsers
					</h3>
				</div>
				<div class="box-content">
					<div id="flot-6" class='flot'></div>
				</div>
			</div>
		</div>
	</div>
	
</div>
<script>
/* vMap */
<?php
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
			color : "#ffffff",
			hoverOpacity : .7,
			selectedColor : "#2d91ef",
			enableZoom : true,
			showTooltip : true,
			values : sample_data,
			scaleColors : [ "#8cc3f6", "#5c86ac" ],
			normalizeFunction : "polynomial",
		});
/* Chart 1 */
function showTooltip(e, t, n) {
	jQuery('<div id="tooltip" class="flot-tooltip tooltip"><div class="tooltip-arrow"></div>'  + n + "</div>").css({
		top : t - 43,
		left : e - 15
	}).appendTo("body").fadeIn(200)
}
if (jQuery("#flot-hdd").length > 0) {
	<?php 
			$js_vect_x[0] = '['.$start_time.'000, 0]';
			if ($division == 'hour') {
			$next_interval = mktime(strftime("%k", $start_time)+1, 0, 0, strftime("%m", $start_time)  , strftime("%d", $start_time), strftime("%Y", $start_time));
			} else {
				$next_interval = mktime(0, 0, 0, strftime("%m", $start_time)  , strftime("%d", $start_time)+1, strftime("%Y", $start_time));
			}
			$i = 0;			
			while ($next_interval < $end_time) {
				$start_x = strftime("%F %T",($next_interval- $increment));
				if ($i == 0) $start_x =  strftime("%F %T",($start_time));
				$end_x =  strftime("%F %T",$next_interval);
				$visitors = $wpdb->get_results(
						"SELECT count(distinct(visitor)) as unique_visitors
						FROM {$wpdb->prefix}popup_visits
						WHERE {$wpdb->prefix}popup_visits.visit >=  '$start_x' AND  {$wpdb->prefix}popup_visits.visit <=  '$end_x' AND popup_id = '$id' order by {$wpdb->prefix}popup_visits.visit asc");
				++$i;
				
				if ($next_interval < $end_time ) {
					$js_vect_x[$i] = '['.$next_interval.'000, 0]';
					if ( $visitors[0]->unique_visitors > 0 ) {
						$js_vect_x[$i] = '['.$next_interval.'000, '.$visitors[0]->unique_visitors.']';
					}
					unset($visitors);
				}
				$next_interval += $increment;
			}
			
			
			$start_x = strftime("%F %T",($next_interval - $increment));
			$end_x =  strftime("%F %T",$end_time);
			$visitors = $wpdb->get_results(
					"SELECT count(distinct(visitor)) as unique_visitors
						FROM {$wpdb->prefix}popup_visits
						WHERE {$wpdb->prefix}popup_visits.visit >=  '$start_x' AND  {$wpdb->prefix}popup_visits.visit <=  '$end_x' AND popup_id = '$id' order by {$wpdb->prefix}popup_visits.visit asc");
					++$i;
					$js_vect_x[$i] = '['.$end_time.'000, 0]';
					if ( $visitors[0]->unique_visitors > 0 ) $js_vect_x[$i] = '['.$end_time.'000, '.$visitors[0]->unique_visitors.']';
					
			$js_string_x = implode (", \n", $js_vect_x);
			
			if ($i > 12 ) $tickSize = 2; else $tickSize = 1; 
			?>
			
		var c = [ <?php echo $js_string_x; ?> ];
		jQuery.plot(jQuery("#flot-hdd"), [ {
			label : "Visitors",
			data : c,
			color : "#f36b6b"
		} ], {
			xaxis : {
				min : <?php echo $start_time. '000'; ?>,
				max : <?php echo $end_time.'000';?>,
				mode : "time",
				tickSize : [ <?php echo $tickSize; ?>, "<?php echo $division; ?>" ]
			},
			series : {
				lines : {
					show : !0,
					fill : !0
				},
				points : {
					show : !0
				}
			},
			grid : {
				hoverable : !0,
				clickable : !0
			},
			legend : {
				show : !1
			}
		});
		jQuery("#flot-hdd").bind(
				"plothover",
				function(e, t, n) {
					if (n) {
						if (previousPoint != n.dataIndex) {
							previousPoint = n.dataIndex;
							jQuery("#tooltip").remove();
							var r = n.datapoint[1].toFixed();
							showTooltip(n.pageX, n.pageY, n.series.label + " = " + r )
						}
					} else {
						jQuery("#tooltip").remove();
						previousPoint = null
					}
				})
	}
/* Chart 2 */
if (jQuery("#flot-audience").length > 0) {
	<?php 
	$js_vect[0] = '['.$start_time.'000, 0]';
	if ($division == 'hour') {
	$next_interval = mktime(strftime("%k", $start_time)+1, 0, 0, strftime("%m", $start_time)  , strftime("%d", $start_time), strftime("%Y", $start_time));
	} else {
		$next_interval = mktime(0, 0, 0, strftime("%m", $start_time)  , strftime("%d", $start_time)+1, strftime("%Y", $start_time));
	}
// 	echo "console.log('start ".strftime("%F %T",($start_time)). ' = '.$start_time. "');";
// 	echo "console.log('next ".strftime("%F %T",($next_interval)).' = '.$next_interval."');";
	
	$i = 0;
	$k = 0;
	$process_time = $next_interval;
	
	$i = 0;
	while ($process_time < $end_time) {
		$start_x = strftime("%F %T",($process_time - $increment));
		if ($i == 0) $start_x =  strftime("%F %T",($start_time));
		$end_x =  strftime("%F %T",$process_time);
		$sub_visits = $wpdb->get_results(
				"SELECT count(*) as unique_visitors
				FROM {$wpdb->prefix}popup_visits
				WHERE {$wpdb->prefix}popup_visits.visit >=  '$start_x' AND  {$wpdb->prefix}popup_visits.visit <=  '$end_x' AND popup_id = '$id' order by {$wpdb->prefix}popup_visits.visit asc");
		++$i;
		if ($process_time < $end_time ) {
			$js_vect[$i] = '['.$process_time.'000, 0]';
			if ($i == 1 ) $js_vect[$i] = '['.($process_time).'000, 0]';
			if ( $sub_visits[0]->unique_visitors > 0 ) {
					$js_vect[$i] = '['.$process_time.'000, '.$sub_visits[0]->unique_visitors.']';
					if ($i == 1 ) $js_vect[$i] = '['.($process_time).'000, '.$sub_visits[0]->unique_visitors.']';
				}
		}
		$process_time += $increment;
		unset($sub_visits);
	}
	
	$start_x = strftime("%F %T",($process_time- $increment));
	$end_x =  strftime("%F %T",$end_time);
	$sub_visits = $wpdb->get_results(
			"SELECT count(*) as unique_visitors
			FROM {$wpdb->prefix}popup_visits
			WHERE {$wpdb->prefix}popup_visits.visit >=  '$start_x' AND  {$wpdb->prefix}popup_visits.visit <=  '$end_x' AND popup_id = '$id' order by {$wpdb->prefix}popup_visits.visit asc");
	++$i;
	$js_vect[$i] = '['.$end_time.'000, 0]';
	if ( $sub_visits[0]->unique_visitors > 0 ) $js_vect[$i] = '['.$end_time.'000, '.$sub_visits[0]->unique_visitors.']';
	
	$js_string = implode (", \n", $js_vect);
	
	if ($i > 12 ) $tickSize = 2; else $tickSize = 1;
	?>
	var c = [ <?php echo $js_string; ?> ];
	jQuery.plot(jQuery("#flot-audience"), [ {
		label : "Visits",
		data : c,
		color : "#f36b6b"
	} ], {
		xaxis : {
			min : <?php echo $start_time. '000'; ?>,
			max : <?php echo $end_time.'000';?>,
			mode : "time",
			tickSize : [ <?php echo $tickSize; ?>, "<?php echo $division; ?>" ]
		},
		series : {
			lines : {
				show : !0,
				fill : !0
			},
			points : {
				show : !0
			}
		},
		grid : {
			hoverable : !0,
			clickable : !0
		},
		legend : {
			show : !1
		}
	});
	jQuery("#flot-audience").bind(
			"plothover",
			function(e, t, n) {
				if (n) {
					if (previousPoint != n.dataIndex) {
						previousPoint = n.dataIndex;
						jQuery("#tooltip").remove();
						var r = n.datapoint[1].toFixed();
						showTooltip(n.pageX, n.pageY, n.series.label + " = " + r)
					}
				} else {
					jQuery("#tooltip").remove();
					previousPoint = null
				}
			})
}
/* Pie chart 1 */	
if (jQuery("#flot-5").length > 0) {
	var c = [];
	<?php
	  if(isset($countsDevice) || count($countsDevice) > 0){
		$i = 0;  
	  	foreach($countsDevice as $key => $value){
			echo "c[".$i."]={ label : '".$key." (".$value.")', data: ".$value."};";
			$i++;
		}
	  }
	?>
	jQuery.plot(jQuery("#flot-5"), c, {
		series : {
			pie : {
				show : !0
			}
		}
	});
}
/* Pie chart 2 */
if (jQuery("#flot-6").length > 0) {
	var c = [];
		<?php
	  if(isset($countsBrowser) || count($countsBrowser) > 0){
		$i = 0;  
	  	foreach($countsBrowser as $key => $value){
			echo "c[".$i."]={ label : '".$key." (".$value.")', data: ".$value."};";
			$i++;
		}
	  }
	?>
	
	jQuery.plot(jQuery("#flot-6"), c, {
		series : {
			pie : {
				show : !0
			}
		}
	});
}
</script>				
<?php 
}//end of id exists
?>