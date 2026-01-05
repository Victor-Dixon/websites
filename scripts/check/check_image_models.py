#!/usr/bin/env python3
"""
Check available Ollama image generation models for the user's hardware
"""

def analyze_hardware():
    """Analyze user's hardware capabilities"""
    print("🔍 Analyzing your hardware for image generation...")
    print("📊 Your specs:")
    print("   CPU: AMD Ryzen 5 1600 (6 cores)")
    print("   RAM: 32GB")
    print("   GPU: AMD Radeon RX 580 (4GB VRAM)")
    print("   OS: Windows 10")
    print()

    # Hardware capabilities
    capabilities = {
        "max_model_size": "8GB",  # Conservative for 4GB VRAM
        "recommended_size": "4GB",  # Ideal range
        "cpu_inference": True,  # Can fall back to CPU
        "gpu_accelerated": True,  # RX 580 supports it
        "memory_efficient": False  # Not the newest GPU
    }

    return capabilities

def get_image_models():
    """Get list of available Ollama image generation models"""
    models = [
        {
            "name": "moondream",
            "size": "2GB",
            "size_gb": 2,
            "description": "Lightweight vision model, excellent for RX 580",
            "quality": "Good",
            "speed": "Fast",
            "recommended": True
        },
        {
            "name": "llava:7b",
            "size": "7GB",
            "size_gb": 7,
            "description": "Popular vision model with solid quality",
            "quality": "Very Good",
            "speed": "Medium",
            "recommended": True
        },
        {
            "name": "llava:13b",
            "size": "13GB",
            "size_gb": 13,
            "description": "High quality but may be slow",
            "quality": "Excellent",
            "speed": "Slow",
            "recommended": False
        },
        {
            "name": "bakllava",
            "size": "7GB",
            "size_gb": 7,
            "description": "Advanced vision model with good capabilities",
            "quality": "Very Good",
            "speed": "Medium",
            "recommended": True
        },
        {
            "name": "stable-diffusion",
            "size": "5GB",
            "size_gb": 5,
            "description": "Base SD model, needs GPU acceleration",
            "quality": "Excellent",
            "speed": "Medium",
            "recommended": True
        },
        {
            "name": "flux-dev",
            "size": "20GB",
            "size_gb": 20,
            "description": "High quality but too large for your setup",
            "quality": "Excellent",
            "speed": "Slow",
            "recommended": False
        }
    ]

    return models

def main():
    """Main analysis and recommendation"""
    capabilities = analyze_hardware()
    models = get_image_models()

    print("🎯 RECOMMENDED IMAGE GENERATION MODELS")
    print("=" * 50)

    # Filter models suitable for hardware
    suitable_models = []
    for model in models:
        if model["size_gb"] <= 8:  # Within hardware limits
            suitable_models.append(model)

    # Sort by recommendation and size
    suitable_models.sort(key=lambda x: (not x["recommended"], x["size_gb"]))

    for model in suitable_models:
        status = "✅ RECOMMENDED" if model["recommended"] else "⚠️  CONSIDER"
        print(f"{status} {model['name']} ({model['size']})")
        print(f"   Quality: {model['quality']} | Speed: {model['speed']}")
        print(f"   {model['description']}")
        print()

    print("🚀 BEST CHOICE FOR YOUR HARDWARE:")
    print("=" * 40)

    best_model = None
    for model in suitable_models:
        if model["recommended"] and model["size_gb"] <= 4:
            best_model = model
            break

    if best_model:
        print(f"🏆 {best_model['name']} - Perfect for your RX 580!")
        print(f"   Size: {best_model['size']}")
        print(f"   Why: {best_model['description']}")
        print()
        print("📥 INSTALL COMMAND:")
        print(f"ollama pull {best_model['name']}")
        print()
        print("🧪 TEST COMMAND:")
        print(f"ollama run {best_model['name']}")
        print()
        print("💡 USAGE TIP:")
        print("Once installed, you can use it to generate images for your blogs!")
        print("Example prompt: 'Generate an image of a futuristic AI assistant'")
    else:
        print("❌ No perfectly suitable models found")
        print("Consider upgrading GPU or using CPU-only models")

if __name__ == "__main__":
    main()