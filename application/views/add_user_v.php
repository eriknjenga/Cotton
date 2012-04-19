<script type="text/javascript">
	$(function() {
		$("#add_user_input").validationEngine();
	});

</script>
<?php
if (isset($user)) {
	$name = $user -> Name;
	$username = $user -> Username;
	$access_level = $user -> Access_Level;
	$phone_number = $user -> Phone_Number;
	$email_address = $user -> Email_Address;
	$user_id = $user -> id;
} else {
	$name = "";
	$username = "";
	$access_level = "";
	$phone_number = "";
	$email_address = "";
	$user_id = "";

}
$attributes = array("method" => "post", "id" => "add_user_input");
echo form_open('user_management/save', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Add New User
	</legend>
	<input type="hidden" name="editing_id" value="<?php echo $user_id;?>" />
	<p>
		<label for="name">Full Name: </label>
		<input id="name"  name="name" type="text"  value="<?php echo $name;?>" class="validate[required]"/>
		<span class="field_desc">Enter the full name for this user</span>
	</p>
	<p>
		<label for="username">Username: </label>
		<input id="username" name="username" type="text" value="<?php echo $username;?>" class="validate[required,minSize[4],maxSize[20]]"/>
		<span class="field_desc">Enter the username for this User</span>
	</p>
	<p>
		<label for="password">Password: </label>
		<input id="password" name="password" type="password" value="" class="validate[required],minSize[4],maxSize[20]"/>
		<span class="field_desc">Enter the password for this User</span>
	</p>
	<p>
		<label for="access_level">Access Level</label>
		<select name="access_level" class="dropdown validate[required]" id="access_level">
			<option></option>
			<?php
foreach($access_levels as $level_object){
			?>
			<option value="<?php echo $level_object -> id;?>" <?php
			if ($level_object -> id == $access_level) {echo "selected";
			}
			?>><?php echo $level_object -> Level_Name;?></option>
			<?php }?>
		</select>
		<span class="field_desc">Select this user's access level</span>
	</p>
	<p>
		<label for="phone">Phone Number: </label>
		<input id="phone"  name="phone" type="text"  value="<?php echo $phone_number;?>"/>
		<span class="field_desc">Enter the phone number for this user</span>
	</p>
	<p>
		<label for="email">Email Address: </label>
		<input id="email" name="email" type="text" value="<?php echo $email_address;?>" class="validate[required,custom[email]]"/>
		<span class="field_desc">Enter the email address for this User</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>