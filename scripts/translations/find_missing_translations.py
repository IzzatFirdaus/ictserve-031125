# I will write a script to find missing translation keys.
# I will read all the files from lang/en and lang/ms, parse them as PHP arrays, and then compare the keys.
# I will then print the missing keys for each language.

import os
import re

def get_keys_from_file(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # This is a simplified parser. It might not work for all PHP array syntax.
    # It should be good enough for Laravel translation files.
    keys = re.findall(r"'(.*?)'\s*=>", content)
    return set(keys)

def get_all_keys(directory):
    all_keys = {}
    for root, _, files in os.walk(directory):
        for file in files:
            if file.endswith('.php'):
                file_path = os.path.join(root, file)
                file_name = os.path.splitext(file)[0]
                keys = get_keys_from_file(file_path)
                all_keys[file_name] = keys
    return all_keys

en_keys = get_all_keys('c:/XAMPP/htdocs/ictserve-031125/lang/en')
ms_keys = get_all_keys('c:/XAMPP/htdocs/ictserve-031125/lang/ms')

print("--- Missing keys in 'ms' ---")
for file_name, keys in en_keys.items():
    if file_name in ms_keys:
        missing_keys = keys - ms_keys[file_name]
        if missing_keys:
            print(f"File: {file_name}.php")
            for key in missing_keys:
                print(f"  - {key}")
    else:
        print(f"File: {file_name}.php (missing file)")

print("\n--- Missing keys in 'en' ---")
for file_name, keys in ms_keys.items():
    if file_name in en_keys:
        missing_keys = keys - en_keys[file_name]
        if missing_keys:
            print(f"File: {file_name}.php")
            for key in missing_keys:
                print(f"  - {key}")
    else:
        print(f"File: {file_name}.php (missing file)")
