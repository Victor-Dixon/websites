#!/bin/bash
# Restructure TradingRobotPlug Navigation Menu
# Reduces primary menu to 5 items, moves legal pages to footer

SITE_KEY="tradingrobotplug.com"

echo "🔄 Restructuring TradingRobotPlug navigation..."

# Step 1: Get or create Primary Menu
PRIMARY_MENU_ID=$(wp menu list --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --format=json --allow-root | jq -r '.[] | select(.name=="Primary Menu") | .term_id')

if [ -z "$PRIMARY_MENU_ID" ]; then
    echo "Creating Primary Menu..."
    wp menu create "Primary Menu" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root
    PRIMARY_MENU_ID=$(wp menu list --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --format=json --allow-root | jq -r '.[] | select(.name=="Primary Menu") | .term_id')
fi

# Assign to primary location
wp menu location assign "Primary Menu" primary --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root

# Clear existing items
echo "Clearing existing Primary Menu items..."
wp menu item list "$PRIMARY_MENU_ID" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --format=ids --allow-root | xargs -I {} wp menu item delete {} --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root 2>/dev/null || true

# Add primary menu items (5 items)
echo "Adding Primary Menu items..."
wp menu item add-post "$PRIMARY_MENU_ID" $(wp post list --post_type=page --name=home --format=ids --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root) --title="Home" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root
wp menu item add-post "$PRIMARY_MENU_ID" $(wp post list --post_type=page --name=features --format=ids --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root) --title="Features" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root
wp menu item add-post "$PRIMARY_MENU_ID" $(wp post list --post_type=page --name=pricing --format=ids --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root) --title="Pricing" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root
wp menu item add-post "$PRIMARY_MENU_ID" $(wp post list --post_type=page --name=ai-swarm --format=ids --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root) --title="AI Swarm" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root
wp menu item add-post "$PRIMARY_MENU_ID" $(wp post list --post_type=page --name=waitlist --format=ids --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root) --title="Get Started" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root

# Step 2: Get or create Footer Menu
FOOTER_MENU_ID=$(wp menu list --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --format=json --allow-root | jq -r '.[] | select(.name=="Footer Menu") | .term_id')

if [ -z "$FOOTER_MENU_ID" ]; then
    echo "Creating Footer Menu..."
    wp menu create "Footer Menu" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root
    FOOTER_MENU_ID=$(wp menu list --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --format=json --allow-root | jq -r '.[] | select(.name=="Footer Menu") | .term_id')
fi

# Assign to footer location
wp menu location assign "Footer Menu" footer --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root

# Clear existing items
echo "Clearing existing Footer Menu items..."
wp menu item list "$FOOTER_MENU_ID" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --format=ids --allow-root | xargs -I {} wp menu item delete {} --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root 2>/dev/null || true

# Add footer menu items (Legal pages)
echo "Adding Footer Menu items..."
wp menu item add-post "$FOOTER_MENU_ID" $(wp post list --post_type=page --name=blog --format=ids --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root) --title="Blog" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root
wp menu item add-post "$FOOTER_MENU_ID" $(wp post list --post_type=page --name=contact --format=ids --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root) --title="Contact" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root
wp menu item add-post "$FOOTER_MENU_ID" $(wp post list --post_type=page --name=privacy --format=ids --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root) --title="Privacy Policy" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root
wp menu item add-post "$FOOTER_MENU_ID" $(wp post list --post_type=page --name=terms-of-service --format=ids --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root) --title="Terms of Service" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root
wp menu item add-post "$FOOTER_MENU_ID" $(wp post list --post_type=page --name=product-terms --format=ids --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root) --title="Product Terms" --path=/home/u996867598/domains/$SITE_KEY/public_html/wp --allow-root

echo ""
echo "✅ Navigation restructured successfully!"
echo ""
echo "Primary Menu (5 items):"
echo "  - Home"
echo "  - Features"
echo "  - Pricing"
echo "  - AI Swarm"
echo "  - Get Started"
echo ""
echo "Footer Menu (Legal pages):"
echo "  - Blog"
echo "  - Contact"
echo "  - Privacy Policy"
echo "  - Terms of Service"
echo "  - Product Terms"

