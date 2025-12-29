#!/usr/bin/env python3
"""
Generate Event Bar Planning Checklist PDF for Houston Sip Queen
Lead Magnet PDF Generator

Usage:
    python generate_hsq_lead_magnet_pdf.py
"""

try:
    from reportlab.lib.pagesizes import letter
    from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
    from reportlab.lib.units import inch
    from reportlab.platypus import SimpleDocTemplate, Paragraph, Spacer, PageBreak, Table, TableStyle
    from reportlab.lib import colors
    from reportlab.lib.enums import TA_CENTER, TA_LEFT
    HAS_REPORTLAB = True
except ImportError:
    HAS_REPORTLAB = False
    print("⚠️  reportlab not installed. Install with: pip install reportlab")

import os
from pathlib import Path

def generate_pdf(output_path):
    """Generate the Event Bar Planning Checklist PDF."""
    if not HAS_REPORTLAB:
        print("❌ Cannot generate PDF - reportlab not installed")
        return False
    
    doc = SimpleDocTemplate(output_path, pagesize=letter,
                            rightMargin=0.75*inch, leftMargin=0.75*inch,
                            topMargin=0.75*inch, bottomMargin=0.75*inch)
    
    story = []
    styles = getSampleStyleSheet()
    
    # Custom styles
    title_style = ParagraphStyle(
        'CustomTitle',
        parent=styles['Heading1'],
        fontSize=24,
        textColor=colors.HexColor('#d4af37'),
        spaceAfter=12,
        alignment=TA_CENTER
    )
    
    heading_style = ParagraphStyle(
        'CustomHeading',
        parent=styles['Heading2'],
        fontSize=16,
        textColor=colors.HexColor('#111111'),
        spaceAfter=8,
        spaceBefore=12
    )
    
    # Title
    story.append(Paragraph("Ultimate Event Bar Planning Checklist", title_style))
    story.append(Spacer(1, 0.2*inch))
    story.append(Paragraph("Houston Sip Queen - Luxury Mobile Bartending", styles['Normal']))
    story.append(Spacer(1, 0.3*inch))
    
    # Introduction
    intro_text = """
    Planning an event bar? Use this comprehensive checklist to ensure your bar service is perfect. 
    From initial planning to day-of execution, we've got you covered.
    """
    story.append(Paragraph(intro_text, styles['Normal']))
    story.append(Spacer(1, 0.2*inch))
    
    # Pre-Planning Section
    story.append(Paragraph("Pre-Planning (4-6 Weeks Before)", heading_style))
    
    pre_planning_items = [
        ["Determine Bar Needs", [
            "Estimate guest count",
            "Determine bar type (open bar, cash bar, wine/beer only)",
            "Plan for duration of event",
            "Consider event style (formal, casual, themed)"
        ]],
        ["Set Bar Budget", [
            "Allocate funds for: beverages, bartenders, equipment, setup",
            "Plan for 1.5 drinks per guest per hour",
            "Set aside 10-15% contingency fund",
            "Consider premium vs standard options"
        ]],
        ["Choose Bar Service Type", [
            "Mobile bar service (brings bar to you)",
            "Venue bar service (if venue allows)",
            "DIY bar (if permitted)",
            "Hybrid approach"
        ]]
    ]
    
    for item_title, sub_items in pre_planning_items:
        story.append(Paragraph(f"<b>{item_title}</b>", styles['Normal']))
        for sub_item in sub_items:
            story.append(Paragraph(f"• {sub_item}", styles['Normal'], leftIndent=0.2*inch))
        story.append(Spacer(1, 0.1*inch))
    
    story.append(PageBreak())
    
    # Beverage Planning Section
    story.append(Paragraph("Beverage Planning (3-4 Weeks Before)", heading_style))
    
    beverage_items = [
        ["Wine Selection", ["Red wine (2-3 options)", "White wine (2-3 options)", "Sparkling wine/champagne (if applicable)", "Consider guest preferences"]],
        ["Beer Selection", ["Light beer option", "Craft beer option", "Import option", "Non-alcoholic beer"]],
        ["Spirits & Cocktails", ["Vodka, gin, rum, whiskey, tequila", "Signature cocktails (2-3 options)", "Mixers and garnishes", "Non-alcoholic options"]],
        ["Bar Equipment", ["Bar setup and equipment", "Glassware (wine, beer, cocktail glasses)", "Ice supply", "Bar tools (shakers, strainers, etc.)"]]
    ]
    
    for item_title, sub_items in beverage_items:
        story.append(Paragraph(f"<b>{item_title}</b>", styles['Normal']))
        for sub_item in sub_items:
            story.append(Paragraph(f"• {sub_item}", styles['Normal'], leftIndent=0.2*inch))
        story.append(Spacer(1, 0.1*inch))
    
    story.append(PageBreak())
    
    # Service Planning Section
    story.append(Paragraph("Service Planning (2-3 Weeks Before)", heading_style))
    
    service_items = [
        ["Bartender Staffing", ["Determine number of bartenders needed", "Plan for peak service times", "Consider service style (craft cocktails vs standard)", "Coordinate with event timeline"]],
        ["Bar Setup", ["Determine bar location", "Plan for traffic flow", "Consider multiple bar stations (if large event)", "Plan for backup bar (if needed)"]],
        ["Service Coordination", ["Coordinate with caterer", "Plan service timing", "Coordinate with entertainment", "Plan for cleanup"]]
    ]
    
    for item_title, sub_items in service_items:
        story.append(Paragraph(f"<b>{item_title}</b>", styles['Normal']))
        for sub_item in sub_items:
            story.append(Paragraph(f"• {sub_item}", styles['Normal'], leftIndent=0.2*inch))
        story.append(Spacer(1, 0.1*inch))
    
    story.append(PageBreak())
    
    # Final Details Section
    story.append(Paragraph("Final Details (1-2 Weeks Before)", heading_style))
    
    final_items = [
        ["Confirmations", ["Finalize guest count", "Confirm beverage quantities", "Confirm bartender count", "Review contracts"]],
        ["Special Considerations", ["Dietary restrictions", "Allergies", "Non-alcoholic options", "Signature drink requests"]],
        ["Day-of Preparation", ["Create bar service timeline", "Prepare bar setup checklist", "Plan for emergency backup", "Prepare welcome materials"]]
    ]
    
    for item_title, sub_items in final_items:
        story.append(Paragraph(f"<b>{item_title}</b>", styles['Normal']))
        for sub_item in sub_items:
            story.append(Paragraph(f"• {sub_item}", styles['Normal'], leftIndent=0.2*inch))
        story.append(Spacer(1, 0.1*inch))
    
    story.append(PageBreak())
    
    # Day of Event Section
    story.append(Paragraph("Day of Event", heading_style))
    
    day_items = [
        ["Morning Setup", ["Bar equipment delivery", "Beverage delivery", "Bar setup and arrangement", "Test all equipment"]],
        ["During Event", ["Monitor bar service", "Ensure adequate supply", "Monitor bartender performance", "Address any issues immediately"]],
        ["Service Execution", ["Welcome drink service", "Cocktail hour service", "Dinner service (wine/beer)", "After-dinner service", "Closing service"]]
    ]
    
    for item_title, sub_items in day_items:
        story.append(Paragraph(f"<b>{item_title}</b>", styles['Normal']))
        for sub_item in sub_items:
            story.append(Paragraph(f"• {sub_item}", styles['Normal'], leftIndent=0.2*inch))
        story.append(Spacer(1, 0.1*inch))
    
    story.append(PageBreak())
    
    # Post-Event Section
    story.append(Paragraph("Post-Event (Within 1 Week)", heading_style))
    
    post_items = [
        ["Follow-up", ["Thank bartenders and staff", "Review service quality", "Gather guest feedback"]],
        ["Evaluation", ["What went well?", "What would you change?", "Beverage consumption analysis", "Service quality assessment"]]
    ]
    
    for item_title, sub_items in post_items:
        story.append(Paragraph(f"<b>{item_title}</b>", styles['Normal']))
        for sub_item in sub_items:
            story.append(Paragraph(f"• {sub_item}", styles['Normal'], leftIndent=0.2*inch))
        story.append(Spacer(1, 0.1*inch))
    
    story.append(Spacer(1, 0.3*inch))
    
    # Mobile Bar Service Benefits
    story.append(Paragraph("Mobile Bar Service Benefits", heading_style))
    benefits = [
        "No venue restrictions - bring the bar to you",
        "Craft cocktails and premium service",
        "Professional bartenders",
        "Complete setup and cleanup",
        "Flexible service options"
    ]
    
    for benefit in benefits:
        story.append(Paragraph(f"✓ {benefit}", styles['Normal']))
    
    story.append(Spacer(1, 0.3*inch))
    
    # Call to Action
    story.append(Paragraph("Need Help? We're Here for You", heading_style))
    cta_text = """
    Planning an event bar can be overwhelming. <b>Houston Sip Queen</b> brings luxury mobile bar service 
    directly to your location, eliminating venue restrictions while ensuring every detail is perfect.
    
    <b>Your outcome:</b> Guests rave about the cocktails, you enjoy your event, professional service throughout.
    """
    story.append(Paragraph(cta_text, styles['Normal']))
    story.append(Spacer(1, 0.2*inch))
    story.append(Paragraph("Ready to impress your guests?", styles['Normal']))
    story.append(Paragraph("Request a quote: houstonsipqueen.com/quote", styles['Normal']))
    story.append(Paragraph("Book a consultation: houstonsipqueen.com/book", styles['Normal']))
    
    story.append(Spacer(1, 0.3*inch))
    story.append(Paragraph("This checklist is provided by Houston Sip Queen - Luxury Mobile Bartending Services", 
                          ParagraphStyle('Footer', parent=styles['Normal'], fontSize=10, textColor=colors.grey, alignment=TA_CENTER)))
    
    # Build PDF
    doc.build(story)
    return True

if __name__ == "__main__":
    # Determine output path
    script_dir = Path(__file__).parent
    websites_dir = script_dir.parent / "websites" / "houstonsipqueen.com" / "wp" / "wp-content" / "uploads"
    
    # Create uploads directory if it doesn't exist
    websites_dir.mkdir(parents=True, exist_ok=True)
    
    output_path = websites_dir / "event-bar-planning-checklist.pdf"
    
    print(f"Generating PDF: {output_path}")
    
    if generate_pdf(str(output_path)):
        print(f"✅ PDF generated successfully: {output_path}")
        print(f"   File size: {output_path.stat().st_size / 1024:.2f} KB")
    else:
        print("❌ PDF generation failed")

