<?php

/**

 * Plugin Name: Spark Immersive Auth Routes

 * Description: Keep public Spark/Emergence routes off raw wp-login.php — use branded Spark login/logout instead.

 * Version: 1.1.0

 *

 * @package SparkImmersiveAuth

 */



if (!defined('ABSPATH')) {

    exit;

}



/**

 * Send visitors away from the default WordPress login screen.

 */

function spark_immersive_auth_redirect_login() {

    $action = isset($_REQUEST['action']) ? sanitize_key(wp_unslash($_REQUEST['action'])) : 'login';



    if ($action === 'logout') {

        if (is_user_logged_in()) {

            check_admin_referer('log-out');

            wp_logout();

        }

        wp_safe_redirect(home_url('/spark-logout/?signed_out=1'));

        exit;

    }



    if (is_user_logged_in()) {

        return;

    }



    if (defined('XMLRPC_REQUEST') && XMLRPC_REQUEST) {

        return;

    }



    if ($action === 'register' || $action === 'signup') {

        wp_safe_redirect(home_url('/spark-signup/'));

        exit;

    }



    if (in_array($action, ['lostpassword', 'rp', 'resetpass', 'confirmaction'], true)) {

        return;

    }



    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'GET' && !isset($_POST['log'])) {

        $target = home_url('/spark-login/');

        if (!empty($_REQUEST['redirect_to'])) {

            $target = add_query_arg(

                'redirect_to',

                rawurlencode(wp_unslash($_REQUEST['redirect_to'])),

                $target

            );

        }

        wp_safe_redirect($target);

        exit;

    }

}

add_action('login_init', 'spark_immersive_auth_redirect_login', 1);



/**

 * Rewrite hardcoded wp-login links emitted by the Emergence theme nav.

 */

function spark_immersive_auth_buffer_start() {

    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {

        return;

    }



    ob_start('spark_immersive_auth_rewrite_html');

}

add_action('template_redirect', 'spark_immersive_auth_buffer_start', 0);



/**

 * @param string $html Full page HTML.

 * @return string

 */

function spark_immersive_auth_rewrite_html($html) {

    if (stripos($html, 'wp-login.php') === false) {

        return $html;

    }



    $html = str_replace('/wp-login.php?action=register', '/spark-signup/', $html);

    $html = str_replace('wp-login.php?action=register', 'spark-signup/', $html);

    $html = str_replace('/wp-login.php?action=logout', '/spark-logout/', $html);

    $html = str_replace('wp-login.php?action=logout', 'spark-logout/', $html);

    $html = preg_replace('#/wp-login\.php(?:\?[^"\'>\s]*)?#i', '/spark-login/', $html);



    return $html;

}



/**

 * @param string $login_url Login URL.

 * @param string $redirect Redirect target.

 * @param bool   $force_reauth Force reauthentication.

 * @return string

 */

function spark_immersive_auth_login_url($login_url, $redirect, $force_reauth) {

    $url = home_url('/spark-login/');

    if (!empty($redirect)) {

        $url = add_query_arg('redirect_to', rawurlencode($redirect), $url);

    }



    return $url;

}

add_filter('login_url', 'spark_immersive_auth_login_url', 10, 3);



/**

 * @param string $logout_url Logout URL.

 * @param string $redirect Redirect target.

 * @return string

 */

function spark_immersive_auth_logout_url($logout_url, $redirect) {

    return home_url('/spark-logout/');

}

add_filter('logout_url', 'spark_immersive_auth_logout_url', 10, 2);



/**

 * @param string $register_url Register URL.

 * @return string

 */

function spark_immersive_auth_register_url($register_url) {

    return home_url('/spark-signup/');

}

add_filter('register_url', 'spark_immersive_auth_register_url');



/**

 * After logout, land on the branded confirmation page.

 *

 * @param string  $redirect_to Redirect target.

 * @param string  $requested_redirect_to Requested redirect.

 * @param WP_User $user User object.

 * @return string

 */

function spark_immersive_auth_logout_redirect($redirect_to, $requested_redirect_to, $user) {

    return home_url('/spark-logout/?signed_out=1');

}

add_filter('logout_redirect', 'spark_immersive_auth_logout_redirect', 10, 3);



/**

 * After login, land on the command post unless a specific redirect was requested.

 *

 * @param string           $redirect_to           Redirect target.

 * @param string           $requested_redirect_to Requested redirect.

 * @param WP_User|WP_Error $user                  User object.

 * @return string

 */

function spark_immersive_auth_login_redirect($redirect_to, $requested_redirect_to, $user) {

    if (!empty($requested_redirect_to)) {

        $path = wp_parse_url($requested_redirect_to, PHP_URL_PATH);

        if ($path && strpos($path, '//') !== 0) {

            return $requested_redirect_to;

        }

    }

    if (!empty($redirect_to) && $redirect_to !== admin_url()) {

        return $redirect_to;

    }

    return home_url('/');

}

add_filter('login_redirect', 'spark_immersive_auth_login_redirect', 10, 3);



/**

 * Hide the WordPress admin bar for anonymous visitors.

 */

function spark_immersive_auth_hide_admin_bar() {

    if (!is_user_logged_in()) {

        show_admin_bar(false);

    }

}

add_action('after_setup_theme', 'spark_immersive_auth_hide_admin_bar');



/**

 * Enqueue shared auth nav script on WordPress-rendered pages.

 */

function spark_immersive_auth_enqueue_nav_assets() {

    if (is_admin()) {

        return;

    }



    $runtime_path = ABSPATH . 'assets/js/spark-account-runtime.js';

    $nav_path = ABSPATH . 'assets/js/spark-auth-nav.js';



    if (is_readable($runtime_path)) {

        wp_enqueue_script(

            'spark-account-runtime',

            home_url('/assets/js/spark-account-runtime.js'),

            [],

            (string) filemtime($runtime_path),

            true

        );

    }



    if (is_readable($nav_path)) {

        wp_enqueue_script(

            'spark-auth-nav',

            home_url('/assets/js/spark-auth-nav.js'),

            ['spark-account-runtime'],

            (string) filemtime($nav_path),

            true

        );

    }

}

add_action('wp_enqueue_scripts', 'spark_immersive_auth_enqueue_nav_assets');



/**

 * Branded Spark logout confirmation page.

 *

 * @param bool $signed_out Whether the session was just cleared.

 * @param bool $logged_in Whether a session is still active.

 * @return string

 */

function spark_immersive_auth_render_logout_page($signed_out, $logged_in) {

    $nonce = wp_create_nonce('spark_logout_confirm');

    $title = $signed_out ? 'Signed Out' : 'End Your Session';

    $headline = $signed_out ? 'Story Paused' : 'Log Out';

    $caption = $signed_out

        ? 'Your Spark session is cleared. Return when Meridian calls again.'

        : 'Leave Meridian City safely — your hero file stays on the server until you return.';



    ob_start();

    ?>

<!doctype html>

<html <?php language_attributes(); ?>>

<head>

  <meta charset="<?php bloginfo('charset'); ?>" />

  <title><?php echo esc_html($title); ?> | Spark Protocol</title>

  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <style>

:root{

  --ink:#08080b;

  --paper:#fff4d6;

  --red:#ff3155;

  --blue:#2dd4ff;

  --yellow:#ffd12f;

}

*{box-sizing:border-box}

body{

  margin:0;

  min-height:100vh;

  color:var(--ink);

  font-family:Impact,"Arial Black",system-ui,sans-serif;

  background:

    radial-gradient(circle at 20% 10%,rgba(255,49,85,.35) 0 8%,transparent 9%),

    radial-gradient(circle at 80% 20%,rgba(45,212,255,.32) 0 9%,transparent 10%),

    linear-gradient(135deg,#20122d,#090912 55%,#190f13);

}

body:before{

  content:"";

  position:fixed;

  inset:0;

  pointer-events:none;

  opacity:.19;

  background-image:radial-gradient(#fff 1px,transparent 1px);

  background-size:8px 8px;

  mix-blend-mode:screen;

}

.comic-nav{

  position:sticky;

  top:0;

  z-index:50;

  display:flex;

  gap:10px;

  justify-content:center;

  align-items:center;

  flex-wrap:wrap;

  padding:12px;

  background:var(--ink);

  border-bottom:5px solid var(--yellow);

}

.comic-nav a{

  color:#fff;

  text-decoration:none;

  font-weight:950;

  letter-spacing:.03em;

  text-transform:uppercase;

  padding:8px 10px;

}

.comic-wrap{

  width:min(860px,calc(100% - 24px));

  margin:0 auto;

  padding:34px 0 76px;

}

.issue-tag{

  display:inline-block;

  background:var(--yellow);

  color:var(--ink);

  border:4px solid var(--ink);

  box-shadow:6px 6px 0 var(--ink);

  padding:8px 12px;

  transform:rotate(-2deg);

  text-transform:uppercase;

}

.comic-cover{

  margin-top:24px;

  border:7px solid var(--ink);

  background:linear-gradient(135deg,#ff3155 0 35%,#ffd12f 35% 58%,#2dd4ff 58% 100%);

  box-shadow:14px 14px 0 rgba(0,0,0,.65);

  padding:clamp(22px,5vw,54px);

}

h1{

  margin:12px 0;

  font-size:clamp(2.8rem,10vw,6rem);

  line-height:.82;

  text-transform:uppercase;

  color:#fff;

  -webkit-text-stroke:3px var(--ink);

  text-shadow:7px 7px 0 var(--ink);

}

.caption{

  display:inline-block;

  background:var(--paper);

  border:5px solid var(--ink);

  padding:14px 16px;

  box-shadow:8px 8px 0 var(--ink);

  font-family:system-ui,sans-serif;

  font-weight:800;

  line-height:1.5;

}

.form-panel{

  margin-top:24px;

  background:var(--paper);

  border:7px solid var(--ink);

  box-shadow:12px 12px 0 rgba(0,0,0,.7);

  padding:22px;

}

.comic-button{

  display:inline-block;

  text-decoration:none;

  border:5px solid var(--ink);

  color:var(--ink);

  background:#fff;

  padding:13px 16px;

  box-shadow:6px 6px 0 var(--ink);

  text-transform:uppercase;

  font-weight:950;

  margin-right:10px;

  margin-top:10px;

}

.comic-button.primary{background:var(--yellow)}

.comic-button.red{background:var(--red);color:#fff;border-color:var(--ink)}

button{

  cursor:pointer;

  border:5px solid var(--ink);

  background:var(--red);

  color:#fff;

  padding:13px 16px;

  box-shadow:6px 6px 0 var(--ink);

  text-transform:uppercase;

  font-weight:950;

  font-family:Impact,"Arial Black",system-ui,sans-serif;

}

  </style>

</head>

<body>

  <nav class="comic-nav" id="spark-auth-nav" aria-label="Spark Protocol">

    <a href="/">Cover</a>

    <a href="/spark-account/">Origin Rules</a>

    <a href="/spark-login/">Log In</a>

    <a class="pop" href="/spark-signup/">Join The Universe</a>

  </nav>

  <main class="comic-wrap">

    <span class="issue-tag"><?php echo $signed_out ? esc_html('Session Cleared') : esc_html('Secure Exit'); ?></span>

    <section class="comic-cover">

      <h1><?php echo esc_html($headline); ?></h1>

      <div class="caption"><?php echo esc_html($caption); ?></div>

    </section>

    <section class="form-panel">

      <?php if ($signed_out) : ?>

        <a class="comic-button primary" href="/">Return To Cover</a>

        <a class="comic-button" href="/spark-login/">Log In Again</a>

      <?php elseif ($logged_in) : ?>

        <form method="post" action="/spark-logout/">

          <input type="hidden" name="spark_logout_confirm" value="1" />

          <input type="hidden" name="spark_logout_nonce" value="<?php echo esc_attr($nonce); ?>" />

          <button type="submit">Confirm Log Out</button>

        </form>

        <a class="comic-button" href="/spark-generator/">Stay In Meridian</a>

      <?php else : ?>

        <p style="font-family:system-ui,sans-serif;font-weight:800;margin:0 0 12px;">No active Spark session detected.</p>

        <a class="comic-button primary" href="/spark-login/">Log In</a>

        <a class="comic-button" href="/">Return To Cover</a>

      <?php endif; ?>

    </section>

  </main>

  <script src="/assets/js/spark-account-runtime.js?v=4"></script>

  <script src="/assets/js/spark-auth-nav.js?v=7"></script>

</body>

</html>

    <?php

    return (string) ob_get_clean();

}

