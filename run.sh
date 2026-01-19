clear
echo "Enter folder name to compile"
#read folder
folder="chat"
# clear folder
rm -rf $folder/compiled

echo "Compiling JSX..."
npx babel $folder/source --out-dir $folder/compiled --presets=@babel/preset-react
#echo "Student JSX compiled successfully"
python3 run.py $folder