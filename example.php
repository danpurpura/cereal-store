<?php

/**
 * Cereal Store Example
 *
 * This example will demonstrate how to use it to store settings within a URL.
 *
 * This is useful when you want to make a simple web app configurable without
 * requiring the use of a database, logins, cookies, etc.
 *
 * Note: This example is not meant to illustrate any best practices, etc.
 * It's intended to give an example of how the store may actually be used.
 *
 */

require_once('CerealStore.php');

$settings = new CerealStore();

// if we have any GET parameters, try to load them
if (!empty($_GET)) {
	$settings->unserialize(key($_GET));
}

// on a form post, add the settings and then redirect
if (!empty($_POST) && is_array($_POST['setting'])) {
	$settings->addArray($_POST['setting']);
	header('Location: example.php?'.$settings);
}

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>Cereal Store Example</title>
	</head>
	<body>
<?php

	// if there are no settings, display the configuration page
	if ($settings->isEmpty() || isset($_GET['edit'])) {
	
?>
		<form method="POST" action="example.php">
			<fieldset>
				<legend>Settings</legend>
				Setting 1: <input type="text" name="setting[1]" value="<?php echo $settings[1]; ?>" /><br/>
				Setting 2: <input type="checkbox" name="setting[2]" value="true" <?php echo ($settings->has(2) ? 'checked' : '') ?>/><br/>
				Setting 3: <input type="radio" name="setting[3]" value="Y" <?php echo ($settings->get(3) == 'Y' ? 'checked' : '') ?> /> Yes 
				           <input type="radio" name="setting[3]" value="N" <?php echo ($settings->get(3) != 'Y' ? 'checked' : '') ?> /> No<br/>
				Setting 1: <select name="setting[4]">
								<option <?php echo ($settings->get(4) == 'one' ? 'selected' : '') ?>>one</option>
								<option <?php echo ($settings->get(4) == 'two' ? 'selected' : '') ?>>two</option>
								<option <?php echo ($settings->get(4) == 'three' ? 'selected' : '') ?>>three</option>
						   </select></br>
				<input type="submit" value="Save" />
			</fieldset>
<?php
	} else {
		// display our settings
?>
		<dl>
<?php
		foreach($settings as $key => $value) {
			printf('<dt>Setting %d</dt><dd>%s</dd>', $key, $value);
		}
?>
		</dl>
		<div><a href="?<?php echo $settings ?>&edit">Change Settings</a></div>
<?php } ?>
	</body>
</html>
