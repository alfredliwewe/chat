import glob
import uuid
import os
import re
import sys
import json

def last_folder_and_name(path):
    # Get the directory and the file separately
    directory, file = os.path.split(path)
    # Get the last folder name
    last_folder = os.path.basename(directory)
    # Combine last folder and file
    return os.path.join(last_folder, file)

def file_name(path):
    # Get the directory and the file separately
    directory, file = os.path.split(path)
    return file

if len(sys.argv) < 2:
    print("Usage: python run.py <folder>")
    exit()

folder = sys.argv[1]

pattern = os.path.join(folder, "compiled", "**", "*.js")
files = glob.glob(pattern, recursive=True)
# generate and keep and a random name for each file

file_store = {}
for file in files:
    file_store[file_name(file)] = str(uuid.uuid4())+".js"
#print(files)


#open each file and find a import statement
for file in files:
    with open(file, "r") as f:
        content = f.read()
        
        lines = re.split(r'\r\n|\n|\r', content)
        new_lines = []
        for line in lines:
            if "import" in line:
                # 1. Extract the filename from the import path
                match = re.search(r'from\s+["\'].*?/([^/]+\.js)["\']', line)

                if match:
                    filename = match.group(1)  # e.g., RiskManagement.js

                    # 2. Check if the filename needs to be replaced
                    if filename in file_store:
                        new_filename = file_store[filename]

                        # 3. Replace the filename in the original line
                        line = line.replace(filename, new_filename)
                        #print(line)
                    #else:
                    #print("No replacement needed:", line)
                #else:
                #print("No match found in line.")
            new_lines.append(line)
        #join the lines and write the file
        new_content = "\n".join(new_lines)
        with open(file, "w") as f:
            f.write(new_content)
        # rename file
        filename = file_name(file)
        new_filename = file_store[filename]
        os.rename(file, file.replace(filename, new_filename))

#save index.js filename into index.txt
with open(os.path.join(folder, "compiled", "index.txt"), "w") as f:
    f.write(file_store["index.js"])

#check if mobile.js
if file_store.__contains__("mobile.js"):
    with open(os.path.join(folder, "compiled", "mobile.txt"), "w") as f:
        f.write(file_store["mobile.js"])

#write file_store as json
# indented
with open(os.path.join(folder, "compiled", "file_store.json"), "w") as f:
    json.dump(file_store, f, indent=4)

print("Done")