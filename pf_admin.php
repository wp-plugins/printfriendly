<?php
if(isset($_POST['pf_save'])){
	$error = false;
	// we should always have a response, but lets still validate it for security
	if($_POST['pf_button_type'] == 'pf-button.gif' || $_POST['pf_button_type'] == 'pf-button-big.gif' || $_POST['pf_button_type'] == 'pf-icon-small.gif' || $_POST['pf_button_type'] == 'pf-icon.gif' ||  $_POST['pf_button_type'] == 'pf-button-both.gif' ||  $_POST['pf_button_type'] == 'pf-icon-both.gif' ||  $_POST['pf_button_type'] == 'text-only' ||  $_POST['pf_button_type'] == 'custom-image'){
		update_option('pf_button_type',$_POST['pf_button_type']);
	}else{
		$error[] = 'Invalid entry for the "Pick Your Button" field. That isn\'t an option to select from! Quit messing with the DOM.';
	}
	
	// custom image
	if(isset($_POST['upload_image'])){
		if(trim($_POST['upload_image']) == ''){
			update_option('pf_custom_image',null);
		}else{
			$imagedata = @getimagesize($_POST['upload_image']);
			if(is_array($imagedata)){
				// well by golly we have an image
				update_option('pf_custom_image',$_POST['upload_image']);
			}else{
				// we need to trigger an error
				$error[] = 'Please provide a valid image link for the "Custom Image URL" field.';
			}
		}
	}
	
	// Custom Text
	if(isset($_POST['custom_text'])){
		update_option('pf_custom_text',htmlspecialchars($_POST['custom_text']) );
	}
	
	// Display custom image with custom text
	if(isset($_POST['custom_both'])){
		update_option('pf_custom_both',true);
	}else{
		update_option('pf_custom_both',null);
	}
	
	// display button
	if(isset($_POST['pf_show_list'])){
		if($_POST['pf_show_list']=="all" || $_POST['pf_show_list']=="single" || $_POST['pf_show_list']=="manual"){
			update_option('pf_show_list',$_POST['pf_show_list']);
		}else{
			// something isn't right
			$error[] = 'Invalid entry for the "Display Button" field. That isn\'t an option to select from! Quit messing with the DOM.';
		}
	}
	
	// now on to the right side
	
	// placement
	if(isset($_POST['pf_content_placement'])){
		if($_POST['pf_content_placement']=="before" || $_POST['pf_content_placement']=="after"){
			if($_POST['pf_content_placement']=='before'){
				update_option('pf_content_placement','before');
			}else{
				update_option('pf_content_placement',null);
			}
		}else{
			// something isn't right
			$error[] = 'Invalid entry for the "Placement" field. That isn\'t an option to select from! Quit messing with the DOM.';
		}
	}
	
	// position
	if(isset($_POST['pf_content_position'])){ 
		
		if($_POST['pf_content_position']=="left" || $_POST['pf_content_position']=="right" || $_POST['pf_content_position']=="center"){
			if($_POST['pf_content_position']=='left'){
				update_option('pf_content_position',null);
			}else{
				update_option('pf_content_position',$_POST['pf_content_position']);
			}
		}else{
			// something isn't right
			$error[] = 'Invalid entry for the "Position" field. That isn\'t an option to select from! Quit messing with the DOM.';
		}
	}
	
	//margin-top
	if(isset($_POST['pf_margin_top'])){
		if(is_numeric($_POST['pf_margin_top'])){
			update_option('pf_margin_top',$_POST['pf_margin_top']);
		}else{
			$error[] ='The value for "Margin Top" must be a number!';
		}
	}
	//margin-right
	if(isset($_POST['pf_margin_right'])){
		if(is_numeric($_POST['pf_margin_right'])){
			update_option('pf_margin_right',$_POST['pf_margin_right']);
		}else{
			$error[] ='The value for "Margin Right" must be a number!';
		}
	}
	//margin-bottom
	if(isset($_POST['pf_margin_bottom'])){
		if(is_numeric($_POST['pf_margin_bottom'])){
			update_option('pf_margin_bottom',$_POST['pf_margin_bottom']);
		}else{
			$error[] ='The value for "Margin Bottom" must be a number!';
		}
	}
	//margin-left
	if(isset($_POST['pf_margin_left'])){
		if(is_numeric($_POST['pf_margin_left'])){
			update_option('pf_margin_left',$_POST['pf_margin_left']);
		}else{
			$error[] ='The value for "Margin Left" must be a number!';
		}
	}
	
	//font color 
	if(isset($_POST['pf_text_color'])){
		if(preg_match('/^#[a-f0-9]{6}$/i', $_POST['pf_text_color'])){
			update_option('pf_text_color',$_POST['pf_text_color']);
		}else{
			$error[] ='The value for "Font Color" must be a valid hex code!';
		}
	}
	
	//font size
	if(isset($_POST['pf_text_size'])){
		if(is_numeric($_POST['pf_text_size'])){
			update_option('pf_text_size',$_POST['pf_text_size']);
		}else{
			$error[] ='The value for "Font Size" must be a number!';
		}
	}
}

?>
<div class="wrap">
	<h2>PrintFriendly: Settings</h2>
	<?php 
		if(isset($error)){
			if(is_array($error)){
				echo '<div class="error"><strong>Settings were not saved, The following problems exist:</strong>';
				foreach($error as $e){
					echo '<p>'.$e.'</p>';
				}
				echo '</div>';
			}else{
				echo '<div class="updated"><p><strong>Settings saved.</strong></p></div>';
			}
		}
	?>
<form action="" method="post" id="pf_admin">
		<div class="pf_box">
			<span>Feedback, questions, request a feature? <a href="mailto:support@printfriendly.com?subject=WordPress:"> Email PrintFriendly</a></span>
			<h3>Button Style</h3>
			<div class="pf_c">
				<div id="pf_buttons">
					<div class="buttongroup">
						<p><label class="preview_button">
							<?php echo pf_radio('pf-button.gif'); ?>
						</label></p>
						<p><label class="preview_button">
							<?php echo pf_radio('pf-button-big.gif'); ?>
						</label></p>
						<p><label class="preview_button">
							<?php echo pf_radio('pf-icon-small.gif'); ?>
						</label></p>
					</div>
					<div class="buttongroup">
						<p><label class="preview_button">
							<?php echo pf_radio('pf-icon.gif'); ?>
						</label></p>
						<p><label class="preview_button">
							<?php echo pf_radio('pf-button-both.gif'); ?>
						</label></p>
						<p><label class="preview_button">
							<?php echo pf_radio('pf-icon-both.gif'); ?>
						</label></p>
					</div>
					<div class="buttongroup larger">
						<p><label class="preview_button">
							<?php echo pf_radio('text-only'); ?> <span class="pf_tip">(text only)</span>
						</label></p>
						<p><label class="preview_button" id="pf_custom_image_label">
							<?php echo pf_radio('custom-image'); ?>	
							<?php if(get_option('pf_custom_image') == false){
								echo '<img src="'.plugins_url().'/'.str_replace(basename( __FILE__),"",plugin_basename(__FILE__)).'/images/noimage.png" alt="No Image"/>';
							} ?> <span class="pf_tip">(Custom Image)</span>
						</label></p>				
					</div>
				</div>
				<div id="pf_button_options">					
					<p>
						<label class="pf_label">Text Options</label>
						<div style="clear: both; overflow: hidden;">
							<div style="float:left; margin-right:5px;">
								<div id="colorSelector"><div style="background-color: <?php echo get_option('pf_text_color'); ?>;"></div></div>
							</div>
							<div style="float:left;">
								<input type="hidden" name="pf_text_color" id="pf_text_color" value="<?php echo get_option('pf_text_color'); ?>"/>
								<label class="pf_check"><input type="text" class="pf_px" id="pf_text_size" name="pf_text_size" value="<?php echo get_option('pf_text_size'); ?>"/><span>px <span class="pf_tip">(font size)</span></span></label>
							</div>
						</div>
					</p>
					<p>
						<label class="pf_label">Custom Text</label>
						<input type="text" name="custom_text" id="pf_custom_text" value="<?php echo get_option('pf_custom_text'); ?>"> <span class="pf_tip"> (optional, leave blank for default text)</span>
					</p>
					<p>
						<div id="pf_uploader">
							<label for="upload_image" class="pf_label">Custom Image URL: </label>
							<input id="upload_image" type="text" size="36" name="upload_image" value="<?php echo get_option('pf_custom_image'); ?>" />
							<input id="upload_image_button" type="button" value="Upload Image" class="button-primary"/>
							<p>Enter an URL or click "Upload Image" to Upload an Image.</p>
						</div>
					</p>
				</div>
				
			</div> <!-- end box c -->
		</div> <!-- end box -->
		
		<div class="pf_box">
			<h3>Button Position</h3>
			<div class="pf_c">
				<p class="pf_float">
					<label class="pf_label">Vertical Position</label>
					<label class="pf_check"><input type="radio" class="pf_content_placement" name="pf_content_placement" value="after"<?php if(get_option('pf_content_placement')==null){echo ' checked="checked"';} if(get_option('pf_show_list')=='manual'){echo ' disabled="disabled"';} ?>/><span>After Post/Page Content</span></label>
					<label class="pf_check"><input type="radio" class="pf_content_placement" name="pf_content_placement" value="before"<?php if(get_option('pf_content_placement')=='before'){echo ' checked="checked"';} if(get_option('pf_show_list')=='manual'){echo ' disabled="disabled"';} ?>/><span>Before Post/Page Content</span></label>
				</p>
				<p class="pf_float">
					<label class="pf_label">Horizontal Position</label>
					<label class="pf_check"><input type="radio" name="pf_content_position" value="left"<?php if(get_option('pf_content_position')==null){echo ' checked="checked"';} ?>/><span>Left</span></label>
					<label class="pf_check"><input type="radio" name="pf_content_position" value="center"<?php if(get_option('pf_content_position')=='center'){echo ' checked="checked"';} ?>/><span>Center</span></label>
					<label class="pf_check"><input type="radio" name="pf_content_position" value="right"<?php if(get_option('pf_content_position')=='right'){echo ' checked="checked"';} ?>/><span>Right</span></label>
				</p>
				<p class="pf_float">
					<label class="pf_label">Margin</label>
					<label class="pf_check"><input type="text" class="pf_px" name="pf_margin_top" value="<?php echo pf_margin_down('top'); ?>"/><span>px Top</span></label>
					<label class="pf_check"><input type="text" class="pf_px" name="pf_margin_right" value="<?php echo pf_margin_down('right'); ?>"/><span>px Right</span></label>
					<label class="pf_check"><input type="text" class="pf_px" name="pf_margin_bottom" value="<?php echo pf_margin_down('bottom'); ?>"/><span>px Bottom</span></label>
					<label class="pf_check"><input type="text" class="pf_px" name="pf_margin_left" value="<?php echo pf_margin_down('left'); ?>"/><span>px Left</span></label>
				</p>		
			</div> <!-- end box c -->
		</div> <!-- end box -->
		
		<div class="pf_box">
			<h3>Pages</h3>
			<div class="pf_c">
				<p>
					<label class="pf_label">Display Button:</label>
					<label class="pf_check"><input type="radio" class="pf_show_list" name="pf_show_list" value="all"<?php if(get_option('pf_show_list')=='all' || get_option('pf_show_list')==null ){echo ' checked="checked"';} ?>/> <span>All Posts and Pages</span></label>
					<label class="pf_check"><input type="radio" class="pf_show_list" name="pf_show_list" value="single"<?php if(get_option('pf_show_list')=='single' || get_option('pf_show_list')==1){echo ' checked="checked"';} ?>/> <span>Individual Posts and Pages <span class="pf_tip">(Not on frontpage / latest posts) </span></span></label>
					<label class="pf_check"><input type="radio" class="pf_show_list" name="pf_show_list" value="manual"<?php if(get_option('pf_show_list')=='manual'){echo ' checked="checked"';} ?>/> <span>Manual <span class="pf_tip">(see explaination below) </span></span></label>
				</p>
				<p>
					PrintFriendly, by default, automatically inserts itself into your blog/site. When Manual mode is enabled this allows you to add the button wherever you like. Simply paste the following code into your template file. <br/><span class="pf_tip">WARNING: You need to know how to edit your Theme files or you might cause errors on your page.</span>
					<h4>Code</h4>
						<code style="display:block;">&lt;?php if(function_exists('pf_show_link')){echo pf_show_link();} ?&gt;</code>			
				</p>
			</div> <!-- end box c -->
		</div> <!-- end Box -->
	<div style="clear:both;">
		<input type="submit" class="button-primary" value="Save Options" name="pf_save"/>
		<input type="reset" class="button-secondary" value="Cancel"/>
	</div>
	<br/><br/>
</form>
</div>