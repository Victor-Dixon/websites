<?php
/**
 * Spark branded logout — clears WordPress session without wp-login.php UI.
 *
 * @package SparkImmersiveAuth
 */

$wp_load = dirname(__DIR__) . '/wp-load.php';
if (!is_readable($wp_load)) {
    http_response_code(503);
    header('Content-Type: text/plain; charset=utf-8');
    exit('Spark logout is temporarily unavailable.');
}

require_once $wp_load;

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
    $nonce = isset($_POST['spark_logout_nonce'])
        ? sanitize_text_field(wp_unslash($_POST['spark_logout_nonce']))
        : '';

    if (
        !empty($_POST['spark_logout_confirm'])
        && wp_verify_nonce($nonce, 'spark_logout_confirm')
    ) {
        if (is_user_logged_in()) {
            wp_logout();
        }
        wp_safe_redirect(home_url('/spark-logout/?signed_out=1'));
        exit;
    }
}

$signed_out = isset($_GET['signed_out']) && sanitize_key(wp_unslash($_GET['signed_out'])) === '1';
$logged_in = is_user_logged_in();

$home = esc_url(home_url('/'));
$login = esc_url(home_url('/spark-login/'));
$signup = esc_url(home_url('/spark-signup/'));
$account = esc_url(home_url('/spark-account/'));
$action = esc_url(home_url('/spark-logout/'));
$nonce_field = wp_nonce_field('spark_logout_confirm', 'spark_logout_nonce', true, false);
$hero_img = esc_url(home_url('/assets/img/spark-logout-hero.png'));
$hero_cdn = 'https://cdn.discordapp.com/attachments/1356064334370836581/1513306308328030338/content.png';

if ($signed_out || !$logged_in) {
    $issue_tag = $signed_out ? 'Session Closed' : 'Off The Grid';
    $headline = $signed_out ? 'Signed Out' : 'Already Off The Grid';
    $message = $signed_out
        ? 'Your hero session is closed. The mask stays yours — come back when Meridian calls.'
        : 'No active Spark session detected. Log in when you are ready to continue your story.';
    $body_actions = sprintf(
        '<div class="comic-actions"><a class="comic-button primary" href="%s">Return To Cover</a><a class="comic-button blue" href="%s">Log In Again</a></div>',
        $home,
        $login
    );
} else {
    $issue_tag = 'End Session';
    $headline = 'Sign Out?';
    $message = 'Leave the field for now? Your Spark roster stays saved — only this browser session ends.';
    $body_actions = sprintf(
        '<form method="post" action="%s" class="logout-form"><div class="comic-actions">%s<input type="hidden" name="spark_logout_confirm" value="1" /><button type="submit" class="comic-button red">Sign Out</button><a class="comic-button" href="%s">Cancel</a></div></form>',
        $action,
        $nonce_field,
        $account
    );
}

$body_class = $signed_out ? ' signed-out-success' : '';
$cover_class = $signed_out ? ' is-signed-out' : '';

status_header(200);
nocache_headers();
header('Content-Type: text/html; charset=utf-8');
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title><?php echo esc_html($headline); ?> | Spark Protocol</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <meta name="robots" content="noindex,nofollow" />
  <style>
:root{
  --ink:#08080b;
  --paper:#fff4d6;
  --cream:#ffeec2;
  --red:#ff3155;
  --blue:#2dd4ff;
  --yellow:#ffd12f;
  --purple:#7c3cff;
  --green:#32ff9c;
  --shadow:#151019;
}
*{box-sizing:border-box}
html{scroll-behavior:smooth}
body{
  margin:0;
  min-height:100vh;
  color:var(--ink);
  font-family:Impact,"Arial Black",system-ui,sans-serif;
  background:
    radial-gradient(circle at 20% 10%,rgba(255,49,85,.35) 0 8%,transparent 9%),
    radial-gradient(circle at 80% 20%,rgba(45,212,255,.32) 0 9%,transparent 10%),
    radial-gradient(circle at 70% 80%,rgba(255,209,47,.34) 0 10%,transparent 11%),
    linear-gradient(135deg,#20122d,#090912 55%,#190f13);
  overflow-x:hidden;
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
body.signed-out-success:before{opacity:.14}
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
  box-shadow:0 6px 0 rgba(0,0,0,.45);
}
.comic-nav a{
  color:#fff;
  text-decoration:none;
  font-weight:950;
  letter-spacing:.03em;
  text-transform:uppercase;
  padding:8px 10px;
  border:3px solid transparent;
}
.comic-nav a:hover,.comic-nav .pop{
  color:var(--ink);
  background:var(--yellow);
  border-color:#fff;
  transform:rotate(-1deg);
}
.comic-wrap{
  width:min(1120px,calc(100% - 24px));
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
  font-size:.95rem;
  letter-spacing:.08em;
}
.issue-tag-success{
  background:var(--green);
  box-shadow:6px 6px 0 var(--ink),0 0 24px rgba(50,255,156,.35);
}
.comic-cover{
  position:relative;
  margin-top:24px;
  border:7px solid var(--ink);
  background:
    radial-gradient(circle at 76% 20%,rgba(255,255,255,.95) 0 8%,transparent 9%),
    linear-gradient(135deg,#ff3155 0 35%,#ffd12f 35% 58%,#2dd4ff 58% 100%);
  box-shadow:14px 14px 0 rgba(0,0,0,.65);
  padding:clamp(22px,5vw,54px);
  overflow:hidden;
}
.comic-cover-hero{
  display:grid;
  grid-template-columns:minmax(0,1fr) minmax(0,1.1fr);
  gap:clamp(16px,4vw,32px);
  align-items:center;
  background:
    linear-gradient(135deg,rgba(8,8,11,.92) 0%,rgba(32,18,45,.88) 100%),
    radial-gradient(circle at 20% 80%,rgba(255,49,85,.25) 0 40%,transparent 55%);
}
.comic-cover-hero.is-signed-out{
  box-shadow:14px 14px 0 rgba(0,0,0,.65),0 0 40px rgba(50,255,156,.12);
  border-color:var(--green);
}
.comic-cover:after{
  content:"SPARK!";
  position:absolute;
  right:-20px;
  bottom:16px;
  color:rgba(8,8,11,.12);
  font-size:clamp(5rem,16vw,13rem);
  transform:rotate(-11deg);
  line-height:.8;
  pointer-events:none;
}
.cover-hero-wrap{
  position:relative;
  z-index:2;
  border:5px solid var(--ink);
  box-shadow:8px 8px 0 rgba(0,0,0,.7);
  transform:rotate(-1.5deg);
  overflow:hidden;
  background:var(--ink);
}
.cover-hero{
  display:block;
  width:100%;
  height:auto;
  max-height:min(420px,55vh);
  object-fit:cover;
  object-position:center top;
}
.cover-copy{
  position:relative;
  z-index:2;
}
h1{
  position:relative;
  z-index:2;
  margin:12px 0;
  font-size:clamp(2.4rem,10vw,6.5rem);
  line-height:.78;
  letter-spacing:-.08em;
  text-transform:uppercase;
  color:#fff;
  -webkit-text-stroke:3px var(--ink);
  text-shadow:7px 7px 0 var(--ink);
}
p{
  font-family:system-ui,-apple-system,BlinkMacSystemFont,"Segoe UI",sans-serif;
  font-weight:800;
  line-height:1.5;
}
.caption{
  position:relative;
  z-index:2;
  display:inline-block;
  max-width:780px;
  background:var(--paper);
  border:5px solid var(--ink);
  padding:14px 16px;
  box-shadow:8px 8px 0 var(--ink);
  font-size:1.08rem;
}
.signed-out-banner{
  display:inline-flex;
  align-items:center;
  gap:8px;
  margin:0 0 14px;
  padding:8px 12px;
  background:rgba(50,255,156,.15);
  border:3px solid var(--green);
  color:#d8ffe8;
  font-family:system-ui,sans-serif;
  font-size:.92rem;
  font-weight:900;
  text-transform:uppercase;
  letter-spacing:.06em;
}
.comic-actions{
  position:relative;
  z-index:2;
  display:flex;
  flex-wrap:wrap;
  gap:12px;
  margin-top:24px;
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
  letter-spacing:.04em;
  font-family:Impact,"Arial Black",system-ui,sans-serif;
  cursor:pointer;
}
.comic-button.primary{background:var(--yellow)}
.comic-button.red{background:var(--red);color:#fff}
.comic-button.blue{background:var(--blue)}
.comic-button:hover{transform:translate(-2px,-2px);box-shadow:9px 9px 0 var(--ink)}
.logout-form{margin:0}
@media (max-width:760px){
  .comic-cover-hero{
    grid-template-columns:1fr;
  }
  .cover-hero{max-height:240px}
  h1{font-size:clamp(2.2rem,14vw,3.6rem)}
  .comic-actions{justify-content:center}
}
  </style>
</head>
<body class="<?php echo esc_attr(trim($body_class)); ?>">
  <nav id="spark-auth-nav" class="comic-nav" aria-label="Spark Protocol">
    <a href="<?php echo $home; ?>">Cover</a>
    <a href="<?php echo $account; ?>">Origin Rules</a>
    <a href="<?php echo $login; ?>">Log In</a>
    <a class="pop" href="<?php echo $signup; ?>">Join The Universe</a>
  </nav>

  <main class="comic-wrap">
    <span class="issue-tag<?php echo $signed_out ? ' issue-tag-success' : ''; ?>"><?php echo esc_html($issue_tag); ?></span>
    <section class="comic-cover comic-cover-hero<?php echo esc_attr($cover_class); ?>">
      <div class="cover-hero-wrap">
        <img
          class="cover-hero"
          src="<?php echo $hero_img; ?>"
          alt="Meridian skyline at dusk — a hero silhouette watches the city from a rooftop"
          width="640"
          height="480"
          loading="eager"
          decoding="async"
          onerror="this.onerror=null;this.src='<?php echo esc_attr($hero_cdn); ?>';"
        />
      </div>
      <div class="cover-copy">
        <?php if ($signed_out) : ?>
          <p class="signed-out-banner" role="status">Session ended successfully</p>
        <?php endif; ?>
        <h1><?php echo esc_html($headline); ?></h1>
        <div class="caption"><?php echo esc_html($message); ?></div>
        <?php echo $body_actions; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>
    </section>
  </main>
  <script src="/assets/js/spark-account-runtime.js?v=4"></script>
  <script src="/assets/js/spark-auth-nav.js?v=7"></script>
</body>
</html>
