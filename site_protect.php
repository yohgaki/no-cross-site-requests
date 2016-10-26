<?php
ob_start();

////////////////// PHP CONFIG ///////////////////
// Rewrite <a href=""> and <form action="">
ini_set('url_rewriter.tags', 'a=href,form=action');

// use_strict_mode=On should be use always
ini_set('session.use_strict_mode',1);

// Cookie based session - optional for PHP 7.1
// Use of Trans SID with PHP less than 7.1 is not recommended
ini_set('session.use_cookies', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.use_trans_sid', 0);

////////////////// USER CONFIG ///////////////////
// Set your entry points here
$entry_points   = ['/index.php'=>1];
const SESSION_EXPIRE = 1;
const RTOKEN_BACKUP  = 10;
const RTOKEN_NAME    = 'rtoken';

////////////////// FUNCTIONS /////////////////////
function sp_session_start() {
    if (session_status() == PHP_SESSION_ACTIVE) {
        return;
    }
    session_start();
    if (isset($_SESSION['deleted']) && $_SESSION['deleted'] + 600 < time()) {
        trigger_error('Your session might have been stolen! Or are you using wireless network?', E_USER_ERROR);
        die(); // Make sure PHP dies
    }
}


function sp_session_regenerate_id() {
    $_SESSION['deleted'] = time();
    if (PHP_VERSION_ID < 70000) {
        session_commit();
        session_start();
    }
    session_regenerate_id();
    unset($_SESSION['deleted']);
    sp_rtoken_generate();
    $_SESSION['created'] = time();
}


function sp_rtoken_check() {
    $rtoken = isset($_GET[RTOKEN_NAME]) ? $_GET[RTOKEN_NAME] :  '';
    foreach($_SESSION['rtokens'] as $tk) {
        if (hash_equals($rtoken, $tk)) {
            return;
        }
    }
    trigger_error('You have been attacked by cross site request! Or request token expired.', E_USER_ERROR);
    die(); // Make sure PHP dies
}


function sp_rtoken_generate() {
    $_SESSION['rtokens'][] = sha1(session_id());
    if (count($_SESSION['rtokens']) > RTOKEN_BACKUP) {
        array_splice($_SESSION['rtokens'], 0, count($_SESSION['rtokens']) - RTOKEN_BACKUP);
    }
}


////////////////// MAIN ///////////////////
sp_session_start();

if (empty($_SESSION['created']) || time() > $_SESSION['created'] + SESSION_EXPIRE) {
    sp_session_regenerate_id();
}

if (!isset($entry_points[$_SERVER['SCRIPT_NAME']])) {
    sp_rtoken_check();
}

output_add_rewrite_var(RTOKEN_NAME, end($_SESSION['rtokens']));

////////////////////// NOTES //////////////////////////
/*
 * If you app uses session_start() and session_regenerate_id(), replace them with
 * sp_session_start() and sp_session_regenerate_id().
*/
