<script type="text/javascript">
	$(function() {
		$("#chief_accountant_login").validationEngine();
	});

</script>
<?php
$attributes = array("method" => "post", "id" => "chief_accountant_login");
echo form_open('user_management/user_authorization', $attributes);
echo validation_errors('
<p class="form_error">', '</p>
');
?>
<div class="message warning close">
	<h2>Warning!</h2>
	<p>
		<?php echo $message;?>
	</p>
	<p>
		You will need authorization from the chief accountant to proceed
	</p>
</div>
<fieldset>
	<legend>
		Chief Accountant Login
	</legend>
	<input type="hidden" name="request_url" value="<?php echo $request_url;?>" />
	<input type="hidden" name="authorization_from" value="chief_accountant" /> 
	<input type="hidden" name="error_callback" value="purchase_management/authorization_failed/<?php echo $depot->id;?>" />
	<input type="hidden" name="log_message" value="Authorized DPS entry from <?php echo $depot->Depot_Name?>" />
	<p>
		<label for="username">Username: </label>
		<input id="username"  name="username" type="text" class="validate[required]"/>
	</p>
	<p>
		<label for="password">Password: </label>
		<input id="password" name="password" type="password" class="validate[required]"/>
	</p>
	<p>
		<input class="button" type="submit" value="Authorize">
		<input class="button" type="reset" value="Reset">
	</p>
</fieldset>
</form>