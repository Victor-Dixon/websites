"""
Ollama Tool Agent - Enables Ollama to use Dream.OS project tools

This agent provides a function-calling interface for Ollama to interact with
the project's tools (blog_manager, deployment tools, etc.)
"""

from __future__ import annotations

import json
import subprocess
import sys
from pathlib import Path
from typing import Any

import requests

# Add tools directory to path
TOOLS_DIR = Path(__file__).parent.parent / "tools"
sys.path.insert(0, str(TOOLS_DIR))


class OllamaToolAgent:
    """Agent that enables Ollama to use project tools via function calling."""
    
    def __init__(self, base_url: str = "http://localhost:11434", model: str = "llama3.2"):
        self.base_url = base_url.rstrip("/")
        self.model = model
        self.tools = self._discover_tools()
    
    def _discover_tools(self) -> dict[str, dict]:
        """Discover available tools in the project."""
        tools = {}
        
        # Blog Manager
        tools["blog_manager"] = {
            "type": "function",
            "function": {
                "name": "blog_manager",
                "description": "Manage WordPress blog posts across all sites",
                "parameters": {
                    "type": "object",
                    "properties": {
                        "action": {
                            "type": "string",
                            "enum": ["list", "create", "edit", "delete", "get"],
                            "description": "Action to perform"
                        },
                        "site": {
                            "type": "string",
                            "description": "Site domain (e.g., ariajet.site)"
                        },
                        "title": {"type": "string", "description": "Post title"},
                        "content": {"type": "string", "description": "Post content (HTML)"},
                        "post_id": {"type": "integer", "description": "Post ID for edit/delete/get"},
                        "status": {
                            "type": "string",
                            "enum": ["draft", "publish"],
                            "description": "Post status"
                        }
                    },
                    "required": ["action", "site"]
                }
            }
        }
        
        # Deployment tools
        tools["deploy_site"] = {
            "type": "function",
            "function": {
                "name": "deploy_site",
                "description": "Deploy changes to a WordPress site",
                "parameters": {
                    "type": "object",
                    "properties": {
                        "site": {
                            "type": "string",
                            "description": "Site domain to deploy"
                        },
                        "dry_run": {
                            "type": "boolean",
                            "description": "Test deployment without making changes"
                        }
                    },
                    "required": ["site"]
                }
            }
        }
        
        return tools
    
    def _execute_tool(self, tool_name: str, arguments: dict) -> str:
        """Execute a tool and return the result."""
        if tool_name == "blog_manager":
            return self._run_blog_manager(arguments)
        elif tool_name == "deploy_site":
            return self._run_deploy(arguments)
        else:
            return f"Unknown tool: {tool_name}"
    
    def _run_blog_manager(self, args: dict) -> str:
        """Run blog_manager.py with given arguments."""
        cmd = ["python3", str(TOOLS_DIR / "blog_manager.py"), args["action"], args["site"]]
        
        if args.get("post_id"):
            cmd.extend(["--id", str(args["post_id"])])
        if args.get("title"):
            cmd.extend(["--title", args["title"]])
        if args.get("content"):
            cmd.extend(["--content", args["content"]])
        if args.get("status"):
            cmd.extend(["--status", args["status"]])
        
        try:
            result = subprocess.run(
                cmd,
                capture_output=True,
                text=True,
                timeout=60,
                cwd=TOOLS_DIR.parent
            )
            return result.stdout or result.stderr or "Command completed"
        except Exception as e:
            return f"Error: {e}"
    
    def _run_deploy(self, args: dict) -> str:
        """Run deployment for a site."""
        site = args["site"]
        dry_run = args.get("dry_run", False)
        
        # Use unified deployer if available
        deployer_path = TOOLS_DIR.parent / "ops" / "deployment" / "unified_deployer.py"
        if deployer_path.exists():
            cmd = ["python3", str(deployer_path), "--site", site]
            if dry_run:
                cmd.append("--dry-run")
        else:
            return f"Deployment tool not found for {site}"
        
        try:
            result = subprocess.run(
                cmd,
                capture_output=True,
                text=True,
                timeout=120,
                cwd=TOOLS_DIR.parent
            )
            return result.stdout or result.stderr or "Deployment completed"
        except Exception as e:
            return f"Error: {e}"
    
    def chat_with_tools(self, user_message: str) -> str:
        """
        Chat with Ollama and allow it to use tools.
        
        This is a simplified version - full implementation would use
        Ollama's function calling API when available.
        """
        # For now, we'll use a prompt-based approach
        system_prompt = f"""You are a helpful assistant with access to Dream.OS project tools.

Available tools:
{json.dumps(list(self.tools.keys()), indent=2)}

When the user asks you to perform an action, analyze what tool is needed and respond with:
1. The tool name
2. The arguments in JSON format

Example:
User: "List all blog posts on ariajet.site"
You: Use tool: blog_manager
Arguments: {{"action": "list", "site": "ariajet.site"}}
"""
        
        messages = [
            {"role": "system", "content": system_prompt},
            {"role": "user", "content": user_message}
        ]
        
        payload = {
            "model": self.model,
            "messages": messages,
            "stream": False
        }
        
        try:
            resp = requests.post(
                f"{self.base_url}/api/chat",
                json=payload,
                timeout=120
            )
            resp.raise_for_status()
            
            response = resp.json()
            content = response.get("message", {}).get("content", "")
            
            # Check if response indicates tool use
            if "Use tool:" in content or "tool:" in content.lower():
                # Parse and execute tool
                # This is simplified - full implementation would parse more carefully
                return self._handle_tool_request(content, user_message)
            
            return content
            
        except Exception as e:
            return f"Error: {e}"
    
    def _handle_tool_request(self, response: str, original_message: str) -> str:
        """Handle a tool request from Ollama's response."""
        # Simplified parsing - in production, use proper JSON parsing
        try:
            if "blog_manager" in response.lower():
                # Extract site and action from original message
                # This is a simplified version
                return "Tool execution would happen here. Full implementation needed."
            return response
        except:
            return response


if __name__ == "__main__":
    agent = OllamaToolAgent()
    
    if len(sys.argv) > 1:
        message = " ".join(sys.argv[1:])
        response = agent.chat_with_tools(message)
        print(response)
    else:
        print("Ollama Tool Agent")
        print("Usage: python ollama_tool_agent.py 'your message here'")

