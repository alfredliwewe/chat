clear
echo "Enter folder name to compile"
#read folder
folder="chat"
# delete folders that start with compiled
rm -rf $folder/compiled*

#generate a unique folder name
timestamp=$(date +%s)

echo "Compiling JSX..."
npx babel $folder/source --out-dir $folder/compiled-$timestamp --presets=@babel/preset-react
#echo "Student JSX compiled successfully"
#python3 run.py $folder
