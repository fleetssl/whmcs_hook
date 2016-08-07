# Let's Encrypt for cPanel - WHMCS Hook

For [Let's Encrypt for cPanel](https://letsencrypt-for-cpanel.com).

The purpose of this hook is to automatically run AutoSSL on a cPanel user immediately after it is provisioned by WHMCS.

Note that your cPanel server should have a valid certificate on port 2087, otherwise this hook will fail.

## Requirements
* Any version of PHP 5+ or 7 is fine
* Let's Encrypt for cPanel 0.7.0 or higher
* cPanel servers with valid SSL certificate on port 2087.
* WHM server must not have 2FA enabled for your root user (but it probably doesn't if you are using WHMCS)

## Installation
Download `le4cp_whmcs_hook.php` to your WHMCS installation under `includes/hooks`, and chmod it to 0644.

The hook will run on all of your cPanel servers, but you may wish to customize this in the code.

The result of the hook execution will be written to the WHMCS activity log.

## Testing
You may optionally test the hook file:

```
php le4cp_whmcs_hook.php <hostname of cPanel server> <root access hash with spaces and newlines removed> <username>
```
