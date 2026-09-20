<?php
// Token shu yerga kiritildi
 $BOT_TOKEN = "8803203058:AAHSL-b-pVyjk2He3dTFNH2-6uxrso3M0us"; 

// Telegramdan kelgan POST so'rovni o'qiymiz
 $update = json_decode(file_get_contents("php://input"), true);

// Agar so'rov bo'sh bo'lsa yoki message bo'lmasa, chiqib ketamiz
if (!$update || !isset($update["message"])) {
    http_response_code(200);
    exit();
}

 $chat_id = $update["message"]["chat"]["id"];
 $text = trim($update["message"]["text"]);

// /start buyrug'i uchun
if ($text == "/start") {
    $msg = "👋 Salom! Men Instagram ma'lumotlarini chiqaruvchi botman.\n\nFoydalanuvchi nomini yuboring (masalan: @username yoki username)";
    sendMessage($chat_id, $msg, $BOT_TOKEN);
} elseif ($text != "") {
    // Username dan @ belgisini olib tashlaymiz
    $User = ltrim($text, "@");
    
    // Sizning API funksiyangiz
    $insta_info = Info($User);
    
    // Natijani tekshiramiz
    if ($insta_info && $insta_info !== "Foydalanuvchi topilmadi!") {
        $msg = "📸 @" . $User . " haqida ma'lumot:\n\n" . $insta_info;
    } else {
        $msg = "❌ Kechirasiz, @" . $User . " nomli foydalanuvchi topilmadi yoki API xato berdi!";
    }
    
    sendMessage($chat_id, $msg, $BOT_TOKEN);
}

// Vercelga so'rov muvaffaqiyatli tugaganini aytamiz
http_response_code(200);
echo "OK";


// --- FUNKSIYALAR ---

function Info($User) {
    $api_key = "@87-T:2YU>B]A<&DO:6YS=&\$O:6YD97@N<&AP/W5R;#T` `";
    $instagram = convert_uudecode($api_key).$User;
    
    // file_get_contents ga so'rov yuboramiz
    $result = @file_get_contents($instagram);
    
    if ($result === FALSE || empty($result)) {
        return "Foydalanuvchi topilmadi!";
    }
    
    return $result;
}

function sendMessage($chat_id, $message, $token) {
    $url = "https://api.telegram.org/bot" . $token . "/sendMessage?" . http_build_query([
        'chat_id' => $chat_id,
        'text' => $message
    ]);
    
    @file_get_contents($url);
}
?>