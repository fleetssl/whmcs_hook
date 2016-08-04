<?php

function le4cp_make_request($hostname, $access_hash, $action, $arguments) {
	$ch = curl_init();

	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);

	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Authorization: WHM root:" . preg_replace("'(\r|\n)'","", $access_hash)
	));

	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_URL, "https://$hostname:2087/cgi/letsencrypt-cpanel/letsencrypt.live.cgi?api=$action");
	curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($arguments));

	$response = curl_exec($ch);

	if($response === false) {
		throw new Exception("LE4CP API request failed: " . curl_error($response));
	}

	switch(curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
		case 200:
		case 204:
			return $response;
			break;
		default:
			throw new Exception("LE4CP API request failed response code: " . $response);
	}
}

/*
 * This function will launch the AutoSSL run for a user asynchronously.
 * It will not wait for the certificates to be issued, rather returning
 * immediately.
*/
function le4cp_autossl_async($hostname, $access_hash, $username) {
	return le4cp_make_request($hostname, $access_hash, 
		"run_autossl_for_user_async", array("username" => $username));
}

function le4cp_hook_aftermodulecreate($vars) {
	if($vars["params"]["moduletype"] !== "cpanel") {
		return;
	}
	try {
		$username = $vars["params"]["username"];
		le4cp_autossl_async(
			$vars["params"]["serverhostname"],
			$vars["params"]["serveraccesshash"],
			$vars["params"]["username"]);

		logActivity("LE4CP hook ran for $username");
	} catch(Exception $e) {
		logActivity("LE4CP hook ran for $username but got an exception: " . $e->getMessage());
	}
}

/* WHMCS Hook: AfterModuleCreate */
if(defined("WHMCS")) {
	add_hook("AfterModuleCreate", 1, "le4cp_hook_aftermodulecreate");
}

/* Test function */
if(php_sapi_name() === "cli") {
	print_r(le4cp_autossl_async($argv[1], $argv[2], $argv[3]));
}

?>
