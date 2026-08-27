import os
import sys
from reportlab.lib.pagesizes import letter
from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.pdfgen import canvas

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_header_footer(num_pages)
            super().showPage()
        super().save()

    def draw_header_footer(self, page_count):
        if self._pageNumber == 1:
            return  # Skip cover page

        self.saveState()
        self.setFont("Helvetica-Bold", 8)
        self.setFillColor(colors.HexColor("#64748b"))

        # Header
        self.drawString(54, 11 * 72 - 36, "CHURCH ADMIN PORTAL  |  FINANCIAL & ASSET MANAGEMENT MANUAL")
        self.setStrokeColor(colors.HexColor("#e2e8f0"))
        self.setLineWidth(0.75)
        self.line(54, 11 * 72 - 42, 8.5 * 72 - 54, 11 * 72 - 42)

        # Footer
        self.line(54, 48, 8.5 * 72 - 54, 48)
        self.setFont("Helvetica", 8)
        self.drawString(54, 34, "Confidential — Life Changers Touch Church Management System")
        page_text = f"Page {self._pageNumber} of {page_count}"
        self.drawRightString(8.5 * 72 - 54, 34, page_text)
        self.restoreState()


def build_pdf(filename):
    doc = SimpleDocTemplate(
        filename,
        pagesize=letter,
        leftMargin=54,
        rightMargin=54,
        topMargin=54,
        bottomMargin=54
    )

    styles = getSampleStyleSheet()

    # Custom styles
    primary_color = colors.HexColor("#1e3a8a")     # Deep Blue
    secondary_color = colors.HexColor("#0284c7")   # Sky Blue
    accent_emerald = colors.HexColor("#059669")    # Emerald
    dark_text = colors.HexColor("#0f172a")         # Slate Dark
    muted_text = colors.HexColor("#475569")        # Slate Muted

    cover_title_style = ParagraphStyle(
        'CoverTitle',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=26,
        leading=32,
        textColor=primary_color,
        spaceAfter=12
    )

    cover_subtitle_style = ParagraphStyle(
        'CoverSubtitle',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=13,
        leading=18,
        textColor=muted_text,
        spaceAfter=24
    )

    h1_style = ParagraphStyle(
        'Heading1_Custom',
        parent=styles['Heading1'],
        fontName='Helvetica-Bold',
        fontSize=16,
        leading=20,
        textColor=primary_color,
        spaceBefore=16,
        spaceAfter=8,
        keepWithNext=True
    )

    h2_style = ParagraphStyle(
        'Heading2_Custom',
        parent=styles['Heading2'],
        fontName='Helvetica-Bold',
        fontSize=12,
        leading=16,
        textColor=secondary_color,
        spaceBefore=12,
        spaceAfter=6,
        keepWithNext=True
    )

    body_style = ParagraphStyle(
        'Body_Custom',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9.5,
        leading=14,
        textColor=dark_text,
        spaceAfter=8
    )

    bullet_style = ParagraphStyle(
        'Bullet_Custom',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9,
        leading=13.5,
        textColor=dark_text,
        leftIndent=15,
        firstLineIndent=-10,
        spaceAfter=4
    )

    callout_style = ParagraphStyle(
        'CalloutText',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=9,
        leading=13,
        textColor=colors.HexColor("#1e293b")
    )

    table_header_style = ParagraphStyle(
        'TableHeader',
        parent=styles['Normal'],
        fontName='Helvetica-Bold',
        fontSize=8.5,
        leading=11,
        textColor=colors.white
    )

    table_cell_style = ParagraphStyle(
        'TableCell',
        parent=styles['Normal'],
        fontName='Helvetica',
        fontSize=8,
        leading=11,
        textColor=dark_text
    )

    story = []

    # ==================== COVER PAGE ====================
    story.append(Spacer(1, 40))
    story.append(Paragraph("LIFE CHANGERS TOUCH INTERNATIONAL", ParagraphStyle('SubHeader', fontName='Helvetica-Bold', fontSize=10, textColor=accent_emerald, spaceAfter=8)))
    story.append(Paragraph("Church Financial & Asset Management System", cover_title_style))
    story.append(Paragraph("Complete Operational Manual, Architecture Breakdown & Step-by-Step Operator Guide", cover_subtitle_style))
    
    story.append(HRFlowable(width="100%", thickness=2, color=primary_color, spaceAfter=25))

    # Meta box
    meta_data = [
        [Paragraph("<b>Document Version:</b>", body_style), Paragraph("v2.4 (Enterprise Edition)", body_style)],
        [Paragraph("<b>Target Currency:</b>", body_style), Paragraph("Nigerian Naira (₦)", body_style)],
        [Paragraph("<b>Target Audience:</b>", body_style), Paragraph("Super Admins, Head Pastors, Treasury, Financial Secretaries, Unit Leaders", body_style)],
        [Paragraph("<b>Deployment Scope:</b>", body_style), Paragraph("Multi-Branch Consolidated & Single Church Administration", body_style)],
        [Paragraph("<b>Date Published:</b>", body_style), Paragraph("August 2026", body_style)],
    ]
    meta_table = Table(meta_data, colWidths=[130, 374])
    meta_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#f8fafc")),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#e2e8f0")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#f1f5f9")),
        ('PADDING', (0,0), (-1,-1), 8),
    ]))
    story.append(meta_table)

    story.append(Spacer(1, 30))

    # Executive Overview Callout
    exec_summary = (
        "<b>Executive Summary:</b><br/>"
        "The Church Financial & Asset Management System provides institutional-grade governance, multi-branch treasury visibility, "
        "and automated accounting workflows. It empowers church leadership to monitor income streams (tithes, offerings, donations), "
        "control departmental budget allocations, fulfill capital pledge campaigns, track cashflow trajectories, audit all financial events, "
        "and manage physical property assets across all church locations with zero manual reconciliation errors."
    )
    callout_table = Table([[Paragraph(exec_summary, callout_style)]], colWidths=[504])
    callout_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#eef2ff")),
        ('BOX', (0,0), (-1,-1), 1.5, colors.HexColor("#4f46e5")),
        ('PADDING', (0,0), (-1,-1), 12),
    ]))
    story.append(callout_table)

    story.append(PageBreak())

    # ==================== TABLE OF CONTENTS & ARCHITECTURE ====================
    story.append(Paragraph("1. System Architecture & Module Map", h1_style))
    story.append(Paragraph(
        "The financial module is partitioned into 7 core pillars, each designed to manage a specific layer of church financial stewardship:",
        body_style
    ))

    modules_data = [
        [Paragraph("<b>Module</b>", table_header_style), Paragraph("<b>Route / URL</b>", table_header_style), Paragraph("<b>Primary Function & Strategic Value</b>", table_header_style)],
        [
            Paragraph("<b>Financial Dashboard</b>", table_cell_style),
            Paragraph("<code>/finance</code>", table_cell_style),
            Paragraph("Consolidated treasury overview, 4 KPI cards, 6-month area trend chart, branch rankings, and instant transaction recording.", table_cell_style)
        ],
        [
            Paragraph("<b>Budget Management</b>", table_cell_style),
            Paragraph("<code>/budgets</code>", table_cell_style),
            Paragraph("Annual fiscal year budgets, department/unit spending caps, real-time burn rates, and health alert thresholds (Caution/Exceeded).", table_cell_style)
        ],
        [
            Paragraph("<b>Pledges & Campaigns</b>", table_cell_style),
            Paragraph("<code>/pledges</code>", table_cell_style),
            Paragraph("Capital building funds, mission pledges, donor commitment tracking, installment payment logging, and automated receipt generation.", table_cell_style)
        ],
        [
            Paragraph("<b>Cashflow & Trends</b>", table_cell_style),
            Paragraph("<code>/finance/cashflow</code>", table_cell_style),
            Paragraph("12-month structured cashflow matrix, dual column/line ApexCharts, operating inflow margin, and Year-over-Year (YoY) performance.", table_cell_style)
        ],
        [
            Paragraph("<b>Financial Audit Trail</b>", table_cell_style),
            Paragraph("<code>/finance/audit-trail</code>", table_cell_style),
            Paragraph("Immutable activity ledger capturing every transaction created, updated, or deleted with operator ID, timestamp, and IP address.", table_cell_style)
        ],
        [
            Paragraph("<b>Church Properties</b>", table_cell_style),
            Paragraph("<code>/properties</code>", table_cell_style),
            Paragraph("Asset inventory tracking (instruments, media, vehicles, furniture), serial numbers, custodian assignments, and maintenance status.", table_cell_style)
        ],
        [
            Paragraph("<b>Property Categories</b>", table_cell_style),
            Paragraph("<code>/property-categories</code>", table_cell_style),
            Paragraph("Asset classification groups with 1-click church templates (Audio, Media, Vehicles, Sanctuary Furniture, Generators, IT).", table_cell_style)
        ],
    ]
    t_modules = Table(modules_data, colWidths=[110, 110, 284])
    t_modules.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('ALIGN', (0,0), (-1,-1), 'LEFT'),
        ('VALIGN', (0,0), (-1,-1), 'TOP'),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#cbd5e1")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#e2e8f0")),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, colors.HexColor("#f8fafc")]),
        ('PADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(t_modules)

    story.append(Spacer(1, 14))

    # ==================== SECTION 2 ====================
    story.append(Paragraph("2. Financial Dashboard & Treasury Analytics", h1_style))
    story.append(Paragraph(
        "The Financial Dashboard (accessible via <code>/finance</code>) serves as the primary nerve center for executive decision-makers. "
        "When accessed by a Super Admin, it renders the <b>Global Treasury Consolidated Dashboard</b>; when accessed by a Branch Pastor or Head Pastor, "
        "it automatically scopes down to their specific branch.",
        body_style
    ))

    story.append(Paragraph("Key Components & KPI Metric Cards:", h2_style))
    story.append(Paragraph("• <b>Global Total Inflow (₦):</b> Aggregates all verified revenue sources (tithes, offerings, thanksgiving, seed faith, pledge redemptions, donations). Displays total transaction volume and positive inflow trend badges.", bullet_style))
    story.append(Paragraph("• <b>Global Total Outflow (₦):</b> Tracks all operating expenditures, honorariums, utility bills, maintenance disbursements, and administrative overheads.", bullet_style))
    story.append(Paragraph("• <b>Net Treasury Balance (₦):</b> Calculates <code>Net Balance = Total Inflow - Total Outflow</code>. Color-codes automatically: Emerald (Surplus) vs Rose (Deficit).", bullet_style))
    story.append(Paragraph("• <b>Budgets & Pledges Health:</b> Displays the percentage of overall fiscal budget consumed and the active redemption rate of open capital pledges.", bullet_style))

    story.append(Spacer(1, 10))

    # Step by Step Table for Recording Transactions
    story.append(Paragraph("Step-by-Step: Recording an Inflow or Outflow Transaction", h2_style))
    steps_tx = [
        [Paragraph("<b>Step</b>", table_header_style), Paragraph("<b>Action</b>", table_header_style), Paragraph("<b>Details & Validation Rules</b>", table_header_style)],
        [Paragraph("<b>1</b>", table_cell_style), Paragraph("Open Record Modal", table_cell_style), Paragraph("Click the <b>+ Record Transaction</b> button on the dashboard header.", table_cell_style)],
        [Paragraph("<b>2</b>", table_cell_style), Paragraph("Select Type", table_cell_style), Paragraph("Choose <b>Income (Inflow)</b> or <b>Expense (Outflow)</b>.", table_cell_style)],
        [Paragraph("<b>3</b>", table_cell_style), Paragraph("Enter Amount & Date", table_cell_style), Paragraph("Input the amount in Nigerian Naira (₦). Date defaults to today.", table_cell_style)],
        [Paragraph("<b>4</b>", table_cell_style), Paragraph("Specify Classification", table_cell_style), Paragraph("Select Category (e.g. Tithes, Offering, Maintenance, Honorarium) and Unit/Department.", table_cell_style)],
        [Paragraph("<b>5</b>", table_cell_style), Paragraph("Payment Method & Ref", table_cell_style), Paragraph("Choose Bank Transfer, POS/Card, Cash, Online Gateway, or Cheque. Add Ref ID.", table_cell_style)],
        [Paragraph("<b>6</b>", table_cell_style), Paragraph("Confirm & Save", table_cell_style), Paragraph("Submitting instantly updates the Treasury balance, charts, and creates an audit log entry.", table_cell_style)],
    ]
    t_steps_tx = Table(steps_tx, colWidths=[35, 125, 344])
    t_steps_tx.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), secondary_color),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#cbd5e1")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#e2e8f0")),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, colors.HexColor("#f8fafc")]),
        ('PADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(t_steps_tx)

    story.append(PageBreak())

    # ==================== SECTION 3 ====================
    story.append(Paragraph("3. Budget Management & Fiscal Control", h1_style))
    story.append(Paragraph(
        "The Budget Management engine (accessible via <code>/budgets</code>) enforces strict financial discipline across departments, units, and church branches. "
        "It prevents unapproved expenditure overruns through continuous threshold monitoring.",
        body_style
    ))

    story.append(Paragraph("Budget Lifecycle & Health Status Thresholds:", h2_style))
    
    budget_health_data = [
        [Paragraph("<b>Health State</b>", table_header_style), Paragraph("<b>Burn Rate (% Utilized)</b>", table_header_style), Paragraph("<b>Visual Badge</b>", table_header_style), Paragraph("<b>System Behavior & Action Required</b>", table_header_style)],
        [
            Paragraph("<b>On Track</b>", table_cell_style),
            Paragraph("0% – 74.9%", table_cell_style),
            Paragraph("<font color='#059669'><b>Green Pill</b></font>", table_cell_style),
            Paragraph("Spending is well within budget limits. Normal disbursements allowed.", table_cell_style)
        ],
        [
            Paragraph("<b>Caution</b>", table_cell_style),
            Paragraph("75.0% – 89.9%", table_cell_style),
            Paragraph("<font color='#d97706'><b>Amber Pill</b></font>", table_cell_style),
            Paragraph("Approaching allocation ceiling. Notification displayed to review upcoming unit expenditures.", table_cell_style)
        ],
        [
            Paragraph("<b>Exceeded</b>", table_cell_style),
            Paragraph("90.0% – 100%+", table_cell_style),
            Paragraph("<font color='#dc2626'><b>Red Alert Pill</b></font>", table_cell_style),
            Paragraph("Budget exhausted or breached. Requires supplementary budget approval by Treasury / Pastor.", table_cell_style)
        ],
    ]
    t_budget_health = Table(budget_health_data, colWidths=[75, 105, 85, 239])
    t_budget_health.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#cbd5e1")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#e2e8f0")),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, colors.HexColor("#f8fafc")]),
        ('PADDING', (0,0), (-1,-1), 6),
    ]))
    story.append(t_budget_health)

    story.append(Spacer(1, 14))

    # ==================== SECTION 4 ====================
    story.append(Paragraph("4. Pledges, Donations & Capital Campaigns", h1_style))
    story.append(Paragraph(
        "Capital projects (e.g. sanctuary acquisition, church bus purchase, evangelism crusades) rely on structured pledge redemptions. "
        "The Pledge Management module (<code>/pledges</code>) bridges the gap between verbal commitments and actual bank inflows.",
        body_style
    ))

    story.append(Paragraph("Pledge Management Capabilities:", h2_style))
    story.append(Paragraph("• <b>Dual Donor Modes:</b> Supports commitments linked directly to registered church members (with phone/email auto-fill) or guest/partner donors.", bullet_style))
    story.append(Paragraph("• <b>Installment Tracking:</b> Members can pay down large commitments in flexible installments (Weekly, Monthly, One-off, or Flexible).", bullet_style))
    story.append(Paragraph("• <b>1-Click Redemption Modal:</b> Clicking <b>Pay</b> on any pledge row opens a quick payment modal pre-filled with the outstanding balance.", bullet_style))
    story.append(Paragraph("• <b>Overdue & Deadline Alerts:</b> Pledges that exceed their target due date are flagged with high-visibility <b>Overdue</b> badges.", bullet_style))
    story.append(Paragraph("• <b>Fulfillment Metrics:</b> Live calculation of total redeemed funds vs target campaign goals.", bullet_style))

    story.append(Spacer(1, 14))

    # ==================== SECTION 5 ====================
    story.append(Paragraph("5. Cashflow Statement & Year-over-Year (YoY) Analytics", h1_style))
    story.append(Paragraph(
        "Located at <code>/finance/cashflow</code>, this module provides an institutional 12-Month Financial Cashflow Statement conforming to standard accounting practices:",
        body_style
    ))

    story.append(Paragraph("• <b>Monthly Inflow Line:</b> Monthly breakdown of total operating revenues.", bullet_style))
    story.append(Paragraph("• <b>Monthly Outflow Line:</b> Monthly breakdown of operational expenses.", bullet_style))
    story.append(Paragraph("• <b>Net Monthly Cashflow:</b> Highlights surplus months in green and deficit months in red.", bullet_style))
    story.append(Paragraph("• <b>Cumulative YTD Balance:</b> Tracks treasury growth trajectory month-by-month throughout the fiscal year.", bullet_style))
    story.append(Paragraph("• <b>Operating Inflow Margin (%):</b> Calculates the percentage of total income retained as surplus after all expenses.", bullet_style))
    story.append(Paragraph("• <b>Year-over-Year (YoY) Benchmark:</b> Compares the current fiscal year directly against the previous year to measure annual income growth rate (%), expense growth rate (%), and net surplus change (%).", bullet_style))

    story.append(PageBreak())

    # ==================== SECTION 6 ====================
    story.append(Paragraph("6. Church Properties, Fixed Assets & Inventory", h1_style))
    story.append(Paragraph(
        "The Property and Asset Management system (<code>/properties</code> and <code>/property-categories</code>) ensures total accountability "
        "for the church's capital equipment, electronics, instruments, and physical assets across all locations.",
        body_style
    ))

    story.append(Paragraph("Asset Classification & Categories:", h2_style))
    story.append(Paragraph(
        "Categories structure assets into recognizable departments with automated template chips:",
        body_style
    ))

    prop_cat_data = [
        [Paragraph("<b>Category</b>", table_header_style), Paragraph("<b>Included Items & Equipment</b>", table_header_style), Paragraph("<b>Custodians / Units</b>", table_header_style)],
        [
            Paragraph("<b>Musical Instruments & Audio</b>", table_cell_style),
            Paragraph("Keyboards, digital drum kits, mixers, stage amplifiers, wireless mics, speakers.", table_cell_style),
            Paragraph("Music Ministry / Choir", table_cell_style)
        ],
        [
            Paragraph("<b>Media, Video & Lighting</b>", table_cell_style),
            Paragraph("4K PTZ cameras, livestream switchers, LED stage lights, monitors, projectors.", table_cell_style),
            Paragraph("Media & Technical Team", table_cell_style)
        ],
        [
            Paragraph("<b>Church Vehicles</b>", table_cell_style),
            Paragraph("Church buses, outreach vans, protocol vehicles, utility pickups.", table_cell_style),
            Paragraph("Transport / Logistics", table_cell_style)
        ],
        [
            Paragraph("<b>Sanctuary Furniture</b>", table_cell_style),
            Paragraph("Pulpits, altar chairs, congregational seating, banquet tables, communion sets.", table_cell_style),
            Paragraph("Ushering & Protocol", table_cell_style)
        ],
        [
            Paragraph("<b>Power & Generators</b>", table_cell_style),
            Paragraph("Heavy-duty diesel generators, solar hybrid inverters, industrial stabilizers.", table_cell_style),
            Paragraph("Facility Maintenance", table_cell_style)
        ],
        [
            Paragraph("<b>Office & IT Equipment</b>", table_cell_style),
            Paragraph("Admin laptops, desktop workstations, biometric attendance terminals, network routers.", table_cell_style),
            Paragraph("Administration / ICT", table_cell_style)
        ],
    ]
    t_prop_cat = Table(prop_cat_data, colWidths=[120, 244, 140])
    t_prop_cat.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), primary_color),
        ('BOX', (0,0), (-1,-1), 1, colors.HexColor("#cbd5e1")),
        ('INNERGRID', (0,0), (-1,-1), 0.5, colors.HexColor("#e2e8f0")),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [colors.white, colors.HexColor("#f8fafc")]),
        ('PADDING', (0,0), (-1,-1), 5),
    ]))
    story.append(t_prop_cat)

    story.append(Spacer(1, 10))

    story.append(Paragraph("Asset Lifecycle Statuses:", h2_style))
    story.append(Paragraph("• <b>Available (Green):</b> Fully operational and ready for use in sanctuary or office.", bullet_style))
    story.append(Paragraph("• <b>In Use (Blue):</b> Assigned to a specific pastor, leader, or department.", bullet_style))
    story.append(Paragraph("• <b>Under Maintenance (Amber):</b> Currently undergoing repairs or routine servicing.", bullet_style))
    story.append(Paragraph("• <b>Damaged (Red):</b> Defective and awaiting technician assessment.", bullet_style))
    story.append(Paragraph("• <b>Disposed / Lost:</b> Retired from inventory with audit trail reasoning.", bullet_style))

    story.append(Spacer(1, 14))

    # ==================== SECTION 7 ====================
    story.append(Paragraph("7. Financial Audit Trail & Regulatory Compliance", h1_style))
    story.append(Paragraph(
        "Located at <code>/finance/audit-trail</code>, the Audit Trail records an immutable, tamper-proof record of every financial transaction, "
        "budget alteration, pledge redemption, and property movement. Each log entry stores the exact timestamp (down to the second), "
        "the operator's full name and role, the action taken (Created, Updated, Deleted, Payment), and the IP address of the client device.",
        body_style
    ))

    story.append(Spacer(1, 15))

    # Summary Callout
    conclusion_text = (
        "<b>Governance Best Practice:</b><br/>"
        "By enforcing mandatory role separation between data entry (Financial Secretaries), review (Treasury Committee), and final sign-off (Head Pastor / Super Admin), "
        "the portal eliminates financial discrepancies, builds congregational trust, and guarantees full compliance with non-profit accounting standards."
    )
    conclusion_table = Table([[Paragraph(conclusion_text, callout_style)]], colWidths=[504])
    conclusion_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor("#ecfdf5")),
        ('BOX', (0,0), (-1,-1), 1.5, colors.HexColor("#10b981")),
        ('PADDING', (0,0), (-1,-1), 12),
    ]))
    story.append(conclusion_table)

    # Build Document
    doc.build(story, canvasmaker=NumberedCanvas)
    print(f"Successfully generated PDF at: {filename}")


if __name__ == '__main__':
    output_pdf = r"c:\xampp\htdocs\ADMIN_PORTAL\public\Church_Financial_and_Asset_Management_Guide.pdf"
    build_pdf(output_pdf)
