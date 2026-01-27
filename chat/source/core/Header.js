const { useEffect, useState, useContext, createContext } = React;
import { Context } from "../index.js";
import { formatRelativeTime } from "../utilities.js";

export function Header() {
    const { friend, setFriend } = useContext(Context);

    return (
        <header className="h-20 flex items-center justify-between px-6 border-b border-gray-100 bg-white/80 backdrop-blur-md sticky top-0 z-10">
            <div className="flex items-center">
                <button onclick="toggleSidebar()" className="md:hidden mr-4 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <i className="fas fa-bars text-xl"></i>
                </button>
                <div className="relative">
                    <img
                        src={"../uploads/" + friend.picture}
                        className="w-10 h-10 rounded-full border border-gray-200"
                        onError={e => {
                            e.target.onerror = null;
                            e.target.src = "../uploads/default_avatar.png"
                        }}
                    />
                    <span className="absolute bottom-0 right-0 w-2.5 h-2.5 bg-green-500 border-2 border-white rounded-full"></span>
                </div>
                <div className="ml-4">
                    <h3 className="font-bold text-gray-800 text-lg leading-tight">{friend.name}</h3>
                    <p className="text-xs text-green-500">Active {formatRelativeTime(friend.last_seen)}</p>
                </div>
            </div>
            <div className="flex items-center space-x-6 text-gray-400">
                <button className="hover:text-indigo-600 transition-colors"><i className="fas fa-phone-alt"></i></button>
                <button className="hover:text-indigo-600 transition-colors"><i className="fas fa-video"></i></button>
                <button className="hover:text-indigo-600 transition-colors"><i className="fas fa-info-circle"></i></button>
            </div>
        </header>
    )
}