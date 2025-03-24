import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    wsHost: window.location.hostname,
    wsPort: 6001,
    disableStats: true,
});

window.Echo.connector.pusher.connection.bind("connected", function () {
    console.log("Connected to Pusher!");
});

window.Echo.connector.pusher.connection.bind("error", function (err) {
    console.error("Pusher error:", err);
});
