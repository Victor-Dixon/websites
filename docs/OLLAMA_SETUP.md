# Ollama Setup for Dream.OS Tools

This guide explains how to set up Ollama for local LLM usage with Dream.OS project tools.

## Quick Setup

```bash
# Run the setup script
./setup_ollama.sh
```

This will:
1. Install Ollama (if not already installed)
2. Start the Ollama service
3. Download the llama3.2 model (good balance of quality and size)

## Manual Setup

### 1. Install Ollama

```bash
curl -fsSL https://ollama.com/install.sh | sh
```

### 2. Start Ollama Service

```bash
ollama serve
```

Keep this running in a terminal, or run it as a background service.

### 3. Download a Model

Recommended models:
- **llama3.2** (2B params) - Fast, good for simple tasks
- **llama3.1** (8B params) - Better quality, more resource intensive
- **mistral** (7B params) - Good balance
- **qwen2.5** (7B params) - Excellent for coding tasks

```bash
ollama pull llama3.2
```

### 4. Verify Installation

```bash
ollama list
```

## Using Ollama with Dream.OS Tools

### Environment Variables

```bash
export OLLAMA_BASE_URL=http://localhost:11434
export OLLAMA_MODEL=llama3.2
```

### Python Client

```python
from src.ollama_client import load_ollama_config, generate_markdown
from src.autoblogger.prompt_builder import Prompt

cfg = load_ollama_config()
prompt = Prompt(
    system="You are a helpful assistant.",
    user="Write a blog post about automation."
)

content = generate_markdown(prompt, cfg=cfg)
print(content)
```

### Tool Agent

The `ollama_tool_agent.py` enables Ollama to use project tools:

```python
from src.ollama_tool_agent import OllamaToolAgent

agent = OllamaToolAgent()
response = agent.chat_with_tools("List all blog posts on ariajet.site")
print(response)
```

## Available Tools

The Ollama Tool Agent can use:

1. **blog_manager** - Manage WordPress blog posts
   - List, create, edit, delete posts
   - Works across all configured sites

2. **deploy_site** - Deploy changes to WordPress sites
   - Uses unified deployer
   - Supports dry-run mode

## Integration with Autoblogger

To use Ollama instead of OpenAI for autoblogger:

```bash
export AUTOBLOGGER_OPENAI_BASE_URL=http://localhost:11434/v1
export AUTOBLOGGER_OPENAI_API_KEY=ollama  # Ollama doesn't require a real key
export AUTOBLOGGER_OPENAI_MODEL=llama3.2
```

Note: Ollama uses a different API format, so you may need to modify `llm_client.py` 
to use `ollama_client.py` instead.

## Troubleshooting

### Ollama not starting

```bash
# Check if port 11434 is in use
lsof -i :11434

# Kill existing Ollama process
pkill ollama

# Restart
ollama serve
```

### Model not found

```bash
# List available models
ollama list

# Pull a model
ollama pull llama3.2
```

### Connection refused

Make sure Ollama service is running:
```bash
ollama serve
```

## Next Steps

1. Test the connection: `python src/ollama_client.py`
2. Try the tool agent: `python src/ollama_tool_agent.py "list blog posts"`
3. Integrate with autoblogger workflow
4. Add more tools to the tool agent

