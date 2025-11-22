import os
import re
pattern = re.compile(r"(?:__|trans|Lang::get)\(\s*['\"]filament\.([\w_\.]+)['\"]")
keys = set()
dirs = ['app/Filament', 'resources/views']
for d in dirs:
    for root, _, files in os.walk(d):
        for name in files:
            if not name.endswith(('.php', '.blade.php', '.volt')):
                continue
            path = os.path.join(root, name)
            try:
                data = open(path, encoding='utf-8').read()
            except Exception:
                continue
            for m in pattern.finditer(data):
                keys.add(m.group(1))
print('\n'.join(sorted(keys)))
print('count', len(keys))
