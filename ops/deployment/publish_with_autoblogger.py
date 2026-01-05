#!/usr/bin/env python3
"""
Publish Blog Post via Autoblogger (Victor's Voice)
===================================================

Processes blog post drafts through the autoblogger system
to apply Victor's authentic voice patterns before publishing.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
import os
import yaml
from pathlib import Path
from typing import Dict, Optional

# Add autoblogger to path
AUTOBLOGGER_PATH = Path("D:/Agent_Cellphone_V2_Repository/temp_repos/Auto_Blogger")
if AUTOBLOGGER_PATH.exists():
    sys.path.insert(0, str(AUTOBLOGGER_PATH))

try:
    from src.autoblogger.services.blog_generator import BlogGenerator
    from src.autoblogger.services.vector_db import VectorDB
    from mistralai.client import MistralClient
    AUTOBLOGGER_AVAILABLE = True
except ImportError as e:
    AUTOBLOGGER_AVAILABLE = False
    print(f"⚠️  Autoblogger not available: {e}")
    print("   Will publish directly without voice processing")

try:
    import paramiko
    PARAMIKO_AVAILABLE = True
except ImportError:
    PARAMIKO_AVAILABLE = False

try:
    from dotenv import load_dotenv
    DOTENV_AVAILABLE = True
except ImportError:
    DOTENV_AVAILABLE = False


def load_victor_voice_template() -> Optional[Dict]:
    """Load Victor's voice template from YAML."""
    # Try multiple locations
    voice_template_paths = [
        Path("D:/Agent_Cellphone_V2_Repository/config/writing_style_template.yaml"),
        Path("D:/Agent_Cellphone_V2_Repository/temp_repos/Auto_Blogger/autoblogger/resources/voice_templates/digitaldreamscape_voice_template.yaml"),
        Path("D:/Agent_Cellphone_V2_Repository/temp_repos/Auto_Blogger/autoblogger/resources/voice_templates/houstonsipqueen_voice_template.yaml"),
    ]
    
    for template_path in voice_template_paths:
        if template_path.exists():
            try:
                with open(template_path, 'r', encoding='utf-8') as f:
                    template = yaml.safe_load(f)
                    print(f"✅ Loaded voice template: {template_path.name}")
                    return template
            except Exception as e:
                print(f"⚠️  Could not load {template_path}: {e}")
    
    print("⚠️  No voice template found, will use default processing")
    return None


def process_with_autoblogger(content: str, title: str, voice_template: Optional[Dict] = None) -> Optional[str]:
    """Process content through Ollama-enabled voice pattern processor with Victor's voice."""
    try:
        # Use the Ollama-enabled voice pattern processor
        from voice_pattern_processor import process_content_with_voice

        print("🤖 Processing with Victor's voice patterns (Ollama)...")

        # Use Ollama with Qwen model (no API keys needed)
        processed = process_content_with_voice(
            content=content,
            title=title,
            template_path=None,  # Auto-load from standard locations
            use_local_llm=True,  # Use local Ollama
            model="qwen2.5:7b"   # Use downloaded Qwen model
        )

        if processed and processed != content:
            print("✅ Voice patterns applied successfully (Ollama)")
            return processed
        else:
            print("⚠️  Voice processing returned original content")
            return content

    except ImportError as e:
        print(f"⚠️  Ollama voice pattern processor not available: {e}")
        print("💡 Make sure voice_pattern_processor.py is in ops/deployment/")
        return None

    except Exception as e:
        print(f"⚠️  Ollama processing error: {e}")
        import traceback
        traceback.print_exc()
        return None


def publish_post_via_wpcli(site_domain: str, title: str, content: str, status: str = 'publish') -> bool:
    """Publish a blog post using WP-CLI over SSH."""
    if not PARAMIKO_AVAILABLE:
        print("❌ paramiko library required")
        return False
    
    # Load credentials (same as publish_post_wpcli.py)
    import json
    
    if DOTENV_AVAILABLE:
        env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
        if env_path.exists():
            load_dotenv(env_path)
    
    hostinger_creds = {
        "host": os.getenv("HOSTINGER_HOST"),
        "username": os.getenv("HOSTINGER_USER"),
        "password": os.getenv("HOSTINGER_PASS"),
        "port": int(os.getenv("HOSTINGER_PORT", "65002"))
    }
    
    sites_json_path = Path("D:/Agent_Cellphone_V2_Repository/.deploy_credentials/sites.json")
    if sites_json_path.exists():
        try:
            with open(sites_json_path, 'r') as f:
                sites = json.load(f)
                site_creds = sites.get(site_domain)
                if site_creds:
                    creds = {
                        "host": site_creds.get('host') or hostinger_creds['host'],
                        "username": site_creds.get('username') or hostinger_creds['username'],
                        "password": site_creds.get('password') or hostinger_creds['password'],
                        "port": site_creds.get('port', hostinger_creds['port']),
                        "remote_path": site_creds.get('remote_path', f"domains/{site_domain}/public_html")
                    }
                else:
                    creds = {**hostinger_creds, "remote_path": f"domains/{site_domain}/public_html"}
        except Exception:
            creds = {**hostinger_creds, "remote_path": f"domains/{site_domain}/public_html"}
    else:
        creds = {**hostinger_creds, "remote_path": f"domains/{site_domain}/public_html"}
    
    if not all([creds.get('host'), creds.get('username'), creds.get('password')]):
        print(f"❌ Incomplete credentials for {site_domain}")
        return False
    
    try:
        ssh = paramiko.SSHClient()
        ssh.set_missing_host_key_policy(paramiko.AutoAddPolicy())
        ssh.connect(creds['host'], port=creds['port'], username=creds['username'], 
                   password=creds['password'], timeout=10)
        
        wp_path = f"/home/{creds['username']}/{creds['remote_path']}"
        
        # Escape for shell
        def escape_for_shell(text: str) -> str:
            return text.replace("'", "'\\''")
        
        escaped_title = escape_for_shell(title)
        escaped_content = escape_for_shell(content)
        
        command = f"cd {wp_path} && wp post create --post_title='{escaped_title}' --post_content='{escaped_content}' --post_status={status} --allow-root 2>&1"
        
        stdin, stdout, stderr = ssh.exec_command(command, timeout=30)
        output = stdout.read().decode()
        error = stderr.read().decode()
        result = output if output else error
        
        if "Success:" in result or "Created post" in result or "ID:" in result:
            # Extract post ID
            post_id = None
            for line in result.split('\n'):
                if 'ID:' in line or 'post' in line.lower():
                    parts = line.split()
                    for i, part in enumerate(parts):
                        if part.isdigit():
                            post_id = part
                            break
                    if post_id:
                        break
            
            if post_id:
                url_cmd = f"cd {wp_path} && wp post get {post_id} --field=url --allow-root 2>&1"
                stdin, stdout, stderr = ssh.exec_command(url_cmd, timeout=10)
                post_url = stdout.read().decode().strip()
                
                print(f"✅ Post published successfully!")
                print(f"   Post ID: {post_id}")
                if post_url:
                    print(f"   URL: {post_url}")
                else:
                    print(f"   URL: https://{site_domain}/?p={post_id}")
            else:
                print(f"✅ Post published successfully!")
            
            ssh.close()
            return True
        else:
            print(f"❌ Failed to publish post")
            print(f"   {result[:500]}")
            ssh.close()
            return False
        
    except Exception as e:
        print(f"❌ SSH error: {e}")
        import traceback
        traceback.print_exc()
        return False


def format_content_as_html(content: str) -> str:
    """Convert markdown-like content to HTML."""
    paragraphs = content.split('\n\n')
    html_parts = []
    
    for para in paragraphs:
        para = para.strip()
        if not para:
            continue
        
        if para.startswith('### '):
            html_parts.append(f'<h3>{para[4:]}</h3>')
        elif para.startswith('## '):
            html_parts.append(f'<h2>{para[3:]}</h2>')
        elif para.startswith('# '):
            html_parts.append(f'<h1>{para[2:]}</h1>')
        elif para.startswith('* ') or para.startswith('- '):
            items = [line.strip()[2:] for line in para.split('\n') if line.strip().startswith(('* ', '- '))]
            if items:
                html_parts.append('<ul>')
                for item in items:
                    item = item.replace('**', '<strong>', 1).replace('**', '</strong>', 1)
                    item = item.replace('*', '<em>', 1).replace('*', '</em>', 1)
                    html_parts.append(f'<li>{item}</li>')
                html_parts.append('</ul>')
        elif '**' in para:
            parts = para.split('**')
            formatted = ''
            for i, part in enumerate(parts):
                if i % 2 == 1:
                    formatted += f'<strong>{part}</strong>'
                else:
                    formatted += part
            html_parts.append(f'<p>{formatted}</p>')
        elif '*' in para and not para.startswith('*'):
            parts = para.split('*')
            formatted = ''
            for i, part in enumerate(parts):
                if i % 2 == 1:
                    formatted += f'<em>{part}</em>'
                else:
                    formatted += part
            html_parts.append(f'<p>{formatted}</p>')
        else:
            html_parts.append(f'<p>{para}</p>')
    
    return '\n'.join(html_parts)


def main():
    """Main execution."""
    import argparse
    
    parser = argparse.ArgumentParser(
        description='Publish blog post via autoblogger (Victor\'s voice)'
    )
    parser.add_argument('--site', type=str, required=True, help='Site domain')
    parser.add_argument('--title', type=str, required=True, help='Post title')
    parser.add_argument('--content', type=str, help='Post content (or read from stdin)')
    parser.add_argument('--file', type=str, help='Read content from file')
    parser.add_argument('--status', type=str, default='publish', choices=['draft', 'publish'], help='Post status')
    parser.add_argument('--skip-autoblogger', action='store_true', help='Skip autoblogger processing')
    
    args = parser.parse_args()
    
    # Get content
    if args.file:
        with open(args.file, 'r', encoding='utf-8') as f:
            content = f.read()
    elif args.content:
        content = args.content
    else:
        content = sys.stdin.read()
    
    print("\n" + "="*60)
    print("📝 WORDPRESS BLOG POST PUBLISHER (WITH AUTOBLOGGER)")
    print("="*60)
    
    # Load voice template
    voice_template = None
    if not args.skip_autoblogger:
        voice_template = load_victor_voice_template()
    
    # Process through Ollama-enabled voice pattern processor
    processed_content = content
    if not args.skip_autoblogger:
        # Import the Ollama-enabled voice pattern processor from ops/deployment/
        try:
            # Add ops/deployment to path for the Ollama-enabled voice_pattern_processor
            ops_deployment_dir = Path(__file__).parent
            if str(ops_deployment_dir) not in sys.path:
                sys.path.insert(0, str(ops_deployment_dir))

            # Import the Ollama-enabled voice pattern processor
            from voice_pattern_processor import VoicePatternProcessor, process_content_with_voice

            # Use the convenience function that prefers Ollama (Qwen model)
            processed = process_content_with_voice(
                content=content,
                title=args.title,
                template_path=None,  # Will auto-load from standard locations
                use_local_llm=True,  # Prefer Ollama/Qwen over API
                model="qwen2.5:7b"   # Use the downloaded Qwen model
            )

            if processed and processed != content:
                processed_content = processed
                print("✅ Content processed with Victor's voice patterns (Ollama Qwen)")
            else:
                print("⚠️  Voice processing returned original content")
        except Exception as e:
            print(f"⚠️  Ollama voice pattern processor error: {e}")
            # Fallback to direct Ollama call
            try:
                from voice_pattern_processor import VoicePatternProcessor
                processor = VoicePatternProcessor(use_local_llm=True)
                if voice_template and not processor.voice_template:
                    processor.voice_template = voice_template
                processed = processor.apply_voice_patterns(content, args.title, model="qwen2.5:7b")
                if processed and processed != content:
                    processed_content = processed
                    print("✅ Content processed with Victor's voice patterns (Ollama fallback)")
                else:
                    print("⚠️  Voice processing returned original content")
            except Exception as e2:
                print(f"⚠️  All voice processing failed: {e2}")
                print("⚠️  Using original content (Ollama unavailable)")
    else:
        print("⚠️  Skipping voice pattern processing")
    
    # Format as HTML
    html_content = format_content_as_html(processed_content)
    
    # Publish
    success = publish_post_via_wpcli(
        args.site,
        args.title,
        html_content,
        args.status
    )
    
    return 0 if success else 1


if __name__ == '__main__':
    exit(main())

