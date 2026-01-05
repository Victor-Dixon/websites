#!/usr/bin/env python3
"""
Test script for using moondream to generate images for blogs
"""

import sys
import os
from pathlib import Path

def test_moondream_generation():
    """Test moondream image generation capabilities"""

    print("🎨 Testing Moondream Image Generation")
    print("=" * 45)
    print("This will demonstrate how to generate images for your blogs")
    print()

    # Test prompts suitable for Digital Dreamscape blog
    test_prompts = [
        "Generate an image of a futuristic AI assistant in a digital realm",
        "Create an image of interconnected neural networks glowing in cyberspace",
        "Visualize a human-AI collaboration workspace in a modern office",
        "Depict an abstract representation of machine learning algorithms",
        "Show a holographic interface displaying data visualization"
    ]

    print("📝 Sample prompts for your Digital Dreamscape blog:")
    for i, prompt in enumerate(test_prompts, 1):
        print(f"{i}. {prompt}")
    print()

    # Test basic functionality
    print("🧪 Testing moondream capabilities...")
    try:
        import subprocess

        # Test 1: Basic text generation (moondream can do both)
        print("Test 1: Text generation capability")
        result = subprocess.run(
            ['ollama', 'run', 'moondream'],
            input='Describe what you can do for generating blog images',
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            text=True,
            timeout=30,
            check=True
        )

        response = result.stdout.strip()
        print(f"✅ Moondream response: {response[:200]}...")
        print()

        # Test 2: Vision capabilities info
        print("Test 2: Vision and image understanding")
        result2 = subprocess.run(
            ['ollama', 'run', 'moondream'],
            input='What kind of images can you help generate or analyze for a tech blog?',
            stdout=subprocess.PIPE,
            stderr=subprocess.PIPE,
            text=True,
            timeout=30,
            check=True
        )

        response2 = result2.stdout.strip()
        print(f"✅ Vision capabilities: {response2[:200]}...")
        print()

    except subprocess.TimeoutExpired:
        print("❌ Test timed out - moondream may be busy")
    except Exception as e:
        print(f"❌ Test failed: {e}")

    print("\n🎯 HOW TO USE MOONDREAM FOR YOUR BLOGS:")
    print("=" * 45)
    print("1. Interactive mode:")
    print("   ollama run moondream")
    print("   Then type prompts like:")
    print("   'Generate an image description of a cyberpunk AI laboratory'")
    print()

    print("2. From scripts (like your autoblogger):")
    print("   Use subprocess to call ollama and get image descriptions")
    print()

    print("3. Integration ideas:")
    print("   - Generate image prompts for AI image tools")
    print("   - Create detailed scene descriptions")
    print("   - Brainstorm visual concepts for blog posts")
    print("   - Analyze existing images for blog content")
    print()

    print("🚀 MOONDREAM IS READY FOR YOUR DIGITAL DREAMSCAPE BLOGS!")
    print("💡 Try: ollama run moondream")

def main():
    """Main function"""
    test_moondream_generation()

if __name__ == "__main__":
    main()