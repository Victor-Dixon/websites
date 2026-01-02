#!/bin/bash
# Setup Ollama for local LLM usage with Dream.OS tools

set -e

echo "=== Setting up Ollama ==="

# Check if Ollama is already installed
if command -v ollama &> /dev/null; then
    echo "✅ Ollama is already installed"
    ollama --version
else
    echo "📥 Installing Ollama..."
    curl -fsSL https://ollama.com/install.sh | sh
fi

# Start Ollama service (if not running)
if ! pgrep -x "ollama" > /dev/null; then
    echo "🚀 Starting Ollama service..."
    ollama serve &
    sleep 3
fi

# Download recommended model (llama3.2 is good balance of quality and size)
echo "📦 Downloading llama3.2 model (this may take a few minutes)..."
ollama pull llama3.2

# Verify installation
echo ""
echo "=== Verification ==="
ollama list

echo ""
echo "✅ Ollama setup complete!"
echo ""
echo "To use Ollama with Dream.OS tools:"
echo "  export OLLAMA_BASE_URL=http://localhost:11434"
echo "  export OLLAMA_MODEL=llama3.2"
echo ""
echo "Or use the Python client: python src/ollama_client.py"

