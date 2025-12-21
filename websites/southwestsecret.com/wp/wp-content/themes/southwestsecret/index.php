<?php
/**
 * Main Template File
 * 
 * @package SouthWestSecret
 */

get_header();
?>

<!-- Hero Section -->
<section id="home" class="hero">
    <div class="container">
        <div class="hero-logo">
            <img src="<?php echo get_template_directory_uri(); ?>/assets/images/logo.svg" alt="<?php bloginfo('name'); ?>" class="main-logo">
        </div>
        <h1 class="graffiti-title">
            <span class="graffiti">Welcome</span>
            <span class="bubble">Inside</span>
        </h1>
        <p class="tagline">Houston chopped & screwed hospitality — slowed down, poured up, and ready for your next gathering.</p>
        <p class="hero-subtitle">We open our door like a living room session: warm lighting, vinyl stories, and the city’s heartbeat looping in the background.</p>
        <div class="hero-buttons">
            <a href="#booking" class="btn-primary">Book a Home Session</a>
            <a href="#music" class="btn-outline">Listen to the Tape Wall</a>
        </div>
        <div class="hero-badges">
            <span>Third Ward energy</span>
            <span>Analog soul & modern polish</span>
            <span>Available for house parties, lounges & community pop-ups</span>
        </div>
    </div>
</section>

<section class="welcome-home">
    <div class="container">
        <div class="welcome-grid">
            <div class="welcome-intro">
                <h2>Step into our living room</h2>
                <p>SouthWest Secret started as a Houston apartment hang — neighbors drifting in, lights low, bass hugging the walls. That same spirit rides with us to every lounge, wedding after-party, and back-yard cookout. When we play, you’re family.</p>
                <ul class="welcome-list">
                    <li><span>🎚️</span> Curated chopped & screwed journeys tailored to the room</li>
                    <li><span>🪩</span> Mobile-friendly setup with warm lighting and analog visuals</li>
                    <li><span>🤝</span> Community-first bookings — fundraisers, creatives, and cultural pop-ups welcome</li>
                </ul>
            </div>
            <div class="welcome-card">
                <h3>What you can expect</h3>
                <p>We blend classics, underground gems, and hometown shout-outs to keep the night unhurried but unforgettable. Tell us the vibe and we’ll slow it down just right.</p>
                <a href="#booking" class="btn-primary">Let’s plan your session</a>
            </div>
        </div>
    </div>
</section>

<section class="neighborhood-sessions">
    <div class="container">
        <h2 class="section-heading">Sessions we host on the regular</h2>
        <p class="section-description">Pick the vibe that fits your people — we’ll bring the speakers, the slowed-down remixes, and the southern hospitality.</p>
        <div class="sessions-grid">
            <article class="session-card">
                <h3>House Sessions</h3>
                <p>Invite the crew over and we transform your living room into a late-night Houston lounge with curated setlists and low-key lighting.</p>
                <a href="#booking" class="session-link">Book a house night →</a>
            </article>
            <article class="session-card">
                <h3>Private Events</h3>
                <p>Birthdays, anniversaries, creative launches — we soundtrack the stories that matter with thoughtful pacing and personalized drops.</p>
                <a href="#booking" class="session-link">Plan your event →</a>
            </article>
            <article class="session-card">
                <h3>Community Pop-Ups</h3>
                <p>Pop-up markets, art shows, and cultural nights. We love teaming up with local organizers to slow things down and keep it soulful.</p>
                <a href="#booking" class="session-link">Partner with us →</a>
            </article>
        </div>
    </div>
</section>

<!-- Screw Tapes Library Section -->
<section id="music" class="screw-tapes-library">
    <div class="container">
        <h2 class="section-title">
            <span class="graffiti-sub">THE</span>
            <span class="bubble-sub">SOUND LIBRARY</span>
        </h2>
        <p class="library-subtitle">Our digital tape wall is always live. Scroll through volumes, press play, and imagine the room we’ll set up for you.</p>
        
        <!-- Featured Tape -->
        <div class="featured-tape-container">
            <div class="cassette-tape featured" data-volume="1" data-youtube="oYqlfb2sghc">
                <div class="tape-body">
                    <div class="tape-holes">
                        <div class="hole"></div>
                        <div class="hole"></div>
                    </div>
                    <div class="tape-label">
                        <div class="volume-number">VOL. 1</div>
                        <div class="tape-title">FEATURED</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tape Grid -->
        <div class="tape-grid">
            <div class="cassette-tape" data-volume="1" data-youtube="oYqlfb2sghc">
                <div class="tape-body">
                    <div class="tape-holes">
                        <div class="hole"></div>
                        <div class="hole"></div>
                    </div>
                    <div class="tape-label">
                        <div class="volume-number">VOL. 1</div>
                        <div class="tape-title">SCREW TAPE</div>
                    </div>
                </div>
            </div>
            
            <div class="cassette-tape" data-volume="2" data-youtube="jBQ0gArMvzc">
                <div class="tape-body">
                    <div class="tape-holes">
                        <div class="hole"></div>
                        <div class="hole"></div>
                    </div>
                    <div class="tape-label">
                        <div class="volume-number">VOL. 2</div>
                        <div class="tape-title">SCREW TAPE</div>
                    </div>
                </div>
            </div>
            
            <div class="cassette-tape" data-volume="3" data-audio="Another_Night_clara_la_san.mp3">
                <div class="tape-body">
                    <div class="tape-holes">
                        <div class="hole"></div>
                        <div class="hole"></div>
                    </div>
                    <div class="tape-label">
                        <div class="volume-number">VOL. 3</div>
                        <div class="tape-title">ANOTHER NIGHT</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Player Section -->
        <div class="tape-player" id="tape-player">
            <div class="player-header">
                <h3>Now Playing</h3>
                <div class="current-tape-info">
                    <span class="current-volume">Select a tape</span>
                </div>
            </div>
            
            <div class="player-content">
                <div class="youtube-container">
                    <div class="placeholder-player">
                        <div class="play-icon">▶</div>
                        <p>Click a cassette tape above to load</p>
                    </div>
                </div>
                
                <div class="tracklist" id="tracklist">
                    <h4>Tracklist</h4>
                    <div class="tracklist-content">
                        <p>Select a tape to view tracklist</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Section -->
<section id="about" class="about">
    <div class="container">
        <h2 class="section-title">
            <span class="graffiti-sub">THE</span>
            <span class="bubble-sub">STORY</span>
        </h2>
        <div class="about-content">
            <p>
                Welcome to <strong>SouthWest Secret</strong>, the portable living room for Houston’s chopped & screwed culture. 
                We keep the warmth of home in every set: mindful pacing, storytelling transitions, and shout-outs that make your people feel seen.
            </p>
            <p>
                Every mix is hand-built with local flair — a hypnotic blend perfect for cruising, unwinding, or celebrating the milestones that deserve to be remembered.
            </p>
            <div class="features">
                <div class="feature">
                    <div class="feature-icon">🎵</div>
                    <h3>Chopped & Screwed</h3>
                    <p>Classic Houston technique</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🎧</div>
                    <h3>Fresh Mixes</h3>
                    <p>New content regularly</p>
                </div>
                <div class="feature">
                    <div class="feature-icon">🔥</div>
                    <h3>Original Style</h3>
                    <p>Unique sound selection</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="community-notes">
    <div class="container">
        <h2 class="section-heading">Neighbors who’ve pulled up</h2>
        <div class="testimonials-grid">
            <article class="testimonial-card">
                <p>“SouthWest Secret turned our backyard into a late-night lounge. Guests kept asking for the playlist — there isn’t one. It’s a whole experience.”</p>
                <span>— Bri, Third Ward house session</span>
            </article>
            <article class="testimonial-card">
                <p>“The pacing, the blends, the storytelling… it felt like sitting on a Houston porch with family. We’ll be booking again.”</p>
                <span>— Malik, community gallery pop-up</span>
            </article>
            <article class="testimonial-card">
                <p>“Perfect balance of nostalgia and fresh cuts. The slowed-down transitions gave our dinner party the exact energy we wanted.”</p>
                <span>— Janelle, private chef experience</span>
            </article>
        </div>
    </div>
</section>

<!-- Booking Section -->
<section id="booking" class="booking-strip">
    <div class="container">
        <div class="booking-content">
            <h2>Ready to invite the city inside?</h2>
            <p>Tell us about your space, your people, and the story you want to slow down. We’ll craft a set, bring the gear, and make it feel like home.</p>
            <div class="booking-actions">
                <a href="<?php echo home_url('/contact'); ?>" class="btn-primary">Request a date</a>
                <a href="https://www.youtube.com/@SouthWestSecret" target="_blank" class="btn-outline">Preview our vibe</a>
            </div>
            <div class="booking-meta">
                <span>Bookings open for Houston & surrounding cities</span>
                <span>Available weeknights + weekends</span>
            </div>
        </div>
    </div>
</section>

<!-- Contact/Subscribe Section -->
<section id="contact" class="contact">
    <div class="container">
        <h2 class="section-title">
            <span class="graffiti-sub">STAY</span>
            <span class="bubble-sub">CONNECTED</span>
        </h2>
        <div class="contact-content">
            <p>Join the neighborhood list for mix drops, pop-up invites, and late-night set recaps.</p>
            <a href="https://www.youtube.com/@SouthWestSecret" target="_blank" class="btn-subscribe">
                Subscribe & Listen
            </a>
        </div>
    </div>
</section>

<?php get_footer(); ?>

