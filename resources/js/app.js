import './bootstrap';
import Echo from "laravel-echo";
import Pusher from "pusher-js";

window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: "pusher",
    key: process.env.MIX_PUSHER_APP_KEY,
    cluster: process.env.MIX_PUSHER_APP_CLUSTER,
    forceTLS: true
});

window.Echo.private("konfirmasi-checkout")
    .listen("KonfirmasiCheckoutEvent", (event) => {
        alert(`Ruangan untuk booking ID ${event.booking_id} siap untuk checkout!`);
        // Bisa juga update tampilan button checkout di dashboard FO
    });
