#!/usr/bin/env python3
"""
Voice Pattern Processor
=======================

Injects Victor's voice patterns into blog post content by:
1. Loading voice templates from YAML
2. Processing content through Mistral with voice instructions
3. Applying voice patterns to existing content

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import yaml
import os
import sys
from pathlib import Path
from typing import Dict, Optional, List
from dotenv import load_dotenv

try:
    from mistralai.client import MistralClient
    MISTRAL_AVAILABLE = True
except ImportError:
    MISTRAL_AVAILABLE = False

# Try to use local Ollama LLM
try:
    import subprocess
    import shlex
    OLLAMA_AVAILABLE = True
except ImportError:
    OLLAMA_AVAILABLE = False

# Try to use OllamaClient from main repo
try:
    import sys
    sys.path.insert(0, "D:/Agent_Cellphone_V2_Repository/src/integrations/jarvis")
    from ollama_integration import OllamaClient
    OLLAMA_CLIENT_AVAILABLE = True
except (ImportError, Exception):
    OLLAMA_CLIENT_AVAILABLE = False


class VoicePatternProcessor:
    """Processes content to apply Victor's authentic voice patterns."""
    
    def __init__(self, voice_template_path: Optional[Path] = None, use_local_llm: bool = True):
        """Initialize with voice template."""
        self.voice_template = self._load_voice_template(voice_template_path)
        self.mistral_client = None
        self.ollama_client = None
        self.use_local_llm = use_local_llm
        
        # Prefer local Ollama LLM if available
        if use_local_llm and OLLAMA_CLIENT_AVAILABLE:
            try:
                self.ollama_client = OllamaClient()
                # Check availability (but don't fail if not running yet)
                try:
                    if self.ollama_client.is_available():
                        print("✅ Using local Ollama LLM (OllamaClient)")
                    else:
                        print("⚠️  Ollama not running, will try subprocess method or Mistral API")
                except Exception:
                    print("⚠️  Ollama check failed, will try subprocess method or Mistral API")
            except Exception as e:
                print(f"⚠️  Ollama client init error: {e}, will try subprocess method or Mistral API")
                self.ollama_client = None
        
        # Fallback to Mistral API
        if not self.ollama_client and MISTRAL_AVAILABLE:
            env_path = Path("D:/Agent_Cellphone_V2_Repository/.env")
            if env_path.exists():
                load_dotenv(env_path)
            
            mistral_key = os.getenv("MISTRAL_API_KEY")
            if mistral_key:
                self.mistral_client = MistralClient(api_key=mistral_key)
                print("✅ Using Mistral API (fallback)")
    
    def _load_voice_template(self, template_path: Optional[Path] = None) -> Optional[Dict]:
        """Load voice template from YAML file."""
        if template_path and template_path.exists():
            paths = [template_path]
        else:
            # Try multiple locations
            paths = [
                template_path,  # User-specified path first
                Path("config/voice_profiles/victor_voice_profile.yaml"),  # Websites repo unified profiles
                Path("D:/Agent_Cellphone_V2_Repository/config/voice_profiles/victor_voice_profile.yaml"),  # Agent repo unified profiles
                Path("D:/Agent_Cellphone_V2_Repository/config/writing_style_template.yaml"),  # Legacy location
                Path("D:/Agent_Cellphone_V2_Repository/temp_repos/Auto_Blogger/autoblogger/resources/voice_templates/digitaldreamscape_voice_template.yaml"),
                Path("D:/Agent_Cellphone_V2_Repository/temp_repos/Auto_Blogger/autoblogger/resources/voice_templates/houstonsipqueen_voice_template.yaml"),
            ]
        
        for path in paths:
            if path.exists():
                try:
                    with open(path, 'r', encoding='utf-8') as f:
                        template = yaml.safe_load(f)
                        print(f"✅ Loaded voice template: {path.name}")
                        return template
                except Exception as e:
                    print(f"⚠️  Could not load {path}: {e}")
        
        print("⚠️  No voice template found")
        return None
    
    def _build_voice_instructions(self) -> str:
        """Build voice pattern instructions from template."""
        if not self.voice_template:
            return ""
        
        instructions = []
        voice_profile = self.voice_template.get('voice_profile', {})
        
        # Base tone and style
        if voice_profile.get('base_tone'):
            tones = voice_profile['base_tone']
            if isinstance(tones, list):
                # Filter out non-string items and join
                tone_strs = [str(t) for t in tones if isinstance(t, (str, int, float))]
                if tone_strs:
                    instructions.append(f"TONE: {', '.join(tone_strs)}")
            elif isinstance(tones, str):
                instructions.append(f"TONE: {tones}")
        
        if voice_profile.get('goal'):
            instructions.append(f"GOAL: {voice_profile['goal']}")
        
        # Mechanics
        mechanics = voice_profile.get('mechanics', {})
        if mechanics.get('casing'):
            instructions.append(f"CASING: Use {', '.join(mechanics['casing'])}")
        
        if mechanics.get('punctuation'):
            instructions.append(f"PUNCTUATION: {', '.join(mechanics['punctuation'])}")
        
        # Shortening
        shortening = voice_profile.get('shortening', [])
        if shortening:
            instructions.append(f"SHORTHAND: Use these abbreviations naturally: {', '.join(shortening)}")
        
        # Phrasing patterns
        phrasing = voice_profile.get('phrasing_patterns', {})
        if phrasing:
            for category, patterns in phrasing.items():
                if patterns:
                    if isinstance(patterns, list):
                        instructions.append(f"{category.upper()}: Use phrases like {', '.join(patterns[:5])}")
        
        # Structure
        structure = voice_profile.get('structure', [])
        if structure:
            instructions.append(f"STRUCTURE: {structure[0] if isinstance(structure, list) else structure}")
        
        # Voice markers
        markers = self.voice_template.get('unique_voice_markers', [])
        if markers:
            instructions.append(f"VOICE MARKERS: Naturally include phrases like {', '.join(markers[:5])}")
        
        # Do's and Don'ts
        do_list = voice_profile.get('do', [])
        dont_list = voice_profile.get('dont', [])
        
        if do_list:
            instructions.append(f"DO: {do_list[0] if isinstance(do_list, list) else do_list}")
        
        if dont_list:
            instructions.append(f"DON'T: {dont_list[0] if isinstance(dont_list, list) else dont_list}")
        
        return "\n".join(instructions)
    
    def _post_process_content(self, content: str) -> str:
        """
        Post-process content to remove dead giveaways that it wasn't written by Victor.
        
        This catches things that might slip through the LLM processing.
        """
        result = content
        
        # CRITICAL: Remove em dashes and en dashes (dead giveaway)
        # Victor never uses these - he uses ... (ellipsis) instead for pacing
        # Handle spacing: "word — word" becomes "word...word" (no spaces around ellipsis)
        
        # Replace em dash with spaces around it: " — " → "..."
        result = result.replace(' — ', '...')
        result = result.replace(' —', '...')  # Space before, no space after
        result = result.replace('— ', '...')  # No space before, space after
        result = result.replace('—', '...')   # No spaces at all
        
        # Same for en dash
        result = result.replace(' – ', '...')
        result = result.replace(' –', '...')
        result = result.replace('– ', '...')
        result = result.replace('–', '...')
        
        # Also check for other "polished" punctuation that Victor doesn't use
        # (Add more as needed)
        
        return result
    
    def apply_voice_patterns(self, content: str, title: str = "", model: str = "mistral:latest") -> str:
        """
        Apply Victor's voice patterns to content.
        
        Args:
            content: Original blog post content
            title: Post title (optional)
            model: Ollama model to use (default: "mistral:latest")
        
        Returns:
            Content with voice patterns applied
        """
        if not self.voice_template:
            print("⚠️  Voice template not loaded, returning original content")
            return self._post_process_content(content)  # Still post-process even if no template
        
        print("🎤 Applying Victor's voice patterns...")
        
        # Build prompt using the actual template structure
        voice_profile = self.voice_template.get('voice_profile', {})
        mechanics = voice_profile.get('mechanics', {})
        shortening = mechanics.get('shortening', [])
        phrasing = voice_profile.get('phrasing_patterns', {})
        
        # Build detailed instructions from template
        casing_rules = mechanics.get('casing', [])
        shortening_list = ', '.join(shortening[:10]) if shortening else 'js, cs, idk, tbh, rn, lol, tryna, gon, wanna, kinda, lowkey'
        intros = phrasing.get('intros', []) if isinstance(phrasing, dict) else []
        intros_list = ', '.join(intros) if intros else 'ok so, lowkey feel like, so hear me out'
        
        # Build casing rules string separately to avoid f-string backslash issue
        casing_text = ', '.join(casing_rules) if casing_rules else 'Use lowercase: i, im, id, dont, cant (not I, I am, do not, cannot)'
        
        prompt = f"""Rewrite this blog post to match Victor's authentic voice patterns EXACTLY as specified in the template.

CRITICAL RULES FROM TEMPLATE:
1. CASING: {casing_text}
2. SHORTHAND (use these EXACTLY): {shortening_list}
3. INTROS (use naturally): {intros_list}
4. TONE: Casual, direct, stream-of-consciousness, thinking while typing
5. STRUCTURE: Keep original markdown format (## headers, * bullets, **bold**)
6. DO NOT: Use slang NOT in template (no "ppl", "vibin", "sumthin", "jus", "dig", "ya", "aint")
7. DO: Use ONLY the shorthand from template: js, cs, idk, tbh, rn, lol, tryna, gon, wanna, kinda, lowkey
8. PUNCTUATION: NEVER use em dashes (—) or en dashes (–). Use ellipsis (...) instead for pauses and pacing. When replacing dashes, use ellipsis with NO spaces: "word — word" becomes "word...word" (not "word ... word"). This is a DEAD GIVEAWAY that the content wasn't written by Victor.

ORIGINAL CONTENT:
{content}

Rewrite maintaining:
- Same markdown structure (headers, bullets, bold)
- Same meaning and flow
- Victor's voice patterns from template ONLY
- No made-up slang or abbreviations
- NO em dashes (—) or en dashes (–) - use ... (ellipsis) instead for pauses, with NO spaces around it: "word — word" becomes "word...word"

Return ONLY the rewritten content with proper markdown formatting."""
        
        # Try local Ollama first (OllamaClient API method)
        if self.ollama_client and self.use_local_llm:
            try:
                # Check if Ollama is actually available
                if self.ollama_client.is_available():
                    print(f"   Using local Ollama model (API): {model}")
                    # Build full prompt with system message
                    full_prompt = f"""You are a voice pattern expert who rewrites content to match specific authentic voice patterns while preserving meaning and structure.

{prompt}"""
                    
                    response = self.ollama_client.generate(
                        model=model,
                        prompt=full_prompt,
                        temperature=0.7,
                        max_tokens=4000  # Allow longer responses
                    )
                    
                    if response and response.response:
                        rewritten = response.response.strip()
                        
                        # Clean up if there are any markdown code blocks
                        if rewritten.startswith("```"):
                            lines = rewritten.split('\n')
                            rewritten = '\n'.join(lines[1:-1]) if len(lines) > 2 else rewritten
                        
                        # Final post-processing to catch any dead giveaways
                        rewritten = self._post_process_content(rewritten)
                        
                        print("✅ Voice patterns applied successfully (Ollama API)")
                        return rewritten
                    else:
                        print("⚠️  Ollama API returned empty response, trying subprocess method")
                else:
                    print("⚠️  Ollama not running, trying subprocess method")
            except Exception as e:
                print(f"⚠️  Ollama API error: {e}, trying subprocess method")
        
        # Fallback to Mistral API
        if self.mistral_client:
            try:
                print("   Using Mistral API (fallback)")
                response = self.mistral_client.chat(
                    model="mistral-medium",  # Use medium for better quality
                    messages=[
                        {
                            "role": "system",
                            "content": "You are a voice pattern expert who rewrites content to match specific authentic voice patterns while preserving meaning and structure."
                        },
                        {"role": "user", "content": prompt}
                    ],
                    temperature=0.7  # Some creativity but not too much
                )
                
                rewritten = response.choices[0].message.content.strip()
                
                # Clean up if there are any markdown code blocks
                if rewritten.startswith("```"):
                    lines = rewritten.split('\n')
                    rewritten = '\n'.join(lines[1:-1]) if len(lines) > 2 else rewritten
                
                        # Final post-processing to catch any dead giveaways
                        rewritten = self._post_process_content(rewritten)
                        
                        print("✅ Voice patterns applied successfully (Mistral API)")
                        return rewritten
                
            except Exception as e:
                print(f"⚠️  Error applying voice patterns: {e}")
                import traceback
                traceback.print_exc()
                return content  # Return original on error
        
        # Try subprocess ollama (like autoblogger does) - works even if API isn't available
        if OLLAMA_AVAILABLE and self.use_local_llm:
            try:
                print(f"   Trying subprocess Ollama: {model}")
                
                # Write prompt to temp file to avoid shell escaping issues
                import tempfile
                with tempfile.NamedTemporaryFile(mode='w', suffix='.txt', delete=False, encoding='utf-8') as f:
                    f.write(prompt)
                    temp_prompt_file = f.name
                
                # Use stdin to pass prompt to Ollama
                # This avoids shell escaping issues
                try:
                    result = subprocess.run(
                        ['ollama', 'run', model],
                        input=prompt,
                        stdout=subprocess.PIPE,
                        stderr=subprocess.PIPE,
                        text=True,
                        encoding="utf-8",
                        timeout=300,  # 5 minute timeout for longer content
                        check=True
                    )
                    rewritten = result.stdout.strip()
                    
                    # Clean up temp file
                    try:
                        os.unlink(temp_prompt_file)
                    except:
                        pass
                    
                    if rewritten:
                        # Clean up the response
                        # Remove any leading/trailing explanation text
                        lines = rewritten.split('\n')
                        
                        # Find where actual content starts (skip meta/explanatory text)
                        content_start = 0
                        for i, line in enumerate(lines):
                            line_lower = line.lower()
                            if any(marker in line_lower for marker in ['lately', 'where are', 'not "better"', 'because i', 'ive been', '**where are']):
                                content_start = i
                                break
                        
                        if content_start > 0:
                            rewritten = '\n'.join(lines[content_start:])
                        
                        # Clean up if there are any markdown code blocks
                        if rewritten.startswith("```"):
                            lines = rewritten.split('\n')
                            rewritten = '\n'.join(lines[1:-1]) if len(lines) > 2 else rewritten
                        
                        # Fix formatting issues (remove trailing backslashes, fix line breaks)
                        rewritten = rewritten.replace('\\\n', '\n')  # Remove escaped newlines
                        rewritten = rewritten.replace('\\', '')  # Remove any remaining backslashes
                        
                        # Ensure proper markdown formatting
                        # Fix any broken markdown patterns
                        rewritten = rewritten.replace('** ', '**').replace(' **', '**')
                        
                        # Final post-processing to catch any dead giveaways
                        rewritten = self._post_process_content(rewritten)
                        
                        print("✅ Voice patterns applied successfully (subprocess Ollama)")
                        return rewritten
                    else:
                        print("⚠️  Ollama subprocess returned empty response")
                except Exception as e:
                    # Clean up temp file on error
                    try:
                        os.unlink(temp_prompt_file)
                    except:
                        pass
                    raise e
            except subprocess.TimeoutExpired:
                print("⚠️  Ollama subprocess timed out (content may be too long)")
            except subprocess.CalledProcessError as e:
                error_msg = e.stderr.strip() if e.stderr else str(e)
                print(f"⚠️  Ollama subprocess error: {error_msg}")
                if "model" in error_msg.lower() and "not found" in error_msg.lower():
                    print(f"   💡 Try: ollama pull {model}")
            except FileNotFoundError:
                print("⚠️  Ollama command not found. Is Ollama installed?")
            except Exception as e:
                print(f"⚠️  Ollama subprocess unexpected error: {e}")
        
        print("⚠️  No LLM available, returning original content (post-processed)")
        return self._post_process_content(content)  # Still post-process even if no LLM
    
    def apply_voice_patterns_simple(self, content: str) -> str:
        """
        Simple rule-based voice pattern application (fallback).
        Applies basic patterns without AI processing.
        """
        if not self.voice_template:
            return content
        
        voice_profile = self.voice_template.get('voice_profile', {})
        shortening = voice_profile.get('shortening', [])
        
        # Simple replacements (very basic, AI is better)
        # This is just a fallback
        result = content
        
        # Note: Full voice pattern application really needs AI
        # This is just a placeholder for when Mistral isn't available
        
        return result


def process_content_with_voice(
    content: str,
    title: str = "",
    template_path: Optional[Path] = None,
    use_local_llm: bool = True,
    model: str = "mistral:latest"
) -> str:
    """
    Convenience function to process content with voice patterns.
    
    Args:
        content: Blog post content
        title: Post title
        template_path: Optional path to voice template YAML
        use_local_llm: Whether to prefer local Ollama (default: True)
        model: Ollama model to use (default: "mistral:latest")
    
    Returns:
        Content with voice patterns applied
    """
    processor = VoicePatternProcessor(template_path, use_local_llm=use_local_llm)
    return processor.apply_voice_patterns(content, title, model=model)


if __name__ == '__main__':
    """CLI interface for testing."""
    import argparse
    
    parser = argparse.ArgumentParser(description='Apply voice patterns to content')
    parser.add_argument('--file', type=str, help='Input file')
    parser.add_argument('--title', type=str, default='', help='Post title')
    parser.add_argument('--output', type=str, help='Output file')
    parser.add_argument('--template', type=str, help='Voice template path')
    parser.add_argument('--model', type=str, default='mistral:latest', help='Ollama model (default: mistral:latest)')
    parser.add_argument('--use-api', action='store_true', help='Force use of Mistral API instead of local Ollama')
    
    args = parser.parse_args()
    
    # Get content
    if args.file:
        with open(args.file, 'r', encoding='utf-8') as f:
            content = f.read()
    else:
        content = sys.stdin.read()
    
    # Process
    template_path = Path(args.template) if args.template else None
    processor = VoicePatternProcessor(template_path, use_local_llm=not args.use_api)
    result = processor.apply_voice_patterns(content, args.title, model=args.model)
    
    # Output
    if args.output:
        with open(args.output, 'w', encoding='utf-8') as f:
            f.write(result)
        print(f"✅ Saved to {args.output}")
    else:
        print("\n" + "="*60)
        print("PROCESSED CONTENT:")
        print("="*60)
        print(result)

