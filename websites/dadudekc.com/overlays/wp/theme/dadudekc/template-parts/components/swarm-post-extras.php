<?php
/**
 * Swarm post extras: proof, quickstart, and CTA blocks.
 * SSOT: Swarm credibility + conversion blocks.
 *
 * @package DaDudeKC
 */

$metrics = dadudekc_get_proof_metrics();
$demo_url = 'https://weareswarm.site';
$repo_url = 'https://github.com/dadudekc';
$contact_url = dadudekc_get_contact_url();
$services_url = home_url('/services/');
$about_url = home_url('/about/');
$roadmap_url = home_url('/roadmap/');
$case_study_url = home_url('/case-studies/');
?>
<section class="swarm-proof">
    <div class="swarm-proof__header">
        <h2><?php esc_html_e('Proof: the Swarm ships real work', 'dadudekc'); ?></h2>
        <p><?php esc_html_e('A quick snapshot of the multi-agent system in motion—coordination, coverage, and execution in one loop.', 'dadudekc'); ?></p>
    </div>
    <div class="swarm-proof__grid">
        <figure class="swarm-proof__figure">
            <img src="<?php echo esc_url(get_stylesheet_directory_uri() . '/assets/images/swarm-dashboard-proof.svg'); ?>" alt="<?php esc_attr_e('Swarm dashboard activity preview', 'dadudekc'); ?>">
            <figcaption><?php esc_html_e('Live agent logs + task handoffs (sample view).', 'dadudekc'); ?></figcaption>
        </figure>
        <div class="swarm-proof__metrics">
            <div class="swarm-proof__metric">
                <span class="swarm-proof__value"><?php echo esc_html($metrics['ai_agents']); ?></span>
                <span class="swarm-proof__label"><?php esc_html_e('specialized agents coordinating the build', 'dadudekc'); ?></span>
            </div>
            <div class="swarm-proof__metric">
                <span class="swarm-proof__value"><?php echo esc_html($metrics['avg_delivery']); ?></span>
                <span class="swarm-proof__label"><?php esc_html_e('avg delivery window for scoped sprints', 'dadudekc'); ?></span>
            </div>
            <div class="swarm-proof__metric">
                <span class="swarm-proof__value"><?php echo esc_html($metrics['revenue_sites']); ?></span>
                <span class="swarm-proof__label"><?php esc_html_e('revenue sites shipped with Swarm support', 'dadudekc'); ?></span>
            </div>
            <a class="swarm-proof__demo" href="<?php echo esc_url($demo_url); ?>" target="_blank" rel="noopener">
                <?php esc_html_e('See the live Swarm demo →', 'dadudekc'); ?>
            </a>
        </div>
    </div>
</section>

<section class="swarm-quickstart">
    <h2><?php esc_html_e('10-minute quickstart', 'dadudekc'); ?></h2>
    <p><?php esc_html_e('Spin up the baseline loop locally with a repo clone, config, and a sample run.', 'dadudekc'); ?></p>
    <div class="swarm-quickstart__grid">
        <div class="swarm-quickstart__card">
            <h3><?php esc_html_e('Prerequisites', 'dadudekc'); ?></h3>
            <ul>
                <li><?php esc_html_e('Node.js 18+ / Python 3.11+', 'dadudekc'); ?></li>
                <li><?php esc_html_e('OpenAI API key or compatible provider', 'dadudekc'); ?></li>
                <li><?php esc_html_e('Git + Docker Desktop', 'dadudekc'); ?></li>
            </ul>
        </div>
        <div class="swarm-quickstart__card">
            <h3><?php esc_html_e('Commands', 'dadudekc'); ?></h3>
            <pre><code>git clone <?php echo esc_html($repo_url); ?>/swarm-starter.git
cd swarm-starter
cp .env.example .env
npm install
npm run swarm:demo</code></pre>
            <p class="swarm-quickstart__expect">
                <?php esc_html_e('Expected output:', 'dadudekc'); ?> <span><?php esc_html_e('“Swarm online · 8 agents connected · 12 tasks queued”', 'dadudekc'); ?></span>
            </p>
        </div>
    </div>
</section>

<section class="swarm-use-cases">
    <h2><?php esc_html_e('Where the Swarm delivers fastest', 'dadudekc'); ?></h2>
    <div class="swarm-use-cases__grid">
        <div class="swarm-use-cases__card">
            <h3><?php esc_html_e('Solo builders', 'dadudekc'); ?></h3>
            <p><?php esc_html_e('Get parallel coverage on research, planning, build, QA, and deploy without hiring a team.', 'dadudekc'); ?></p>
        </div>
        <div class="swarm-use-cases__card">
            <h3><?php esc_html_e('Startups', 'dadudekc'); ?></h3>
            <p><?php esc_html_e('Ship MVPs, maintain velocity, and document decisions with an always-on agent crew.', 'dadudekc'); ?></p>
        </div>
        <div class="swarm-use-cases__card">
            <h3><?php esc_html_e('Agencies', 'dadudekc'); ?></h3>
            <p><?php esc_html_e('Scale delivery, reduce QA escapes, and keep every client sprint documented and recoverable.', 'dadudekc'); ?></p>
        </div>
    </div>
</section>

<section class="swarm-results">
    <h2><?php esc_html_e('What gets faster and safer', 'dadudekc'); ?></h2>
    <ul>
        <li><?php esc_html_e('Regression tests run automatically before every handoff.', 'dadudekc'); ?></li>
        <li><?php esc_html_e('Deploy checklists auto-generated and verified by release agents.', 'dadudekc'); ?></li>
        <li><?php esc_html_e('Architecture drift reduced with consistent spec + QA coverage.', 'dadudekc'); ?></li>
        <li><?php esc_html_e('Project memory captured so you never lose context between cycles.', 'dadudekc'); ?></li>
    </ul>
</section>

<section class="swarm-cta">
    <div class="swarm-cta__content">
        <h2><?php esc_html_e('Ready to build with the Swarm?', 'dadudekc'); ?></h2>
        <p><?php esc_html_e('Book a call, grab the services menu, or join updates to see new builds as they ship.', 'dadudekc'); ?></p>
        <div class="swarm-cta__actions">
            <a class="button" href="<?php echo esc_url($contact_url); ?>"><?php esc_html_e('Book a call', 'dadudekc'); ?></a>
            <a class="button button-outline" href="<?php echo esc_url($services_url); ?>"><?php esc_html_e('Services & pricing', 'dadudekc'); ?></a>
            <a class="button button-outline" href="<?php echo esc_url($demo_url); ?>" target="_blank" rel="noopener"><?php esc_html_e('Live Swarm demo', 'dadudekc'); ?></a>
        </div>
        <form class="cta-row" aria-label="<?php esc_attr_e('Join updates', 'dadudekc'); ?>">
            <label class="screen-reader-text" for="swarm-email"><?php esc_html_e('Email', 'dadudekc'); ?></label>
            <input type="email" id="swarm-email" name="email" placeholder="<?php esc_attr_e('Email address', 'dadudekc'); ?>">
            <button type="submit"><?php esc_html_e('Join updates', 'dadudekc'); ?></button>
        </form>
    </div>
    <div class="swarm-cta__links">
        <h3><?php esc_html_e('Related links', 'dadudekc'); ?></h3>
        <ul>
            <li><a href="<?php echo esc_url($about_url); ?>"><?php esc_html_e('About the builder', 'dadudekc'); ?></a></li>
            <li><a href="<?php echo esc_url($services_url); ?>"><?php esc_html_e('Services overview', 'dadudekc'); ?></a></li>
            <li><a href="<?php echo esc_url($roadmap_url); ?>"><?php esc_html_e('Swarm roadmap', 'dadudekc'); ?></a></li>
            <li><a href="<?php echo esc_url($case_study_url); ?>"><?php esc_html_e('Case study library', 'dadudekc'); ?></a></li>
            <li><a href="<?php echo esc_url($repo_url); ?>" target="_blank" rel="noopener"><?php esc_html_e('Source on GitHub', 'dadudekc'); ?></a></li>
        </ul>
    </div>
</section>
