<?php
/**
 * Business Plan Display Template
 * 
 * @package Crosby_Business_Plan
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Get section and download parameters (extracted from $atts by plugin)
$section = isset($section) ? $section : 'all';
$show_download = isset($download) && $download === 'true';
?>

<div class="crosby-business-plan">
    
    <!-- Header -->
    <div class="crosby-business-plan-header">
        <h1>Business Plan: Crosby Ultimate Events</h1>
        <p class="subtitle">Private Chef & Event Planning Services</p>
        <div class="meta">
            <strong>Website:</strong> crosbyultimateevents.com<br>
            <strong>Date:</strong> December 2024<br>
            <strong>Version:</strong> 1.0
        </div>
    </div>

    <?php if ($show_download) : ?>
    <div class="crosby-business-plan-download">
        <a href="<?php echo esc_url(wp_get_attachment_url(get_option('crosby_bp_pdf_id'))); ?>" target="_blank" rel="noopener">
            📥 Download Business Plan (PDF)
        </a>
    </div>
    <?php endif; ?>

    <!-- Table of Contents -->
    <div class="crosby-business-plan-toc">
        <h2>Table of Contents</h2>
        <ul>
            <li><a href="#executive-summary">Executive Summary</a></li>
            <li><a href="#company-description">Company Description</a></li>
            <li><a href="#products-services">Products and Services</a></li>
            <li><a href="#market-analysis">Market Analysis</a></li>
            <li><a href="#marketing-sales">Marketing and Sales Strategy</a></li>
            <li><a href="#operations">Operations Plan</a></li>
            <li><a href="#financial">Financial Plan</a></li>
            <li><a href="#management">Management and Organization</a></li>
            <li><a href="#risks">Risk Analysis and Mitigation</a></li>
            <li><a href="#growth">Growth Strategy</a></li>
            <li><a href="#timeline">Implementation Timeline</a></li>
            <li><a href="#metrics">Success Metrics and KPIs</a></li>
        </ul>
    </div>

    <?php if ($section === 'all' || $section === 'executive') : ?>
    <!-- Executive Summary -->
    <section id="executive-summary" class="crosby-business-plan-section executive-summary">
        <h2>Executive Summary</h2>
        
        <p>Crosby Ultimate Events is a premium private chef and event planning service business that provides personalized culinary experiences and comprehensive event coordination for clients seeking exceptional, memorable occasions. The business combines culinary artistry with meticulous event planning to deliver seamless, high-end experiences for private dinners, corporate events, weddings, celebrations, and special occasions.</p>

        <div class="mission-vision-container">
            <div class="mission-box">
                <h3>Mission Statement</h3>
                <p>To create extraordinary culinary experiences and flawlessly executed events that exceed client expectations through personalized service, exceptional cuisine, and attention to detail.</p>
            </div>
            
            <div class="vision-box">
                <h3>Vision Statement</h3>
                <p>To become the premier destination for private chef services and event planning in our market, recognized for innovation, quality, and unparalleled customer satisfaction.</p>
            </div>
        </div>

        <div class="key-objectives">
            <h3>Key Objectives (Year 1)</h3>
            <ul>
                <li>Establish strong brand presence through professional website and marketing</li>
                <li>Achieve 50+ successful events/bookings in first year</li>
                <li>Build a portfolio of satisfied clients and testimonials</li>
                <li>Generate $150,000+ in revenue</li>
                <li>Maintain 90%+ client satisfaction rating</li>
            </ul>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($section === 'all' || $section === 'company') : ?>
    <!-- Company Description -->
    <section id="company-description" class="crosby-business-plan-section">
        <h2>1. Company Description</h2>
        
        <h3>Business Overview</h3>
        <p>Crosby Ultimate Events operates as a service-based business specializing in two core areas:</p>
        <ol>
            <li><strong>Private Chef Services</strong>: In-home and on-location culinary experiences</li>
            <li><strong>Event Planning Services</strong>: Full-service event coordination and management</li>
        </ol>

        <h3>Legal Structure</h3>
        <ul>
            <li><strong>Recommended</strong>: LLC (Limited Liability Company) or Sole Proprietorship</li>
            <li>Provides liability protection and operational flexibility</li>
            <li>Allows for future expansion and partnership opportunities</li>
        </ul>

        <h3>Business Location</h3>
        <ul>
            <li>Primary service area: [Specify geographic region]</li>
            <li>Mobile service model (travels to client locations)</li>
            <li>Potential for commercial kitchen space as business grows</li>
        </ul>

        <h3>Industry Overview</h3>
        <p>The private chef and event planning industry has shown strong growth, particularly in the premium/luxury segment. Key trends include:</p>
        <ul>
            <li>Increased demand for personalized, unique experiences</li>
            <li>Growing preference for in-home dining experiences</li>
            <li>Corporate event market expansion</li>
            <li>Wedding and celebration market stability</li>
            <li>Focus on locally sourced, sustainable ingredients</li>
        </ul>
    </section>
    <?php endif; ?>

    <?php if ($section === 'all' || $section === 'products') : ?>
    <!-- Products and Services -->
    <section id="products-services" class="crosby-business-plan-section">
        <h2>2. Products and Services</h2>
        
        <h3>Core Services</h3>
        
        <h4>A. Private Chef Services</h4>
        <p><strong>Service Offerings:</strong></p>
        <ul>
            <li><strong>In-Home Dining Experiences</strong>
                <ul>
                    <li>Multi-course fine dining meals</li>
                    <li>Custom menu development</li>
                    <li>Wine pairing recommendations</li>
                    <li>Dietary restriction accommodations (vegan, gluten-free, keto, etc.)</li>
                </ul>
            </li>
            <li><strong>Cooking Classes</strong>
                <ul>
                    <li>Private group instruction</li>
                    <li>Team building culinary experiences</li>
                    <li>Date night cooking classes</li>
                </ul>
            </li>
            <li><strong>Meal Prep Services</strong>
                <ul>
                    <li>Weekly meal preparation</li>
                    <li>Special diet meal plans</li>
                    <li>Family meal services</li>
                </ul>
            </li>
        </ul>

        <p><strong>Pricing Structure:</strong></p>
        <ul>
            <li>In-home dining: $150-$300 per person (depending on menu complexity)</li>
            <li>Cooking classes: $100-$200 per person</li>
            <li>Meal prep: $200-$500 per week (varies by frequency and portions)</li>
        </ul>

        <h4>B. Event Planning Services</h4>
        <p><strong>Service Offerings:</strong></p>
        <ul>
            <li><strong>Full Event Coordination</strong>
                <ul>
                    <li>Venue selection and booking</li>
                    <li>Vendor management</li>
                    <li>Timeline development and execution</li>
                    <li>Day-of coordination</li>
                </ul>
            </li>
            <li><strong>Event Types:</strong>
                <ul>
                    <li>Corporate events (meetings, retreats, celebrations)</li>
                    <li>Private parties (birthdays, anniversaries, holidays)</li>
                    <li>Intimate weddings and elopements</li>
                    <li>Social gatherings and celebrations</li>
                </ul>
            </li>
            <li><strong>À La Carte Services</strong>
                <ul>
                    <li>Menu planning and consultation</li>
                    <li>Vendor referrals</li>
                    <li>Event design consultation</li>
                    <li>Partial planning packages</li>
                </ul>
            </li>
        </ul>

        <p><strong>Pricing Structure:</strong></p>
        <ul>
            <li>Full event planning: 15-20% of total event budget</li>
            <li>Day-of coordination: $1,500-$3,000</li>
            <li>Consultation services: $150-$300 per hour</li>
            <li>Partial planning: Custom quotes based on scope</li>
        </ul>

        <h3>Service Packages</h3>
        <div class="service-packages">
            <div class="service-package">
                <h4>Package 1: Intimate Dining Experience</h4>
                <ul>
                    <li>3-course meal for 2-6 guests</li>
                    <li>Custom menu consultation</li>
                    <li>Table setting and service</li>
                    <li>Cleanup included</li>
                </ul>
                <div class="price">$800-$1,500</div>
            </div>

            <div class="service-package">
                <h4>Package 2: Celebration Package</h4>
                <ul>
                    <li>Event planning + private chef services</li>
                    <li>Full coordination for parties up to 30 guests</li>
                    <li>Custom menu and event design</li>
                    <li>Vendor coordination</li>
                </ul>
                <div class="price">$3,000-$8,000</div>
            </div>

            <div class="service-package">
                <h4>Package 3: Corporate Experience</h4>
                <ul>
                    <li>Team building cooking class or catered event</li>
                    <li>Customizable for groups of 10-50</li>
                    <li>Professional presentation</li>
                </ul>
                <div class="price">$2,000-$10,000</div>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($section === 'all' || $section === 'market') : ?>
    <!-- Market Analysis -->
    <section id="market-analysis" class="crosby-business-plan-section">
        <h2>3. Market Analysis</h2>
        
        <h3>Target Market</h3>
        
        <h4>Primary Market Segments</h4>
        
        <p><strong>1. Affluent Professionals & Families</strong></p>
        <ul>
            <li>Household income: $150,000+</li>
            <li>Age: 35-65</li>
            <li>Lifestyle: Busy professionals seeking convenience and quality</li>
            <li>Values: Time-saving, premium experiences, social status</li>
        </ul>

        <p><strong>2. Corporate Clients</strong></p>
        <ul>
            <li>Companies hosting client entertainment</li>
            <li>Team building events</li>
            <li>Corporate retreats and celebrations</li>
            <li>Budget: $2,000-$20,000 per event</li>
        </ul>

        <p><strong>3. Special Occasion Market</strong></p>
        <ul>
            <li>Engagements and anniversaries</li>
            <li>Milestone birthdays</li>
            <li>Holiday celebrations</li>
            <li>Intimate weddings (20-50 guests)</li>
        </ul>

        <p><strong>4. Health-Conscious Consumers</strong></p>
        <ul>
            <li>Dietary restrictions (vegan, gluten-free, etc.)</li>
            <li>Wellness-focused individuals</li>
            <li>Meal prep clients seeking quality nutrition</li>
        </ul>

        <h3>Competitive Advantages</h3>
        <ol>
            <li><strong>Dual Service Offering</strong>: Combined chef + event planning (unique positioning)</li>
            <li><strong>Personalized Approach</strong>: Custom menus and tailored experiences</li>
            <li><strong>Flexibility</strong>: Accommodates various event sizes and budgets</li>
            <li><strong>Quality Focus</strong>: Premium ingredients and attention to detail</li>
            <li><strong>Professional Website</strong>: Modern, professional online presence</li>
        </ol>
    </section>
    <?php endif; ?>

    <?php if ($section === 'all' || $section === 'marketing') : ?>
    <!-- Marketing and Sales Strategy -->
    <section id="marketing-sales" class="crosby-business-plan-section">
        <h2>4. Marketing and Sales Strategy</h2>
        
        <h3>Brand Positioning</h3>
        <p><strong>Premium, Personalized, Professional</strong></p>
        <p>Position Crosby Ultimate Events as the go-to choice for clients who value:</p>
        <ul>
            <li>Exceptional culinary experiences</li>
            <li>Personalized service</li>
            <li>Professional execution</li>
            <li>Attention to detail</li>
        </ul>

        <h3>Marketing Channels</h3>
        
        <h4>Digital Marketing</h4>
        <ol>
            <li><strong>Website (crosbyultimateevents.com)</strong>
                <ul>
                    <li>Professional design and user experience</li>
                    <li>SEO optimization for local search</li>
                    <li>Service showcase and portfolio</li>
                    <li>Online booking/contact forms</li>
                    <li>Blog content (recipes, event tips, behind-the-scenes)</li>
                </ul>
            </li>
            <li><strong>Social Media</strong>
                <ul>
                    <li>Instagram: Visual content (food photography, event setups)</li>
                    <li>Facebook: Community building, event promotion</li>
                    <li>Pinterest: Recipe and event inspiration</li>
                    <li>LinkedIn: B2B corporate client outreach</li>
                </ul>
            </li>
            <li><strong>Content Marketing</strong>
                <ul>
                    <li>Blog posts: Event planning tips, recipe features</li>
                    <li>Video content: Cooking demonstrations, event highlights</li>
                    <li>Email newsletter: Monthly updates, special offers</li>
                </ul>
            </li>
            <li><strong>Online Advertising</strong>
                <ul>
                    <li>Google Ads (local search)</li>
                    <li>Facebook/Instagram ads (targeted demographics)</li>
                    <li>Yelp and local directory listings</li>
                </ul>
            </li>
        </ol>

        <h4>Traditional Marketing</h4>
        <ol>
            <li><strong>Networking</strong>
                <ul>
                    <li>Local business associations</li>
                    <li>Wedding and event industry events</li>
                    <li>Culinary community involvement</li>
                    <li>Chamber of commerce membership</li>
                </ul>
            </li>
            <li><strong>Partnerships</strong>
                <ul>
                    <li>Venue partnerships (mutual referrals)</li>
                    <li>Vendor collaborations (photographers, florists, etc.)</li>
                    <li>Corporate HR departments</li>
                    <li>Real estate agents (for new homeowners)</li>
                </ul>
            </li>
            <li><strong>Referral Program</strong>
                <ul>
                    <li>Incentivize client referrals (10-15% discount)</li>
                    <li>Partner referral commissions</li>
                </ul>
            </li>
        </ol>
    </section>
    <?php endif; ?>

    <?php if ($section === 'all' || $section === 'operations') : ?>
    <!-- Operations Plan -->
    <section id="operations" class="crosby-business-plan-section">
        <h2>5. Operations Plan</h2>
        
        <h3>Service Delivery Process</h3>
        
        <h4>Private Chef Service Workflow</h4>
        <ol>
            <li><strong>Initial Consultation</strong> (1-2 weeks before event)
                <ul>
                    <li>Menu planning discussion</li>
                    <li>Dietary restrictions and preferences</li>
                    <li>Guest count confirmation</li>
                    <li>Kitchen assessment (if in-home)</li>
                </ul>
            </li>
            <li><strong>Preparation Phase</strong>
                <ul>
                    <li>Menu finalization</li>
                    <li>Ingredient sourcing</li>
                    <li>Equipment preparation</li>
                    <li>Timeline development</li>
                </ul>
            </li>
            <li><strong>Service Day</strong>
                <ul>
                    <li>Arrival and setup</li>
                    <li>Food preparation</li>
                    <li>Service execution</li>
                    <li>Cleanup</li>
                </ul>
            </li>
            <li><strong>Follow-up</strong>
                <ul>
                    <li>Client feedback collection</li>
                    <li>Thank you communication</li>
                    <li>Review request</li>
                </ul>
            </li>
        </ol>

        <h4>Event Planning Workflow</h4>
        <ol>
            <li><strong>Discovery Phase</strong> (4-8 weeks before event)</li>
            <li><strong>Planning Phase</strong></li>
            <li><strong>Pre-Event</strong></li>
            <li><strong>Event Day</strong></li>
            <li><strong>Post-Event</strong></li>
        </ol>

        <h3>Quality Control</h3>
        <ul>
            <li>Client satisfaction surveys</li>
            <li>Post-event debriefs</li>
            <li>Continuous menu and service improvement</li>
            <li>Professional development and training</li>
            <li>Health and safety certifications (food handling, etc.)</li>
        </ul>
    </section>
    <?php endif; ?>

    <?php if ($section === 'all' || $section === 'financial') : ?>
    <!-- Financial Plan -->
    <section id="financial" class="crosby-business-plan-section">
        <h2>6. Financial Plan</h2>
        
        <h3>Startup Costs</h3>
        <div class="financial-highlights">
            <div class="financial-card">
                <div class="label">Total Startup</div>
                <div class="value">$18K-$36K</div>
            </div>
            <div class="financial-card">
                <div class="label">Monthly Break-Even</div>
                <div class="value">$2.5K-$3.5K</div>
            </div>
            <div class="financial-card">
                <div class="label">Year 1 Revenue Target</div>
                <div class="value">$150K+</div>
            </div>
        </div>

        <h3>Revenue Projections (Year 1 - Conservative)</h3>
        <table>
            <thead>
                <tr>
                    <th>Quarter</th>
                    <th>Revenue</th>
                    <th>Events</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Q1</td>
                    <td>$25,000</td>
                    <td>10 events</td>
                </tr>
                <tr>
                    <td>Q2</td>
                    <td>$35,000</td>
                    <td>14 events</td>
                </tr>
                <tr>
                    <td>Q3</td>
                    <td>$40,000</td>
                    <td>16 events</td>
                </tr>
                <tr>
                    <td>Q4</td>
                    <td>$50,000</td>
                    <td>20 events</td>
                </tr>
                <tr>
                    <td><strong>Year 1 Total</strong></td>
                    <td><strong>$150,000</strong></td>
                    <td><strong>60 events</strong></td>
                </tr>
            </tbody>
        </table>

        <h3>Profit Margins</h3>
        <ul>
            <li><strong>Gross Margin Target</strong>: 50-60%</li>
            <li><strong>Net Profit Target</strong>: 20-30% (after all expenses)</li>
        </ul>
    </section>
    <?php endif; ?>

    <?php if ($section === 'all' || $section === 'management') : ?>
    <!-- Management and Organization -->
    <section id="management" class="crosby-business-plan-section">
        <h2>7. Management and Organization</h2>
        
        <h3>Organizational Structure</h3>
        <p><strong>Year 1</strong></p>
        <ul>
            <li><strong>Owner/Founder</strong>: Primary operator
                <ul>
                    <li>Chef services</li>
                    <li>Event planning</li>
                    <li>Business development</li>
                    <li>Marketing</li>
                    <li>Client relations</li>
                </ul>
            </li>
        </ul>

        <h3>Professional Development</h3>
        <ul>
            <li>Continued culinary education</li>
            <li>Event planning certifications (CSEP, CMP, etc.)</li>
            <li>Business management training</li>
            <li>Food safety certifications (ServSafe, etc.)</li>
            <li>Industry conference attendance</li>
        </ul>
    </section>
    <?php endif; ?>

    <?php if ($section === 'all' || $section === 'risks') : ?>
    <!-- Risk Analysis -->
    <section id="risks" class="crosby-business-plan-section">
        <h2>8. Risk Analysis and Mitigation</h2>
        
        <h3>Key Risks</h3>
        
        <div class="callout warning">
            <h4>1. Market/Competition Risks</h4>
            <p><strong>Risk:</strong> Established competitors with strong market presence</p>
            <p><strong>Mitigation:</strong> Differentiate through unique dual-service offering, focus on personalized service and quality, build strong referral network, competitive pricing strategy</p>
        </div>

        <div class="callout info">
            <h4>2. Operational Risks</h4>
            <p><strong>Risk:</strong> Equipment failure, transportation issues</p>
            <p><strong>Mitigation:</strong> Maintain backup equipment, reliable transportation with backup plan, insurance coverage, vendor relationships for emergency support</p>
        </div>

        <div class="callout">
            <h4>3. Financial Risks</h4>
            <p><strong>Risk:</strong> Cash flow fluctuations, seasonal demand</p>
            <p><strong>Mitigation:</strong> Maintain operating reserve, diversify service offerings, require deposits (50% standard), clear payment terms and contracts</p>
        </div>

        <h3>Insurance Requirements</h3>
        <ul>
            <li>General liability insurance: $1M-$2M</li>
            <li>Professional liability insurance</li>
            <li>Commercial auto insurance (if applicable)</li>
            <li>Workers' compensation (if employees)</li>
            <li>Equipment insurance</li>
        </ul>
    </section>
    <?php endif; ?>

    <?php if ($section === 'all' || $section === 'growth') : ?>
    <!-- Growth Strategy -->
    <section id="growth" class="crosby-business-plan-section">
        <h2>9. Growth Strategy</h2>
        
        <h3>Short-Term Goals (Year 1)</h3>
        <ul>
            <li>Establish brand and online presence</li>
            <li>Build client base (50+ events)</li>
            <li>Develop portfolio and testimonials</li>
            <li>Achieve profitability</li>
            <li>Build vendor and partner network</li>
        </ul>

        <h3>Medium-Term Goals (Years 2-3)</h3>
        <ul>
            <li>Expand service offerings</li>
            <li>Hire additional staff (chef, coordinator)</li>
            <li>Increase event volume (100+ events/year)</li>
            <li>Develop signature service packages</li>
            <li>Expand geographic service area</li>
            <li>Revenue target: $300,000-$500,000</li>
        </ul>

        <h3>Long-Term Goals (Years 4-5)</h3>
        <ul>
            <li>Consider commercial kitchen space</li>
            <li>Build team of 3-5 professionals</li>
            <li>Develop training programs</li>
            <li>Potential franchise/licensing opportunities</li>
            <li>Revenue target: $750,000-$1,000,000+</li>
        </ul>
    </section>
    <?php endif; ?>

    <?php if ($section === 'all' || $section === 'timeline') : ?>
    <!-- Implementation Timeline -->
    <section id="timeline" class="crosby-business-plan-section">
        <h2>10. Implementation Timeline</h2>
        
        <div class="timeline">
            <div class="timeline-item">
                <h4>Pre-Launch (Months -2 to 0)</h4>
                <ul>
                    <li>Complete business registration and licensing</li>
                    <li>Secure insurance coverage</li>
                    <li>Finalize website and online presence</li>
                    <li>Develop marketing materials</li>
                    <li>Purchase equipment and supplies</li>
                    <li>Build initial vendor network</li>
                    <li>Create service packages and pricing</li>
                    <li>Develop contracts and legal documents</li>
                </ul>
            </div>

            <div class="timeline-item">
                <h4>Launch Phase (Months 1-3)</h4>
                <ul>
                    <li>Soft launch with friends/family/referrals</li>
                    <li>Launch marketing campaigns</li>
                    <li>Network and build partnerships</li>
                    <li>Secure first 10-15 clients</li>
                    <li>Refine processes based on initial feedback</li>
                    <li>Build portfolio (photos, testimonials)</li>
                </ul>
            </div>

            <div class="timeline-item">
                <h4>Growth Phase (Months 4-12)</h4>
                <ul>
                    <li>Scale marketing efforts</li>
                    <li>Increase client acquisition</li>
                    <li>Develop repeat client base</li>
                    <li>Expand service offerings</li>
                    <li>Hire support staff (as needed)</li>
                    <li>Optimize operations and profitability</li>
                    <li>Build strong referral network</li>
                </ul>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($section === 'all' || $section === 'metrics') : ?>
    <!-- Success Metrics -->
    <section id="metrics" class="crosby-business-plan-section">
        <h2>11. Success Metrics and KPIs</h2>
        
        <h3>Financial Metrics</h3>
        <ul>
            <li>Monthly revenue</li>
            <li>Average event value</li>
            <li>Profit margins (gross and net)</li>
            <li>Cash flow</li>
            <li>Client acquisition cost</li>
            <li>Lifetime client value</li>
        </ul>

        <h3>Operational Metrics</h3>
        <ul>
            <li>Number of events per month</li>
            <li>Client satisfaction scores</li>
            <li>Repeat client rate</li>
            <li>Referral rate</li>
            <li>Booking lead time</li>
            <li>Event completion rate</li>
        </ul>

        <h3>Marketing Metrics</h3>
        <ul>
            <li>Website traffic and conversions</li>
            <li>Social media engagement</li>
            <li>Lead generation sources</li>
            <li>Cost per acquisition</li>
            <li>Brand awareness metrics</li>
        </ul>
    </section>
    <?php endif; ?>

    <!-- Conclusion -->
    <section class="crosby-business-plan-section">
        <h2>Conclusion</h2>
        <p>Crosby Ultimate Events is positioned to capitalize on the growing demand for premium private chef and event planning services. With a professional website, clear service offerings, and a focus on quality and personalization, the business is well-equipped to build a strong client base and achieve sustainable growth.</p>
        
        <p>The combination of culinary expertise and event planning services creates a unique value proposition that differentiates Crosby Ultimate Events in the market. By focusing on exceptional service delivery, strategic marketing, and building strong client relationships, the business can achieve its Year 1 objectives and establish a foundation for long-term success.</p>

        <div class="callout success">
            <h4>Next Steps</h4>
            <ol>
                <li>Review and refine this business plan</li>
                <li>Complete pre-launch checklist</li>
                <li>Begin marketing and client acquisition</li>
                <li>Execute first events and build portfolio</li>
                <li>Monitor KPIs and adjust strategy as needed</li>
            </ol>
        </div>
    </section>

    <div class="crosby-business-plan-footer" style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid #e0e0e0; text-align: center; color: #888; font-size: 0.9rem;">
        <p><strong>Document Control:</strong> Version 1.0 | Date: December 2024 | Next Review: Quarterly updates recommended</p>
        <p><em>This business plan is a living document and should be reviewed and updated regularly to reflect changes in the business, market conditions, and strategic direction.</em></p>
    </div>

</div>

