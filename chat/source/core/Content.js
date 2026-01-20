import { Context } from "../index.js";
const { useContext, useState, useEffect } = React;

export function Content() {
    const { friend } = useContext(Context);
    const [messages, setMessages] = useState([]);

    const getMessages = () => {
        $.get("api/", { getMessages: friend.id }, res => {
            if (!Array.isArray(res)) {
                Toast("Failed to get messages");
                console.log(res);
                return;
            }
            setMessages(res);
            //Toast(res.length + " messages");
        })
    }

    useEffect(() => {
        getMessages();

        const interval = setInterval(() => {
            getMessages();
        }, 2000);

        return () => clearInterval(interval);
    }, [friend]);
    return (
        <>
            <div className="flex-1 overflow-y-auto p-6 space-y-6 bg-gray-50" id="chatContainer">
                {messages.map((message, index) => (
                    <div key={message.id}>
                        {message.type === "date" ? (
                            <div className="flex justify-center mb-4">
                                <span className="text-xs text-gray-400 bg-gray-200 px-3 py-1 rounded-full">Today</span>
                            </div>
                        ) : <>
                            {message.sender_type === "me" ? (
                                <div className="flex items-end justify-end">
                                    <div className="message-bubble bg-indigo-600 text-white p-4 rounded-2xl rounded-br-sm shadow-md">
                                        <p className="text-sm">{message.message}</p>
                                        <div className="flex justify-end items-center gap-1 mt-1">
                                            <span className="text-[10px] text-indigo-200">{message.ago}</span>
                                            <i className="fas fa-check-double text-[10px] text-indigo-200"></i>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className="flex items-end">
                                    <img
                                        src={"../uploads/" + message.user_data.picture}
                                        className="w-8 h-8 rounded-full mb-1 mr-2 invisible md:visible"
                                        onError={e => {
                                            e.target.onerror = null;
                                            e.target.src = "../uploads/default_avatar.png"
                                        }}
                                    />
                                    <div className="message-bubble bg-white text-gray-700 p-4 rounded-2xl rounded-bl-sm shadow-sm border border-gray-100">
                                        <p className="text-sm">{message.message}</p>
                                        <span className="text-[10px] text-gray-400 block mt-1 text-right">{message.ago}</span>
                                    </div>
                                </div>)
                            }
                        </>}
                    </div>
                ))}
            </div >
        </>
    )
}