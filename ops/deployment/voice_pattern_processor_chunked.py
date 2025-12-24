#!/usr/bin/env python3
"""
Voice Pattern Processor (Chunked Processing)
=============================================

Processes long content in chunks to avoid timeouts.

Author: Agent-7 (Web Development Specialist)
Date: 2025-12-22
"""

import sys
from pathlib import Path
from voice_pattern_processor import VoicePatternProcessor


def process_in_chunks(processor: VoicePatternProcessor, content: str, title: str = "", chunk_size: int = 500) -> str:
    """Process content in smaller chunks."""
    paragraphs = content.split('\n\n')
    processed_chunks = []
    
    # Group paragraphs into chunks
    current_chunk = []
    current_length = 0
    
    for para in paragraphs:
        para_length = len(para)
        if current_length + para_length > chunk_size and current_chunk:
            # Process current chunk
            chunk_text = '\n\n'.join(current_chunk)
            processed = processor.apply_voice_patterns(chunk_text, title if not processed_chunks else "")
            processed_chunks.append(processed)
            
            # Start new chunk
            current_chunk = [para]
            current_length = para_length
        else:
            current_chunk.append(para)
            current_length += para_length
    
    # Process remaining chunk
    if current_chunk:
        chunk_text = '\n\n'.join(current_chunk)
        processed = processor.apply_voice_patterns(chunk_text, title if not processed_chunks else "")
        processed_chunks.append(processed)
    
    return '\n\n'.join(processed_chunks)


def main():
    """Main execution."""
    import argparse
    
    parser = argparse.ArgumentParser(description='Apply voice patterns (chunked)')
    parser.add_argument('--file', type=str, help='Input file')
    parser.add_argument('--title', type=str, default='', help='Post title')
    parser.add_argument('--output', type=str, help='Output file')
    parser.add_argument('--chunk-size', type=int, default=500, help='Characters per chunk')
    
    args = parser.parse_args()
    
    # Get content
    if args.file:
        with open(args.file, 'r', encoding='utf-8') as f:
            content = f.read()
    else:
        content = sys.stdin.read()
    
    # Process
    processor = VoicePatternProcessor()
    result = process_in_chunks(processor, content, args.title, args.chunk_size)
    
    # Output
    if args.output:
        with open(args.output, 'w', encoding='utf-8') as f:
            f.write(result)
        print(f"✅ Saved to {args.output}")
    else:
        print(result)


if __name__ == '__main__':
    main()


