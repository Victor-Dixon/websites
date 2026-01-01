#!/bin/bash
# WordPress Testing Environment Setup
# For FreeRideInvestor Plugin Testing

echo "🚀 Setting up WordPress Testing Environment..."

# Start Docker containers
echo "📦 Starting Docker containers..."
docker-compose up -d

# Wait for WordPress to be ready
echo "⏳ Waiting for WordPress to initialize (60 seconds)..."
sleep 60

# Install WordPress
echo "🔧 Installing WordPress..."
docker-compose exec -T wpcli core install \
  --url="http://localhost:8080" \
  --title="FreeRider Test Site" \
  --admin_user="admin" \
  --admin_password="test_admin_123" \
  --admin_email="test@freerider.local" \
  --skip-email

# Activate theme
echo "🎨 Activating FreeRider theme..."
docker-compose exec -T wpcli theme activate freerider

# Install useful plugins for testing
echo "🔌 Installing testing utilities..."
docker-compose exec -T wpcli plugin install query-monitor --activate
docker-compose exec -T wpcli plugin install debug-bar --activate

# Activate all plugins in /plugins directory
echo "🔌 Activating all custom plugins..."
for plugin_dir in plugins/*/; do
    plugin_name=$(basename "$plugin_dir")
    echo "Activating: $plugin_name"
    docker-compose exec -T wpcli plugin activate "$plugin_name" || echo "⚠️ Failed to activate $plugin_name"
done

# Create test data
echo "📝 Creating test data..."
docker-compose exec -T wpcli post create \
  --post_type=post \
  --post_status=publish \
  --post_title="Test Post 1" \
  --post_content="This is test content for plugin testing."

docker-compose exec -T wpcli post create \
  --post_type=post \
  --post_status=publish \
  --post_title="Test Post 2" \
  --post_content="Another test post with sample data."

# Create test pages
docker-compose exec -T wpcli post create \
  --post_type=page \
  --post_status=publish \
  --post_title="Test Page" \
  --post_content="Test page for plugin testing."

echo "✅ WordPress Testing Environment Ready!"
echo ""
echo "🌐 Access Points:"
echo "   WordPress:   http://localhost:8080"
echo "   Admin:       http://localhost:8080/wp-admin"
echo "   Username:    admin"
echo "   Password:    test_admin_123"
echo "   PHPMyAdmin:  http://localhost:8081"
echo ""
echo "🔧 Useful Commands:"
echo "   List plugins:     docker-compose exec wpcli plugin list"
echo "   Activate plugin:  docker-compose exec wpcli plugin activate PLUGIN_NAME"
echo "   Check logs:       docker-compose logs -f wordpress"
echo "   Stop environment: docker-compose down"
echo "   Reset database:   docker-compose down -v && ./test-environment-setup.sh"


