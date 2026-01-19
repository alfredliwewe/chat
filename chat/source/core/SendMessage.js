const { useEffect, useState, useContext, createContext } = React;
import { Context } from "../index.js";
import { post } from "../utilities.js";

export function SendMessage() {
    const { friend, setFriend } = useContext(Context);

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
        <div className="p-4 bg-white border-t border-gray-100">
            <form action="#" onSubmit={onSendMessage} className="flex items-center gap-2">
                <input type="hidden" name="sendMessage" value={"true"} />
                <input type="hidden" name="friend_id" value={friend.id} />

                <button type="button" className="text-gray-400 hover:text-indigo-600 p-3 rounded-full hover:bg-gray-50 transition-colors">
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
    )
}
