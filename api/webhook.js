const BOT_TOKEN = "8803203058:AAHSL-b-pVyjk2He3dTFNH2-6uxrso3M0us";
const INSTA_API_URL = "https://asti.uz/api/insta/index.php?url=";

module.exports = async (req, res) => {
  if (req.method !== "POST") {
    res.status(200).end("OK");
    return;
  }

  const message = req.body && req.body.message;
  if (!message) {
    res.status(200).end("OK");
    return;
  }

  const chatId = message.chat.id;
  const text = (message.text || "").trim();

  if (text === "/start") {
    const msg =
      "👋 Salom! Men Instagram ma'lumotlarini chiqaruvchi botman.\n\nFoydalanuvchi nomini yuboring (masalan: @username yoki username)";
    await sendMessage(chatId, msg);
  } else if (text !== "") {
    const user = text.replace(/^@/, "");
    const info = await getInfo(user);

    if (info) {
      await sendMessage(chatId, "📸 @" + user + " haqida ma'lumot:\n\n" + info);
    } else {
      await sendMessage(
        chatId,
        "❌ Kechirasiz, @" + user + " nomli foydalanuvchi topilmadi yoki API xato berdi!"
      );
    }
  }

  res.status(200).end("OK");
};

async function getInfo(user) {
  try {
    const resp = await fetch(INSTA_API_URL + encodeURIComponent(user));
    const data = await resp.text();
    if (!resp.ok || data.length === 0) return null;
    return data;
  } catch (err) {
    return null;
  }
}

async function sendMessage(chatId, text) {
  try {
    const url =
      "https://api.telegram.org/bot" +
      BOT_TOKEN +
      "/sendMessage?" +
      new URLSearchParams({ chat_id: chatId, text });
    await fetch(url);
  } catch (err) {}
}