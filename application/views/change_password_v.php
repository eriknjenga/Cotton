<script type="text/javascript">
	$(function() {
		$("#change_password").validationEngine();
	});

</script>
<?php
$attributes = array("method" => "post", "id" => "change_password");
echo form_open('user_management/save_new_password', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<!-- Fieldset -->
<fieldset>
	<legend>
		Change My Password
	</legend>
	<p>
		<label for="old_password">Current Password: </label>
		<input id="old_password"  name="old_password" type="password" class="validate[required]"/>
		<span class="field_desc">Enter your current password</span>
	</p>
	<p>
		<label for="new_password">New Password: </label>
		<input id="new_password" name="new_password" type="password" class="validate[required]"/>
		<span class="field_desc">Enter your desired password</span>
	</p>
	<p>
		<label for="new_password_confirm">Confirm New Password: </label>
		<input id="new_password_confirm" name="new_password_confirm" type="password" class="validate[required]"/>
		<span class="field_desc">Confirm the new password you entered</span>
	</p>
	<p>
		<input class="button" type="submit" value="Submit">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
<!-- End of fieldset -->
</form>