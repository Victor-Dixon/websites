# Ollama Integration - Moved to agent_tools

The Ollama integration has been moved to the `agent_tools` repository as it's part of
the Dream.OS agent tools infrastructure, not website content.

## Location

The Ollama setup and tool agent are now in:
- `~/agent_tools/ollama_client.py`
- `~/agent_tools/ollama_tool_agent.py`
- `~/agent_tools/setup_ollama.sh`
- `~/agent_tools/docs/OLLAMA_SETUP.md`

## Why Moved?

This integration enables agents to use tools, which is core agent_tools functionality,
not website-specific. It belongs in the Dream.OS ecosystem.

## Using from Website Repo

If you need to use Ollama from the website repo, you can:
1. Import from agent_tools (if it's in your Python path)
2. Or use the standalone client directly

