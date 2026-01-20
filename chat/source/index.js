const { useEffect, useState, useContext, createContext } = React;
import { Header } from "./core/Header.js";
import { SendMessage } from "./core/SendMessage.js";
import { Content } from "./core/Content.js";
import { AccountSettingsDialog } from "./core/AccountSetting.js";

export const Context = createContext({});

function Index() {
    const [sidebarOpen, setSidebarOpen] = useState(false);
    const [chatHeads, setChatHeads] = useState([
        {
            id: 1,
            name: "Alice Smith",
            status: "Online",
            lastMessage: "Are we still meeting later?",
            time: "10:42 AM",
            user_data: {
                id: 1,
                picture: "default_avatar.png",
                name: "Alice Smith"
            }
        },
        {
            id: 2,
            name: "Bob Johnson",
            status: "Offline",
            lastMessage: "Thanks for the files!",
            time: "Yesterday",
            user_data: {
                id: 2,
                picture: "default_avatar.png",
                name: "Bob Johnson"
            }
        },
        {
            id: 3,
            name: "Charlie Day",
            status: "Online",
            lastMessage: "Can you check the email?",
            time: "Mon",
            user_data: {
                id: 3,
                picture: "default_avatar.png",
                name: "Charlie Day"
            }
        },
        {
            id: 4,
            name: "David Wilson",
            status: "Offline",
            lastMessage: "Can you check the email?",
            time: "Mon",
            user_data: {
                id: 4,
                picture: "default_avatar.png",
                name: "David Wilson"
            }
        },
        {
            id: 5,
            name: "Eve Johnson",
            status: "Online",
            lastMessage: "Can you check the email?",
            time: "Mon",
            user_data: {
                id: 5,
                picture: "default_avatar.png",
                name: "Eve Johnson"
            }
        }
    ]);
    const [user, setUser] = useState({
        id: 0,
        picture: "default_avatar.png",
        name: ""
    });
    const [friend, setFriend] = useState({
        id: 0,
        picture: "default_avatar.png",
        name: ""
    });
    const [search, setSearch] = useState("");
    const [users, setUsers] = useState([]);
    const [open, setOpen] = useState({
        settings: false,
        sidebar: false
    });

    const getUser = () => {
        $.get("api/", { getUser: "true" }, res => setUser(res));
    }

    const getUsers = () => {
        $.get("api/", { getUsers: "true" }, res => setUsers(res));
    }

    const getChatHeads = () => {
        $.get("api/", { getChatHeads: "true" }, res => {
            if (!Array.isArray(res)) {
                Toast("Failed to load chat heads");
                console.log(res);
                return;
            }
            setChatHeads(res)
        });
    }

    useEffect(() => {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            overlay.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        }
    }, []);

    useEffect(() => {
        getUser();
        getUsers();
        getChatHeads();

        const interval = setInterval(() => {
            getChatHeads();
        }, 2000);

        return () => {
            clearInterval(interval);
        }
    }, []);

    useEffect(() => {
        if (user.id != 0) {
            $.post("api/", { updateUser: "true", data: JSON.stringify(user) }, res => {
                console.log(res);
            });
        }
    }, [user]);
    return (
        <Context.Provider value={{ user, setUser, friend, setFriend, search, setSearch }}>
            <aside
                className="w-full md:w-80 lg:w-96 bg-white border-r border-gray-200 flex flex-col h-full z-20 absolute md:relative transform -translate-x-full md:translate-x-0 transition-transform duration-300 ease-in-out"
                id="sidebar"
                style={{
                    height: innerHeight + "px"
                }}
            >

                <div className="h-20 flex items-center justify-between px-6 border-b border-gray-100 bg-white">
                    <div className="flex items-center space-x-3">
                        <img
                            src={"../uploads/" + user.picture}
                            alt="My Profile"
                            className="w-10 h-10 rounded-full border-2 border-indigo-100"
                            onClick={() => setOpen({ ...open, settings: true })}
                            onError={e => {
                                e.target.onerror = null;
                                e.target.src = "../uploads/default_avatar.png"
                            }}
                        />
                        <div onClick={() => setOpen({ ...open, settings: true })} className="cursor-pointer">
                            <h2 className="text-lg font-bold text-gray-800">{user.name}</h2>
                            <p className="text-xs text-green-500 font-medium flex items-center gap-1">
                                <span className="w-2 h-2 rounded-full bg-green-500"></span> Online
                            </p>
                        </div>
                    </div>
                    <a href="../logout.php" className="text-gray-400 hover:text-red-500 transition-colors" title="Logout">
                        <i className="fas fa-sign-out-alt text-xl"></i>
                    </a>
                </div>


                <div className="p-4 border-b border-gray-100 sticky top-0 bg-white z-10">
                    <div className="relative">
                        <span className="absolute left-4 top-3.5 text-gray-400">
                            <i className="fas fa-search"></i>
                        </span>
                        <input
                            type="text"
                            placeholder="Search chats..."
                            className="w-full bg-gray-50 text-gray-700 rounded-xl py-3 pl-11 pr-4 border-none focus:ring-2 focus:ring-indigo-100 outline-none placeholder-gray-400 transition-all"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                        />
                    </div>
                </div>


                <div className="flex-1 overflow-y-auto p-3 space-y-2">
                    {
                        chatHeads
                            .filter(chatHead => chatHead.user_data.name.toLowerCase().includes(search.toLowerCase()))
                            .map((chatHead, index) => (
                                <div
                                    className={`flex items-center p-3 rounded-xl cursor-pointer transition-all ${friend.id === chatHead.user_data.id ? "bg-indigo-50 border border-indigo-100" : ""}`}
                                    onClick={() => {
                                        setFriend(chatHead.user_data);
                                    }}
                                    key={chatHead.id}
                                >
                                    <div className="relative">
                                        <img
                                            src={"../uploads/" + chatHead.user_data.picture}
                                            className="w-12 h-12 rounded-full object-cover"
                                            onError={e => {
                                                e.target.onerror = null;
                                                e.target.src = "../uploads/default_avatar.png"
                                            }}
                                        />
                                        <span className="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                                    </div>
                                    <div className="ml-4 flex-1">
                                        <div className="flex justify-between items-baseline mb-1">
                                            <h3 className="font-semibold text-gray-800">{chatHead.user_data.name}</h3>
                                            <span className="text-xs text-indigo-600 font-medium">{chatHead.ago}</span>
                                        </div>
                                        <p className="text-sm text-indigo-800 truncate">{chatHead.message}</p>
                                    </div>
                                </div>
                            ))
                    }

                    {
                        users
                            .filter(row => {
                                return chatHeads.find(chatHead => chatHead.user_data.id === row.id) === undefined;
                            })
                            .filter(row => row.name.toLowerCase().includes(search.toLowerCase()))
                            .map((chatHead, index) => (
                                <div
                                    className={`flex items-center p-3 rounded-xl cursor-pointer transition-all ${friend.id === chatHead.id ? "bg-indigo-50 border border-indigo-100" : ""}`}
                                    onClick={() => {
                                        setFriend(chatHead);
                                    }}
                                    key={chatHead.id}
                                >
                                    <div className="relative">
                                        <img
                                            src={"../uploads/" + chatHead.picture}
                                            className="w-12 h-12 rounded-full object-cover"
                                            onError={e => {
                                                e.target.onerror = null;
                                                e.target.src = "../uploads/default_avatar.png"
                                            }}
                                        />
                                        <span className="absolute bottom-0 right-0 w-3 h-3 bg-green-500 border-2 border-white rounded-full"></span>
                                    </div>
                                    <div className="ml-4 flex-1">
                                        <div className="flex justify-between items-baseline mb-1">
                                            <h3 className="font-semibold text-gray-800">{chatHead.name}</h3>
                                            <span className="text-xs text-indigo-600 font-medium">{"start chat"}</span>
                                        </div>
                                        <p className="text-sm text-indigo-800 truncate">{chatHead.email}</p>
                                    </div>
                                </div>
                            ))
                    }
                </div>
            </aside>

            <div className="fixed inset-0 bg-black/50 z-10 hidden md:hidden" id="sidebarOverlay" onclick="toggleSidebar()"></div>

            {friend.id != 0 && <main className="flex-1 flex flex-col h-full bg-white relative w-full">

                <Header />

                <Content />

                <SendMessage />
            </main>}

            {friend.id == 0 && <main className="flex-1 flex flex-col h-full bg-white items-center justify-center w-full">
                <div className="text-gray-500 text-xl">Click on a chat head to start a chat</div>
            </main>}

            <AccountSettingsDialog
                open={open.settings}
                onClose={() => setOpen({ ...open, settings: false })}
            />
        </Context.Provider>
    )
}

window.onload = function () {
    ReactDOM.render(<Index />, document.getElementById('root'));
}