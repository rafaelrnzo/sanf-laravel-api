<?php

namespace Sanf\Core\Modules\LlmOcr\Enums;

enum DocumentTypeEnum: string
{
    case INVOICE = 'invoice';
    case PURCHASE_ORDER = 'purchase_order';

    public function extractionPrompt(): string
    {
        return match ($this) {
            self::INVOICE => <<<'PROMPT'
Extract the following fields from this invoice document. Return ONLY a JSON object with these exact keys:
{
  "invoice_number": "the invoice number/ID",
  "invoice_date": "invoice date in YYYY-MM-DD format",
  "due_date": "payment due date in YYYY-MM-DD format, or null if not found",
  "subtotal": 0,
  "pph23": 0,
  "backcharge": 0,
  "other_amount": 0,
  "grand_total": 0
}

Rules:
- All monetary values must be numbers (not strings), without currency symbols or separators.
- If a field is not found in the document, use 0 for numbers and empty string for text.
- grand_total = subtotal - pph23 - backcharge + other_amount
- Do NOT include PPN/VAT in the output.
- Dates must be in YYYY-MM-DD format. If the date format is ambiguous, prefer DD-MM-YYYY interpretation.
- Return ONLY the JSON object, no markdown, no explanation.
PROMPT,
            self::PURCHASE_ORDER => <<<'PROMPT'
Extract the following fields from this purchase order document. Return ONLY a JSON object with these exact keys:
{
  "po_number": "the purchase order number/ID",
  "po_date": "PO date in YYYY-MM-DD format",
  "subtotal": 0,
  "pph23": 0,
  "backcharge": 0,
  "other_amount": 0,
  "grand_total": 0
}

Rules:
- All monetary values must be numbers (not strings), without currency symbols or separators.
- If a field is not found in the document, use 0 for numbers and empty string for text.
- grand_total = subtotal - pph23 - backcharge + other_amount
- Do NOT include PPN/VAT in the output.
- Dates must be in YYYY-MM-DD format. If the date format is ambiguous, prefer DD-MM-YYYY interpretation.
- Return ONLY the JSON object, no markdown, no explanation.
PROMPT,
        };
    }

    public function extractionPromptMultiple(): string
    {
        return match ($this) {
            self::INVOICE => <<<'PROMPT'
Extract ALL invoices from this document. Return a JSON array of objects with these exact keys:
[
  {
    "invoice_number": "the invoice number/ID",
    "invoice_date": "invoice date in YYYY-MM-DD format",
    "due_date": "payment due date in YYYY-MM-DD format, or null if not found",
    "subtotal": 0,
    "pph23": 0,
    "backcharge": 0,
    "other_amount": 0,
    "grand_total": 0
  }
]

Rules:
- Return a JSON array, even if only ONE invoice is found.
- All monetary values must be numbers (not strings), without currency symbols or separators.
- If a field is not found in the document, use 0 for numbers and empty string for text.
- grand_total = subtotal - pph23 - backcharge + other_amount
- Do NOT include PPN/VAT in the output.
- Dates must be in YYYY-MM-DD format. If the date format is ambiguous, prefer DD-MM-YYYY interpretation.
- Return ONLY the JSON array, no markdown, no explanation.
PROMPT,
            self::PURCHASE_ORDER => <<<'PROMPT'
Extract ALL purchase orders from this document. Return a JSON array of objects with these exact keys:
[
  {
    "po_number": "the purchase order number/ID",
    "po_date": "PO date in YYYY-MM-DD format",
    "subtotal": 0,
    "pph23": 0,
    "backcharge": 0,
    "other_amount": 0,
    "grand_total": 0
  }
]

Rules:
- Return a JSON array, even if only ONE purchase order is found.
- All monetary values must be numbers (not strings), without currency symbols or separators.
- If a field is not found in the document, use 0 for numbers and empty string for text.
- grand_total = subtotal - pph23 - backcharge + other_amount
- Do NOT include PPN/VAT in the output.
- Dates must be in YYYY-MM-DD format. If the date format is ambiguous, prefer DD-MM-YYYY interpretation.
- Return ONLY the JSON array, no markdown, no explanation.
PROMPT,
        };
    }

    public function requiredFields(): array
    {
        return match ($this) {
            self::INVOICE => ['invoice_number', 'invoice_date', 'subtotal', 'grand_total'],
            self::PURCHASE_ORDER => ['po_number', 'po_date', 'subtotal', 'grand_total'],
        };
    }
}
