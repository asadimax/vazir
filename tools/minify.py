#!/usr/bin/env python3
"""style.css را فشرده می‌کند و assets/css/style.min.css می‌سازد.
اجرا:  python3 tools/minify.py   (بعد از هر تغییر در style.css)"""
import re, pathlib
root = pathlib.Path(__file__).resolve().parent.parent
src = (root / 'style.css').read_text(encoding='utf-8')
src = re.sub(r'/\*.*?\*/', '', src, flags=re.S)
parts = re.split(r'("(?:[^"\\]|\\.)*"|\'(?:[^\'\\]|\\.)*\')', src)
out = []
for i, p in enumerate(parts):
    if i % 2:
        out.append(p); continue
    p = re.sub(r'\s+', ' ', p)
    p = re.sub(r' ?([{};,>]) ?', r'\1', p)
    p = re.sub(r': ', ':', p)
    out.append(p)
css = ''.join(out).replace(';}', '}').strip()
css = css.replace('url("assets/', 'url("{{URI}}/assets/')
d = root / 'assets' / 'css'; d.mkdir(parents=True, exist_ok=True)
(d / 'style.min.css').write_text(css, encoding='utf-8')
print(len(src.encode()), '->', len(css.encode()), 'bytes')
