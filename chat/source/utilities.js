const { createContext } = React;
const { createTheme } = MaterialUI;

const { grey } = MaterialUI.colors;

//make the todays date
const now = moment();
const today = now.format('YYYY-MM-DD');
const first_day = now.format('YYYY-MM-') + "01";

var format = new Intl.NumberFormat();

function number_format(num, decimals = null) {
    if (num == undefined || num == "") return "";

    if (decimals == null) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    } else {
        return format.format(num);
    }
}

let theme = createTheme({
    palette: {
        primary: {
            main: '#0052cc',
        },
        secondary: {
            main: '#edf2ff',
        },
    },
    components: {
        MuiButton: {
            styleOverrides: {
                root: {
                    borderRadius: "64px",
                    textTransform: "none",
                    padding: "6px 24px"
                }
            },
            defaultProps: {
                variant: 'contained', // Set the default variant to 'contained'
            },
        },
        MuiTab: {
            styleOverrides: {
                root: {
                    textTransform: "none",
                    minHeight: "unset"
                }
            }
        },
        MuiDialog: {
            styleOverrides: {
                root: {
                    borderRadius: "24px",
                    textTransform: "none",
                },
                paper: {
                    borderRadius: "36px",
                    backgroundColor: "#f8f1f6",
                    padding: "24px 12px",
                    boxShadow: "0px 1px 2px 0px rgb(0 0 0 / 30%), 0px 2px 6px 2px rgb(0 0 0 / 15%);",
                    //border:"14px solid rgba(4, 25, 52, 0.32)",
                    outline: "1px solid rgba(1, 7, 13, 0.32)"
                }
            }
        },
        MuiOutlinedInput: {
            styleOverrides: {
                root: {
                    '& .MuiOutlinedInput-notchedOutline': {
                        borderRadius: '8px',
                    },
                },
            },
        },
    }
});

const styles = {
    fab: {
        boxShadow: "none",
        background: "inherit"
    },
    btn: {
        textTransform: "none",
        lineHeight: "unset"
    },
    smallBtn: {
        textTransform: "none",
        lineHeight: "unset",
        padding: "5px 14px"
    }
}

const useStorage = (key, initialState) => {
    const [value, setValue] = React.useState(
        localStorage.getItem(key) != null ? JSON.parse(localStorage.getItem(key)) : initialState
    );

    React.useEffect(() => {
        localStorage.setItem(key, JSON.stringify(value));
    }, [value, key]);

    return [value, setValue];
};

const useStorageValue = (key, initialState, parse = null) => {
    const [value, setValue] = React.useState(
        localStorage.getItem(key) != null ? parse != null ? parse(localStorage.getItem(key)) : localStorage.getItem(key) : initialState
    );

    React.useEffect(() => {
        localStorage.setItem(key, value);
    }, [value, key]);

    return [value, setValue];
};


function date(format, timestamp = null) {
    // Use provided timestamp or current date
    const date = timestamp ? new Date(timestamp * 1000) : new Date();

    // Helper function to pad numbers with leading zeros
    const pad = (num, size = 2) => String(num).padStart(size, '0');

    const formats = {
        // Day
        'd': () => pad(date.getDate()),
        'D': () => ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][date.getDay()],
        'j': () => date.getDate(),
        'l': () => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][date.getDay()],
        'N': () => date.getDay() === 0 ? 7 : date.getDay(),
        'S': () => {
            const d = date.getDate();
            if (d >= 11 && d <= 13) return 'th';
            switch (d % 10) {
                case 1: return 'st';
                case 2: return 'nd';
                case 3: return 'rd';
                default: return 'th';
            }
        },
        'w': () => date.getDay(),
        'z': () => {
            const start = new Date(date.getFullYear(), 0, 0);
            const diff = date - start;
            return Math.floor(diff / (1000 * 60 * 60 * 24));
        },

        // Month
        'F': () => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'][date.getMonth()],
        'm': () => pad(date.getMonth() + 1),
        'M': () => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'][date.getMonth()],
        'n': () => date.getMonth() + 1,
        't': () => new Date(date.getFullYear(), date.getMonth() + 1, 0).getDate(),

        // Year
        'L': () => {
            const year = date.getFullYear();
            return year % 4 === 0 && (year % 100 !== 0 || year % 400 === 0) ? 1 : 0;
        },
        'Y': () => date.getFullYear(),
        'y': () => date.getFullYear().toString().slice(-2),

        // Time
        'a': () => date.getHours() < 12 ? 'am' : 'pm',
        'A': () => date.getHours() < 12 ? 'AM' : 'PM',
        'g': () => date.getHours() % 12 || 12,
        'G': () => date.getHours(),
        'h': () => pad(date.getHours() % 12 || 12),
        'H': () => pad(date.getHours()),
        'i': () => pad(date.getMinutes()),
        's': () => pad(date.getSeconds()),
        'u': () => pad(date.getMilliseconds() * 1000, 6),

        // Timezone
        'O': () => {
            const offset = date.getTimezoneOffset();
            const hours = Math.abs(Math.floor(offset / 60));
            const minutes = Math.abs(offset % 60);
            return (offset <= 0 ? '+' : '-') + pad(hours) + pad(minutes);
        },
        'P': () => {
            const offset = date.getTimezoneOffset();
            const hours = Math.abs(Math.floor(offset / 60));
            const minutes = Math.abs(offset % 60);
            return (offset <= 0 ? '+' : '-') + pad(hours) + ':' + pad(minutes);
        },
        'Z': () => -date.getTimezoneOffset() * 60,

        // Full Date/Time
        'c': () => date.toISOString(),
        'r': () => date.toUTCString(),
        'U': () => Math.floor(date.getTime() / 1000)
    };

    // Replace each character in the format string with its corresponding value
    return format.replace(/[dDjlNSwzFmMntLYyaAgGhHisueOPZcrU]/g, match => {
        const replacement = formats[match];
        return replacement ? replacement() : match;
    });
}

function post(url, formdata, callback) {
    var ajax = new XMLHttpRequest();

    var completeHandler = function (event) {
        const contentType = ajax.getResponseHeader("Content-Type");
        console.log(contentType);
        if (contentType == "application/json") {
            try {
                callback(JSON.parse(event.target.responseText));
            }
            catch (E) {
                console.error(E.toString());
                console.error("Failed to parse: " + event.target.responseText);
            }
        }
        else {
            var response = event.target.responseText;
            callback(response);
        }
    }

    var progressHandler = function (event) {
        //try{return obj.progress(event.loaded, event.total);}catch(E){}
    }

    ajax.upload.addEventListener("progress", progressHandler, false);
    ajax.addEventListener("load", completeHandler, false);
    //ajax.addEventListener("error", errorHandler, false);
    //ajax.addEventListener("abort", abortHandler, false);
    ajax.open("POST", url);
    ajax.send(formdata);
}

var Strings = (function () {
    function words(stmt, count) {
        count = count == undefined ? 6 : count

        let chars = stmt.split(" ");
        if (chars.length > count) {
            chars = chars.slice(0, count)
        }

        return chars.join(" ");
    }

    function uc_words(stmt) {
        let chars = stmt.split(" ");
        for (let i = 0; i < chars.length; i++) {
            chars[i] = chars[i].charAt(0).toUpperCase() + chars[i].substring(1).toLowerCase();
        }

        return chars.join(" ");
    }

    return {
        words,
        uc_words
    }
})();

const isNumber = (value) => {
    if (value == "") return false;
    return !isNaN(Number(value));
}


function numberToWords(num) {
    if (num === 0) return "zero";

    const belowTwenty = [
        "zero", "one", "two", "three", "four", "five", "six", "seven", "eight", "nine", "ten",
        "eleven", "twelve", "thirteen", "fourteen", "fifteen", "sixteen", "seventeen", "eighteen", "nineteen"
    ];
    const tens = [
        "", "", "twenty", "thirty", "forty", "fifty", "sixty", "seventy", "eighty", "ninety"
    ];
    const thousands = [
        "", "thousand", "million"
    ];

    function helper(n) {
        if (n < 20) return belowTwenty[n];
        if (n < 100) return tens[Math.floor(n / 10)] + (n % 10 !== 0 ? " " + belowTwenty[n % 10] : "");
        if (n < 1000) return belowTwenty[Math.floor(n / 100)] + " hundred" + (n % 100 !== 0 ? " " + helper(n % 100) : "");
        for (let i = 1, p = 1000; i < thousands.length; i++, p *= 1000) {
            if (n < p * 1000) {
                return helper(Math.floor(n / p)) + " " + thousands[i] + (n % p !== 0 ? " " + helper(n % p) : "");
            }
        }
    }

    return helper(num);
}


const uuidv4 = () => {
    return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
        const r = Math.random() * 16 | 0;
        const v = c === 'x' ? r : (r & 0x3 | 0x8);
        return v.toString(16);
    });
}

const config = {
    comments_per_page: 4,
    bg_dark: '#121212',
    bg_light: '#f9f9f9',
    border_dark: grey[900],
    border_light: "#f1f1f1"
}

export {
    theme,
    styles,
    useStorage,
    useStorageValue,
    today,
    first_day,
    format,
    number_format,
    date,
    post,
    Strings,
    isNumber,
    numberToWords,
    uuidv4,
    config
}