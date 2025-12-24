# Local LLM Setup for Voice Pattern Processing

## Overview

The voice pattern processor now uses **local Ollama LLM** instead of Mistral API by default. This provides:
- ✅ **No API costs** - Free processing
- ✅ **Privacy** - Content stays local
- ✅ **No rate limits** - Process as much as needed
- ✅ **Faster** - No network latency

## How It Works

The processor tries methods in this order:

1. **OllamaClient API** (if Ollama is running)
2. **Subprocess Ollama** (fallback, works even if API unavailable)
3. **Mistral API** (last resort, requires API key)

## Setup

### 1. Install Ollama

Download from: https://ollama.ai

```bash
# Windows (PowerShell)
winget install Ollama.Ollama

# Or download installer from website
```

### 2. Pull Model

```bash
# Default model (Mistral)
ollama pull mistral:latest

# Or use other models
ollama pull llama3.2
ollama pull codellama
```

### 3. Verify Installation

```bash
# Check if Ollama is running
ollama list

# Test a simple generation
ollama run mistral:latest "Hello, world!"
```

## Usage

### Standard (Uses Local Ollama)

```bash
python ops/deployment/publish_with_autoblogger.py \
  --site digitaldreamscape.site \
  --title "Your Post" \
  --file content.md
```

**What happens:**
1. Checks if Ollama is running
2. Uses local model to apply voice patterns
3. Publishes to WordPress

### Force Mistral API

```bash
python ops/deployment/voice_pattern_processor.py \
  --file content.md \
  --title "Test" \
  --use-api
```

### Specify Model

```bash
python ops/deployment/voice_pattern_processor.py \
  --file content.md \
  --title "Test" \
  --model llama3.2
```

## Available Models

Common models you can use:

- `mistral:latest` - Default, good balance
- `llama3.2` - Meta's latest, very capable
- `codellama` - Better for code-related content
- `mixtral` - Mixture of experts, high quality

## Troubleshooting

### Ollama Not Found

**Error:** `ollama: command not found`

**Solution:**
1. Install Ollama from https://ollama.ai
2. Restart terminal/PowerShell
3. Verify: `ollama --version`

### Model Not Found

**Error:** `model "mistral:latest" not found`

**Solution:**
```bash
ollama pull mistral:latest
```

### Ollama Not Running

**Error:** Connection refused or timeout

**Solution:**
1. Start Ollama service:
   ```bash
   # Windows - should auto-start, but check:
   Get-Service Ollama
   ```
2. Or start manually:
   ```bash
   ollama serve
   ```

### Slow Processing

**Solutions:**
1. Use smaller model: `llama3.2:1b` instead of `llama3.2`
2. Reduce content length (split long posts)
3. Use GPU acceleration (if available)

### Out of Memory

**Error:** CUDA out of memory or similar

**Solutions:**
1. Use smaller model
2. Process shorter content chunks
3. Close other applications
4. Use CPU-only mode (slower but works)

## Performance Tips

1. **Keep Ollama Running** - Don't close the service
2. **Use Appropriate Model** - Smaller models are faster
3. **Batch Processing** - Process multiple posts at once
4. **GPU Acceleration** - Use if available (automatic)

## Fallback Behavior

If local Ollama fails:
- ⚠️ Warning displayed
- Falls back to Mistral API (if key available)
- Or returns original content (if no LLM available)

## Configuration

### Change Default Model

Edit `voice_pattern_processor.py`:
```python
processor = VoicePatternProcessor()
result = processor.apply_voice_patterns(content, title, model="llama3.2")
```

### Disable Local LLM

```python
processor = VoicePatternProcessor(use_local_llm=False)
```

## Benefits Over API

| Feature | Local Ollama | Mistral API |
|---------|-------------|-------------|
| Cost | Free | Pay per token |
| Privacy | 100% local | Sent to API |
| Speed | Fast (local) | Network latency |
| Rate Limits | None | API limits |
| Offline | Works | Requires internet |

## Example Output

**Before processing:**
> "I have been asking myself a question that feels simple, but hits deep: Where are the people who move like me?"

**After processing (local Ollama):**
> "lately ive been asking myself a question that feels simple, but hits deep: where are the people who move like me?"

Same quality, but processed locally with no API costs!


