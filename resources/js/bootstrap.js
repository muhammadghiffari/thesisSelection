import Echo from "laravel-echo";
import Pusher from "pusher-js";
import "./echo";
import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
