# Reject all cross site requests - CSRF/XSS

This is sample PHP script that rejects all cross site requests. You wrote your own private PHP tools to help development and operations and don't want cross site attacks, i.e. CSRF/XSS, then this script may be for you.

You can use URL rewriter to protect your entire app from cross site attack, simple and easy with PHP.

## How to use sample

 1. Clone this repository
 1. Run built in web server like "php -d auto_prepend_file=site_protect.php -S 127.0.0.1:8888" in the cloned repository directory.
 1. Access http://127.0.0.1:8888/index.php from you browser
 1. Click "protected.php" link
 1. Then try to access without rtoken or broken token in URL. You should get error for invalid rtoken for protected.php. (index.php is not protected by config. You need at least one entry point to enter your web app.)

To use with real web servers, use php.ini's "auto_prepend_file" setting or include site_protect.php from your entry point script. A few configuration parameters can be used, refer to site_protect.php for details.

## How it works?

 - Create request validation token from session ID. (i.e. sha1(session_id())
 - Add request token to any <a> and <form> tags by output_add_rewrite_var()
 - Verify request token except PHP scripts defined in $entry_points

CSRF and XSS is common vulnerability. If your app uses single index.php entry point, modify script so that certain REQUEST_URI is protected. Your private sites/apps could be more secure with this.

Enjoy better security!
