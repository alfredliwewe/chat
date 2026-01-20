const {
    Dialog,
    Box,
    Typography,
    List,
    ListItemButton,
    ListItemIcon,
    ListItemText,
    IconButton,
    Avatar,
    Divider,
    Button,
    TextField,
} = MaterialUI;
const { useState, useEffect, useContext } = React;
import { Context } from "../index.js";
import { post } from "../utilities.js";

export function AccountSettingsDialog({ open, onClose }) {
    const { user } = useContext(Context);
    const [activeMenu, setActiveMenu] = useState("Account");
    const menuItems = [
        { label: "Account", icon: <span className="material-symbols-rounded">person</span>, active: true },
        { label: "Sessions", icon: <span className="material-symbols-rounded">laptop_mac</span> },
        { label: "Appearance", icon: <span className="material-symbols-rounded">palette</span> },
        { label: "Notifications", icon: <span className="material-symbols-rounded">notifications</span> },
        { label: "Preferences", icon: <span className="material-symbols-rounded">tune</span> },
        { label: "Keyboard", icon: <span className="material-symbols-rounded">keyboard</span> },
        { label: "Sidebar", icon: <span className="material-symbols-rounded">view_sidebar</span> },
        { label: "Voice & Video", icon: <span className="material-symbols-rounded">mic</span> },
        { label: "Security & Privacy", icon: <span className="material-symbols-rounded">security</span> },
        { label: "Encryption", icon: <span className="material-symbols-rounded">lock</span> },
        { label: "Labs", icon: <span className="material-symbols-rounded">science</span> },
        { label: "Help & About", icon: <span className="material-symbols-rounded">help_outline</span> }
    ];

    return (
        <Dialog open={open} onClose={onClose} maxWidth="md" fullWidth>
            <Box sx={{ display: "flex", height: 600 }}>

                {/* Sidebar */}
                <Box
                    sx={{
                        width: 240,
                        borderRight: "1px solid #e0e0e0",
                        p: 2,
                        overflowY: "auto"
                    }}
                >
                    <Typography variant="h6" mb={2}>
                        Settings
                    </Typography>

                    <List>
                        {menuItems.map((item) => (
                            <ListItemButton
                                key={item.label}
                                selected={item.label === activeMenu}
                                sx={{
                                    borderRadius: 3,
                                    mb: 0.5,
                                    py: 1
                                }}
                                onClick={() => setActiveMenu(item.label)}
                            >
                                <ListItemIcon>{item.icon}</ListItemIcon>
                                <ListItemText primary={item.label} />
                            </ListItemButton>
                        ))}
                    </List>
                </Box>

                {/* Main Content */}
                <Box sx={{ flex: 1, p: 3, position: "relative" }}>

                    {/* Close Button */}
                    <IconButton
                        onClick={onClose}
                        sx={{ position: "absolute", top: 16, right: 16 }}
                    >
                        <span className="material-symbols-rounded">close</span>
                    </IconButton>

                    {activeMenu === "Account" && <ProfileSettings />}
                </Box>
            </Box>
        </Dialog>
    );
}


function ProfileSettings() {
    const { user, setUser } = useContext(Context);

    const changePicture = () => {
        let input = document.createElement("input");
        input.type = 'file';
        input.accept = 'image/*';
        input.addEventListener('change', function (event) {
            //upload
            let formdata = new FormData();
            formdata.append("change_picture", input.files[0]);

            post("api/", formdata, function (response) {
                try {
                    let res = JSON.parse(response);

                    if (res.status) {
                        setUser({ ...user, picture: res.filename });
                    }
                }
                catch (e) {
                    alert(e.toString() + response);
                }
            })
        });

        input.click();
    }

    return (
        <Box>
            <Typography variant="h5" mb={0.5}>
                Profile
            </Typography>
            <Typography variant="body2" color="text.secondary" mb={3}>
                This is how you appear to others on the app.
            </Typography>

            {/* Profile Row */}
            <Box sx={{ display: "flex", alignItems: "center", mb: 3 }}>
                <Box sx={{ position: "relative" }}>
                    <Avatar
                        src={"../uploads/" + user.picture}
                        sx={{ width: 64, height: 64, bgcolor: "#f3e5f5", color: "#7b1fa2" }}
                    >
                        {user.name?.charAt(0).toUpperCase()}
                    </Avatar>
                    <IconButton
                        size="small"
                        sx={{
                            position: "absolute",
                            bottom: -4,
                            right: -4,
                            bgcolor: "#fff",
                            boxShadow: 1
                        }}
                        onClick={changePicture}
                    >
                        <span className="material-symbols-rounded">edit</span>
                    </IconButton>
                </Box>

                <Box sx={{ ml: 3, flex: 1 }}>
                    <Typography variant="subtitle2" mb={0.5}>
                        Display Name
                    </Typography>
                    <TextField
                        fullWidth
                        size="small"
                        value={user.name}
                        onChange={(e) => setUser({ ...user, name: e.target.value })}
                    />
                </Box>
            </Box>

            {/* Username */}
            <Typography variant="subtitle2" mb={0.5}>
                Username
            </Typography>
            <TextField
                fullWidth
                size="small"
                value={user.email}
                onChange={(e) => setUser({ ...user, email: e.target.value })}
                InputProps={{
                    readOnly: true,
                    endAdornment: (
                        <IconButton size="small">
                            <span className="material-symbols-rounded">content_copy</span>
                        </IconButton>
                    )
                }}
            />

            {/* Phone */}
            <Typography variant="subtitle2" mb={0.5}>
                Phone
            </Typography>
            <TextField
                fullWidth
                size="small"
                value={user.phone}
                onChange={(e) => setUser({ ...user, phone: e.target.value })}
                InputProps={{
                    readOnly: true,
                    endAdornment: (
                        <IconButton size="small">
                            <span className="material-symbols-rounded">content_copy</span>
                        </IconButton>
                    )
                }}
            />

            <Divider sx={{ my: 3 }} />

            {/* Actions */}
            <Box sx={{ display: "flex", gap: 2 }}>
                <Button
                    variant="contained"
                    color="inherit"
                    startIcon={<span className="material-symbols-rounded">open_in_new</span>}
                >
                    Manage account
                </Button>

                <Button
                    variant="outlined"
                    color="error"
                    startIcon={<span className="material-symbols-rounded">logout</span>}
                >
                    Sign out
                </Button>
            </Box>
        </Box>
    );
}