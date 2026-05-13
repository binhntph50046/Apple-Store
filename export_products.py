import re
from pathlib import Path
import csv

path = Path('applestore.sql')
text = path.read_text(encoding='utf-8', errors='replace')
pattern = re.compile(r"INSERT INTO `products` \(`id`, `name`, `slug`, `description`, `content`, `category_id`, `warranty_months`, `is_featured`, `status`, `views`, `created_at`, `updated_at`, `deleted_at`, `total_sold`\) VALUES\n")


def parse_sql_tuple(inner: str):
    fields = []
    i, n = 0, len(inner)
    while i < n:
        while i < n and inner[i].isspace():
            i += 1
        if i >= n:
            break
        if inner[i] == "'":
            i += 1
            buf = []
            while i < n:
                ch = inner[i]
                if ch == "'":
                    if i + 1 < n and inner[i + 1] == "'":
                        buf.append("'")
                        i += 2
                        continue
                    i += 1
                    break
                buf.append(ch)
                i += 1
            fields.append(''.join(buf))
            while i < n and inner[i].isspace():
                i += 1
            if i < n and inner[i] == ',':
                i += 1
        else:
            start = i
            while i < n and inner[i] != ',':
                i += 1
            token = inner[start:i].strip()
            fields.append(token)
            if i < n and inner[i] == ',':
                i += 1
    return fields

rows = []
for m in pattern.finditer(text):
    start = m.end()
    depth = 0
    in_quote = False
    escape = False
    i = start
    while i < len(text):
        ch = text[i]
        if in_quote:
            if escape:
                escape = False
            elif ch == '\\':
                escape = True
            elif ch == "'":
                if i + 1 < len(text) and text[i + 1] == "'":
                    i += 1
                else:
                    in_quote = False
        else:
            if ch == "'":
                in_quote = True
            elif ch == '(':
                depth += 1
            elif ch == ')':
                if depth > 0:
                    depth -= 1
            elif ch == ';' and depth == 0:
                break
        i += 1
    block = text[start:i]
    depth = 0
    start_idx = None
    for j, ch in enumerate(block):
        if ch == '(' and depth == 0:
            start_idx = j
        if ch == '(':
            depth += 1
        elif ch == ')':
            depth -= 1
            if depth == 0 and start_idx is not None:
                tuple_text = block[start_idx:j+1]
                inner = tuple_text[1:-1]
                row = parse_sql_tuple(inner)
                rows.append(row)
                start_idx = None
out_rows = [r for r in rows if len(r) >= 14]
print('parsed rows', len(out_rows))

out_csv = Path('products_export_fixed.csv')
with out_csv.open('w', newline='', encoding='utf-8') as f:
    writer = csv.writer(f)
    writer.writerow(['id', 'slug', 'title', 'content', 'deleted_at'])
    for r in out_rows:
        writer.writerow([r[0], r[2], r[1], r[4], r[12]])
print('wrote', out_csv)
try:
    import openpyxl
    from openpyxl import Workbook
    wb = Workbook()
    ws = wb.active
    ws.title = 'products'
    ws.append(['id', 'slug', 'title', 'content', 'deleted_at'])
    for r in out_rows:
        ws.append([r[0], r[2], r[1], r[4], r[12]])
    out_xlsx = Path('products_export_fixed.xlsx')
    wb.save(out_xlsx)
    print('wrote', out_xlsx)
except Exception as e:
    print('openpyxl unavailable or failed:', e)
