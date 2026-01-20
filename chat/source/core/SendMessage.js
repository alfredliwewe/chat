const { useEffect, useState, useContext, createContext, useRef } = React;
import { Context } from "../index.js";
import { post } from "../utilities.js";
const {
    Dialog,
    Box,
    Button
} = MaterialUI;

export function SendMessage() {
    const { friend, setFriend } = useContext(Context);
    const [open, setOpen] = useState({
        attach: false
    });

    const onSendMessage = (event) => {
        event.preventDefault();

        let formdata = new FormData(event.target);

        post("api/", formdata, response => {
            try {
                let res = JSON.parse(response);
                if (res.status) {
                    Toast("Message sent");
                    event.target.reset();
                }
                else {
                    Toast(res.message);
                }
            }
            catch (E) {
                alert(E.toString() + response);
            }
        })
    }

    return (
        <>
            <div className="p-4 bg-white border-t border-gray-100">
                <form action="#" onSubmit={onSendMessage} className="flex items-center gap-2">
                    <input type="hidden" name="sendMessage" value={"true"} />
                    <input type="hidden" name="friend_id" value={friend.id} />

                    <button
                        type="button"
                        onClick={() => setOpen({
                            ...open,
                            attach: true
                        })}
                        className="text-gray-400 hover:text-indigo-600 p-3 rounded-full hover:bg-gray-50 transition-colors"
                    >
                        <i className="fas fa-paperclip"></i>
                    </button>
                    <div className="flex-1 relative">
                        <input
                            type="text"
                            placeholder="Type a message..."
                            className="w-full bg-gray-50 text-gray-700 rounded-full py-3.5 pl-5 pr-12 focus:ring-2 focus:ring-indigo-500 focus:bg-white border-transparent focus:border-transparent transition-all outline-none"
                            name="message"
                        />
                        <button type="button" className="absolute right-2 top-1.5 text-gray-400 hover:text-indigo-600 p-2 rounded-full transition-colors">
                            <i className="far fa-smile"></i>
                        </button>
                    </div>
                    <button type="submit" className="bg-indigo-600 text-white p-3.5 rounded-full hover:bg-indigo-700 shadow-lg hover:shadow-indigo-500/30 transform transition-transform hover:scale-105 active:scale-95">
                        <i className="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>

            <Dialog open={open.attach} onClose={() => setOpen({
                ...open,
                attach: false
            })}>
                <Box sx={{ width: 500, p: 2 }}>
                    <FileUploader
                        friend={friend}
                        onSuccess={() => {
                            Toast("Success");
                            getPictures();
                        }}
                    />
                </Box>
            </Dialog>
        </>
    )
}


// ImageUploader component for handling image selection, preview, and upload
function FileUploader({ friend, onSuccess }) {
    // State to store selected files, their preview URLs, and upload progress
    const [selectedFiles, setSelectedFiles] = useState([]);
    const [previews, setPreviews] = useState([]);
    const [uploadProgress, setUploadProgress] = useState({}); // { fileName: progressPercentage }
    const [uploading, setUploading] = useState(false); // To disable input during upload

    // Ref for the file input element
    const fileInputRef = useRef(null);

    // Effect to clean up object URLs when component unmounts or previews change
    useEffect(() => {
        return () => {
            previews.forEach(preview => URL.revokeObjectURL(preview.url));
        };
    }, [previews]);

    /**
     * Handles file selection from the input.
     * Reads each file to create a preview URL and stores file objects.
     * @param {Event} event - The change event from the file input.
     */
    const handleFileChange = (event) => {
        const files = Array.from(event.target.files);
        setSelectedFiles(files);

        const newPreviews = [];
        files.forEach(file => {
            if (file.type.startsWith("image/")) {
                newPreviews.push({
                    name: file.name,
                    url: URL.createObjectURL(file), // Create a URL for image preview
                    file: file, // Store the actual file object
                });
            }

            if (file.name.endsWith(".zip") || file.name.endsWith(".rar")) {
                newPreviews.push({
                    name: file.name,
                    url: "../images/zip.png",
                    file: file, // Store the actual file object
                });
            }
        });
        setPreviews(newPreviews);
        setUploadProgress({}); // Reset progress when new files are selected
        setUploading(false); // Reset uploading state
    };

    /**
     * Simulates an image upload using XMLHttpRequest.
     * Updates the progress for the specific file.
     * @param {File} file - The file to be "uploaded".
     * @returns {Promise<void>} A promise that resolves when the "upload" is complete.
     */
    const uploadImage = (file) => {
        return new Promise((resolve, reject) => {
            // Create a new XMLHttpRequest instance
            const xhr = new XMLHttpRequest();
            // Define a dummy URL for demonstration purposes. In a real app, this would be your API endpoint.
            const uploadUrl = 'api/'; // Dummy API endpoint

            // Listen for progress events during the upload
            xhr.upload.addEventListener("progress", (event) => {
                if (event.lengthComputable) {
                    const percentComplete = Math.round((event.loaded / event.total) * 100);
                    // Update the progress for the specific file
                    setUploadProgress(prev => ({
                        ...prev,
                        [file.name]: percentComplete
                    }));
                }
            });

            // Listen for when the upload is complete
            xhr.addEventListener("load", () => {
                if (xhr.status >= 200 && xhr.status < 300) {
                    console.log(`Upload complete for ${file.name}:`, xhr.responseText);
                    resolve(); // Resolve the promise on successful "upload"
                } else {
                    console.error(`Upload failed for ${file.name}:`, xhr.statusText);
                    reject(new Error(`Upload failed for ${file.name}`)); // Reject on error
                }
            });

            // Listen for errors during the upload
            xhr.addEventListener("error", () => {
                console.error(`Network error during upload for ${file.name}`);
                reject(new Error(`Network error for ${file.name}`));
            });

            // Open the request (POST method) to the dummy URL
            xhr.open("POST", uploadUrl);

            // Create a FormData object and append the file
            const formData = new FormData();
            formData.append("file_attachment", file, file.name); // 'image' is the field name expected by the server
            formData.append("friend_id", friend.id);

            // Send the request with the FormData
            xhr.send(formData);
        });
    };

    /**
     * Handles the overall upload process for all selected images.
     * Uploads images one by one.
     */
    const handleUploadAll = async () => {
        if (selectedFiles.length === 0) {
            console.log("No files selected for upload.");
            return;
        }

        setUploading(true); // Disable input and button during upload

        for (const file of selectedFiles) {
            try {
                // Reset progress for the current file to 0 before starting its upload
                setUploadProgress(prev => ({ ...prev, [file.name]: 0 }));
                await uploadImage(file); // Wait for each image to "upload"
            } catch (error) {
                console.error(`Error uploading ${file.name}:`, error);
                // Optionally, handle individual file upload errors (e.g., show an error message)
            }
        }
        setUploading(false); // Re-enable input and button after all uploads
        setSelectedFiles([]);
        setPreviews([])
        onSuccess();
        console.log("All selected images processed.");
    };

    return (
        <div className="bg-white p-8 rounded-xl w-full border border-gray-200 mt-2">
            <h2 className="text-2xl font-bold text-gray-800 mb-6 text-center">File Uploader</h2>

            {/* File input section */}
            <div className="mb-6">
                <label
                    htmlFor="file-upload"
                    className="block text-sm font-medium text-gray-700 mb-2"
                >
                    Select Files:
                </label>
                <input
                    id="file-upload"
                    type="file"
                    multiple
                    accept="image/*" // Only allow image files
                    onChange={handleFileChange}
                    ref={fileInputRef}
                    disabled={uploading} // Disable input while uploading
                    className="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                />
                {/* Upload button */}
                <button
                    onClick={handleUploadAll}
                    disabled={selectedFiles.length === 0 || uploading} // Disable if no files or already uploading
                    className={`mt-4 w-full py-2 px-4 rounded-lg font-semibold transition duration-300 ease-in-out
			  ${selectedFiles.length === 0 || uploading
                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                            : 'bg-blue-600 hover:bg-blue-700 text-white shadow-md'
                        }`}
                >
                    {uploading ? 'Uploading...' : 'Upload All Files'}
                </button>
            </div>

            {/* Image previews and progress bars */}
            {previews.length > 0 && (
                <div className="mt-6 border-t pt-6 border-gray-200">
                    <h3 className="text-xl font-semibold text-gray-700 mb-4">File Previews</h3>
                    <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        {previews.map((preview, index) => (
                            <div key={index} className="relative group bg-white rounded-lg shadow-sm overflow-hidden border border-gray-200">
                                <img
                                    src={preview.url}
                                    alt={`Preview ${preview.name}`}
                                    className="w-full h-40 object-cover rounded-t-lg"
                                    // Fallback for broken image or if URL is invalid
                                    onError={(e) => {
                                        e.target.onerror = null; // Prevent infinite loop
                                        e.target.src = `https://placehold.co/160x160/cccccc/333333?text=No+Image`;
                                    }}
                                />
                                <div className="p-3">
                                    <p className="text-sm font-medium text-gray-800 truncate mb-2" title={preview.name}>
                                        {preview.name}
                                    </p>
                                    {/* Progress bar */}
                                    <div className="w-full bg-gray-200 rounded-full h-2.5">
                                        <div
                                            className="bg-blue-500 h-2.5 rounded-full transition-all duration-300 ease-out"
                                            style={{ width: `${uploadProgress[preview.name] || 0}%` }}
                                        ></div>
                                    </div>
                                    <p className="text-xs text-gray-500 mt-1 text-right">
                                        {uploadProgress[preview.name] || 0}%
                                    </p>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            )}
        </div>
    );
}