<?php 
include_once ISP_DIR_PATH.'includes/Ips_mail_services.php';
	// save/update
	if(isset($_REQUEST['ips_submit_bttn'])){
		ips_update_opt_in_metas();
	}	
	//getting the metas
	$meta_arr = ips_return_opt_in_metas();
?>
<script>
	base_url = '<?php echo get_site_url();?>';
</script>
<div class="ips_opt_in_settings_wrap">
<div class="ips-dashboard-title">Smart Popup - <span class="second-text">Opt-In Settings</span></div>

	<form action="" method="post">
		<div class="ips_stuffbox">
		  <h3>
		    <label>
		      Additional Main E-Mail
		    </label>
		  </h3>
		  <div class="inside">
		  	  <input type="text" name="ips_main_email" value="<?php echo $meta_arr['ips_main_email'];?>" style="min-width: 300px;"/>
			  <div class="submit">
			  		<input type="submit" value="Save changes" name="ips_submit_bttn" class="button button-primary button-large">
			  </div>
		  </div>
		</div>	

		<!-- aweber options -->
		<div class="ips_stuffbox">
		  <h3>
		    <label>
		      Aweber
		    </label>
		  </h3>
		  <div class="inside">
		    <table>
		      <tbody>
		        <tr>
		          <td>
		            Auth Code
		          </td>
		          <td>
		            <textarea id="ips_auth_code" name="ips_aweber_auth_code" style="min-width: 375px;"><?php echo $meta_arr['ips_aweber_auth_code'];?>
		            </textarea>
		          </td>
		        </tr>
		        <tr>
		          <td>
		          </td>
		          <td>
		            <a href="https://auth.aweber.com/1.0/oauth/authorize_app/751d27ee" target="_blank" class="indeed_info_link">
		              Get Your Auth Code From Here
		            </a>
		          </td>
		        </tr>
		        <tr>
		          <td>
		            Unique List ID:
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_aweber_list'];?>" name="ips_aweber_list" style="min-width: 375px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		          </td>
		          <td>
		            <a href="https://www.aweber.com/users/settings/" target="_blank" class="indeed_info_link">
		              Get Unique List ID
		            </a>
		          </td>
		        </tr>
		        <tr>
		          <td>
		          </td>
		          <td>
		            <div onclick="ips_connect_aweber( '#ips_auth_code' );" class="button button-primary button-large">
		              Connect
		            </div>
		          </td>
		        </tr>
		      </tbody>
		    </table>
		  <div class="submit">
		    <input type="submit" value="Save changes" name="ips_submit_bttn" class="button button-primary button-large">
		  </div>
		  </div>
		</div>
		
		<!--mailchimp option-->
		<div class="ips_stuffbox">
		  <h3>
		    <label>
		      Mailchimp
		    </label>
		  </h3>
		  <div class="inside">
		    <table>
		      <tbody>
		        <tr>
		          <td>
		            API Key
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_mailchimp_api'];?>" name="ips_mailchimp_api" style="min-width: 375px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		          </td>
		          <td>
		            <a href="http://kb.mailchimp.com/article/where-can-i-find-my-api-key" target="_blank" class="indeed_info_link">
		              Where can I find my API Key?
		            </a>
		          </td>
		        </tr>
		        <tr>
		          <td>
		            ID List
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_mailchimp_id_list'];?>" name="ips_mailchimp_id_list" style="min-width: 375px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		          </td>
		          <td>
		            <a href="http://kb.mailchimp.com/article/how-can-i-find-my-list-id/" target="_blank" class="indeed_info_link">
		              Where can I find List ID?
		            </a>
		          </td>
		        </tr>
		      </tbody>
		    </table>
			  <div class="submit">
			    <input type="submit" value="Save changes" name="ips_submit_bttn" class="button button-primary button-large">
			  </div>   
		  </div>
		</div>
		
		<!-- get response options -->
		<div class="ips_stuffbox">
		  <h3>
		    <label>
		      Get Response
		    </label>
		  </h3>
		  <div class="inside">
		    <table>
		      <tbody>
		        <tr>
		          <td>
		            GetResponse API Key
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_getResponse_api_key'];?>" name="ips_getResponse_api_key" style="min-width: 240px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		          </td>
		          <td>
		            <a href="http://www.getresponse.com/learning-center/glossary/api-key.html" target="_blank" class="indeed_info_link">
		              Where can I find my API Key?
		            </a>
		          </td>
		        </tr>
		        <tr>
		          <td>
		            GetResponse Campaign Token
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_getResponse_token'];?>" name="ips_getResponse_token" style="min-width: 240px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		          </td>
		          <td>
		            <a href="https://app.getresponse.com/campaign_list.html " target="_blank" class="indeed_info_link">
		              Where can I find Campaign Token?
		            </a>
		          </td>
		        </tr>
		      </tbody>
		    </table>
			  <div class="submit">
			    <input type="submit" value="Save changes" name="ips_submit_bttn" class="button button-primary button-large">
			  </div>    
		  </div>
		</div>
		
		<!-- campaign monitor options -->
		<div class="ips_stuffbox">
		  <h3>
		    <label>
		      Campaign Monitor
		    </label>
		  </h3>
		  <div class="inside">
		    <table>
		      <tbody>
		        <tr>
		          <td>
		            CampaignMonitor API Key
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_cm_api_key'];?>" name="ips_cm_api_key" style="min-width: 270px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		          </td>
		          <td>
		            <a href="https://www.campaignmonitor.com/api/getting-started/#apikey" target="_blank" class="indeed_info_link">
		              Where can I find API Key ?
		            </a>
		          </td>
		        </tr>
		        <tr>
		          <td>
		            CampaignMonitor List ID
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_cm_list_id'];?>" name="ips_cm_list_id" style="min-width: 270px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		          </td>
		          <td>
		            <a href="https://www.campaignmonitor.com/api/clients/#subscriber_lists" target="_blank" class="indeed_info_link">
		              Where can I find List ID?
		            </a>
		          </td>
		        </tr>
		      </tbody>
		    </table>
			  <div class="submit">
			    <input type="submit" value="Save changes" name="ips_submit_bttn" class="button button-primary button-large">
			  </div>
		  </div>
		</div>
		
		<!-- icontact options -->
		<div class="ips_stuffbox">
		  <h3>
		    <label>
		      IContact
		    </label>
		  </h3>
		  <div class="inside">
		    <table>
		      <tbody>
		        <tr>
		          <td>
		            iContact Username
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_icontact_user'];?>" name="ips_icontact_user" style="min-width: 280px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		            iContact App ID
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_icontact_appid'];?>" name="ips_icontact_appid" style="min-width: 280px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		          </td>
		          <td>
		            <a href="http://www.icontact.com/developerportal/documentation/register-your-app/" target="_blank" class="indeed_info_link">
		              Where can I get my App ID?
		            </a>
		          </td>
		        </tr>
		        <tr>
		          <td>
		            iContact App Password
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_icontact_pass'];?>" name="ips_icontact_pass" style="min-width: 280px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		            iContact List ID
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_icontact_list_id'];?>" name="ips_icontact_list_id" style="min-width: 280px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		          </td>
		          <td>
		            <div>
		              <a href="https://app.icontact.com/icp/core/mycontacts/lists" target="_blank" class="indeed_info_link">
		                Click on the list name:
		              </a>
		            </div>
		            <div>
		              Click on the list name and get the ID from the URL (ex:  https://app.icontact.com/icp/core/mycontacts/lists/edit/
		              <b>
		                ID_LIST
		              </b>
		              /?token=f155cba025333b071d49974c96ae0894 )
		            </div>
		            
		          </td>
		        </tr>
		      </tbody>
		    </table>
			  <div class="submit">
			    <input type="submit" value="Save changes" name="ips_submit_bttn" class="button button-primary button-large">
			  </div>
		  </div>
		</div>
		
		<!-- constant_contact options -->
		<div class="ips_stuffbox">
		  <h3>
		    <label>
		      Constant Contact
		    </label>
		  </h3>
		  <div class="inside">
		    <table>
		      <tbody>
		        <tr>
		          <td>
		            Constant Contact Username
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_cc_user'];?>" id="ips_cc_user" name="ips_cc_user" style="min-width: 260px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		            Constant Contact Password
		          </td>
		          <td>
		            <input type="password" value="<?php echo $meta_arr['ips_cc_pass'];?>" id="ips_cc_pass" name="ips_cc_pass" style="min-width: 260px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		          </td>
		          <td>
		            <div onclick="ips_get_cc_list( '#ips_cc_user', '#ips_cc_pass' );" class="button button-primary button-large">
		              Get Lists
		            </div>
		          </td>
		        </tr>
		        <tr>
		          <td>
		            Constant Contact List
		          </td>
		          <td>
		            <select id="ips_cc_list" name="ips_cc_list" style="min-width: 260px;">
		            	<?php 
		            		$list_name = '';
		            		if(isset($meta_arr['ips_cc_list']) && $meta_arr['ips_cc_list']!=''){
		            			//getting list name by id
		            			include_once ISP_DIR_PATH .'includes/email_services/constantcontact/class.cc.php';
		            			$cc = new cc($meta_arr['ips_cc_user'], $meta_arr['ips_cc_pass']);
		            			@$list_arr= $cc->get_list($meta_arr['ips_cc_list']);
		            			if(isset($list_arr['Name'])) $list_name = $list_arr['Name'];
		            		}
		            	?>
		            	<option value="<?php echo $meta_arr['ips_cc_list'];?>"><?php echo $list_name;?></option>
		            </select>
		          </td>
		        </tr>
		      </tbody>
		    </table>
			  <div class="submit">
			    <input type="submit" value="Save changes" name="ips_submit_bttn" class="button button-primary button-large">
			  </div>
		  </div>
		</div>
		
		<!-- wysija options -->
		<div class="ips_stuffbox">
		  <h3>
		    <label>
		      Wysija Contact
		    </label>
		  </h3>
		  <div class="inside">
		    <table>
		      <tbody>
		        <tr>
		          <td>
		            Select Wysija List:
		          </td>
		          <td>
                  	<?php
                    	$obj = new Ips_mail_services();
                        @$wysija_list = $obj->indeed_returnWysijaList();
                        if($wysija_list && count($wysija_list)>0){
                        	?>
                            <select name="ips_wysija_list_id">
                            	<?php
                                	foreach($wysija_list as $k=>$v){
                                		$selected = '';
                                		if($meta_arr['ips_wysija_list_id']==$k) $selected = 'selected="selected"';
                                        ?>
                                        	<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
                                        <?php
                                    }
                                ?>
                            </select>
                     <?php
                     	}else echo "No List available <input type='hidden' name='ics_wysija_list_id' value=''/> ";
                     ?>
		          </td>
		        </tr>
		      </tbody>
		    </table>
			  <div class="submit">
			    <input type="submit" value="Save changes" name="ips_submit_bttn" class="button button-primary button-large">
			  </div>
		  </div>
		</div>
		
		<!-- myMail options -->
		<div class="ips_stuffbox">
		  <h3>
		    <label>
		      MyMail
		    </label>
		  </h3>
		  <div class="inside">
		    <table>
		      <tbody>
		        <tr>
		          <td>
		            Select MyMail List:
		          </td>
		          <td>
					<?php 
                    	@$mymailList = $obj->indeed_getMyMailLists();
                        if(isset($mymailList) && $mymailList!=FALSE && count($mymailList)>0){
                        	?>
                            <select name="ips_mymail_list_id">
                            	<?php
                                foreach($mymailList as $k=>$v){
                                	$selected = '';
                                	if($meta_arr['ips_mymail_list_id']==$k) $selected = 'selected="selected"';
                                    ?>
                                    	<option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
                                <?php
                                }
                                ?>
                            </select>
                    <?php
                    	}else echo "No List available <input type='hidden' name='ips_mymail_list_id' value=''/> ";
		          	?>
		          </td>
		        </tr>
		      </tbody>
		    </table>
			  <div class="submit">
			    <input type="submit" value="Save changes" name="ips_submit_bttn" class="button button-primary button-large">
			  </div>
		  </div>
		</div>
		
		<!-- myMail options -->
		<div class="ips_stuffbox">
		  <h3>
		    <label>
		      Mad Mimi
		    </label>
		  </h3>
		  <div class="inside">
		    <table>
		      <tbody>
		        <tr>
		          <td>
		            Username Or Email:
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_madmimi_username'];?>" name="ips_madmimi_username" style="min-width: 260px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		            Api Key:
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_madmimi_apikey'];?>" name="ips_madmimi_apikey" style="min-width: 260px;">
		          </td>
		        </tr>
		        <tr>
		          <td>
		            List Name:
		          </td>
		          <td>
		            <input type="text" value="<?php echo $meta_arr['ips_madmimi_listname'];?>" name="ips_madmimi_listname" style="min-width: 260px;">
		          </td>
		        </tr>
		      </tbody>
		    </table>
			  <div class="submit">
			    <input type="submit" value="Save changes" name="ips_submit_bttn" class="button button-primary button-large">
			  </div>
		  </div>
		</div>
		
		<!-- email list -->
		<div class="ips_stuffbox">
		  <h3>
		    <label>
		      Saved E-mail List
		    </label>
		  </h3>
		  <div class="inside">
		  	<?php 
		  		@$email_list = get_option('ips_email_list');
		  		if($email_list==FALSE) $email_list = ''; 
		  	?>
		    <textarea disabled style="width: 450px;height: 100px;"><?php echo $email_list;?>
		    </textarea>
		  </div>
		</div>
	</form>
</div>