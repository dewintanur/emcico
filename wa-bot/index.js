const { makeWASocket, useMultiFileAuthState } = require("@whiskeysockets/baileys");
const fs = require("fs");

async function connectWA() {
    const { state, saveCreds } = await useMultiFileAuthState("auth_info");
    const sock = makeWASocket({ auth: state });

    sock.ev.on("creds.update", saveCreds);
    sock.ev.on("connection.update", ({ connection }) => {
        if (connection === "open") console.log("✅ WhatsApp Connected!");
        if (connection === "close") console.log("❌ WhatsApp Disconnected!");
    });

    return sock;
}

async function sendQRCode(number, kodeBooking) {
    const sock = await connectWA();
    const imagePath = `../storage/app/public/qrcodes/${kodeBooking}.png`;

    if (!fs.existsSync(imagePath)) {
        console.log(`⚠ QR Code ${kodeBooking} tidak ditemukan.`);
        return;
    }

    const message = `Halo, berikut QR code untuk check-in:\nKode Booking: ${kodeBooking}\nGunakan QR ini untuk check-in di MCC.`;
    const media = fs.readFileSync(imagePath);

    await sock.sendMessage(number + "@s.whatsapp.net", { image: media, caption: message });
    console.log(`✅ QR Code terkirim ke ${number}`);
}

const args = process.argv.slice(2);
if (args.length < 2) {
    console.log("⚠ Gunakan: node index.js <nomor> <kode_booking>");
    process.exit(1);
}

sendQRCode(args[0], args[1]);
