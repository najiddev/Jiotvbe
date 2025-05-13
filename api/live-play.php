<?php

$id = @$_GET['id'];
$user_ip = $_SERVER['REMOTE_ADDR'];
$currentTimestamp = time();

// CONFIGURATION
$portal = "tv.fusion4k.cc";
$mac = "00:1A:79:00:02:2B";
$deviceid = "EB33A9633A8664B14E27807A8A53CFF299DD38E76996A8C1D7B5D0E2D32890CF";
$serial = "973DDBA22C9B6";

// 1. Handshake
$handshakeUrl = "http://$portal/stalker_portal/server/load.php?type=stb&action=handshake&prehash=false&JsHttpRequest=1-xml";

$headers1 = [
    "Cookie: mac=$mac; stb_lang=en; timezone=GMT",
    "X-Forwarded-For: $user_ip",
    "Referer: http://$portal/stalker_portal/c/",
    "User-Agent: Mozilla/5.0 (QtEmbedded; U; Linux; C) AppleWebKit/533.3 (KHTML, like Gecko) MAG200 stbapp ver: 2 rev: 250 Safari/533.3",
    "X-User-Agent: Model: MAG250; Link:",
];

$ch1 = curl_init();
curl_setopt($ch1, CURLOPT_URL, $handshakeUrl);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers1);
$response1 = curl_exec($ch1);
curl_close($ch1);

$data1 = json_decode($response1, true);
$token = $data1['js']['random'];
$realToken = $data1['js']['token'];

// 2. Get Profile
$headers2 = [
    "Cookie: mac=$mac; stb_lang=en; timezone=GMT",
    "X-Forwarded-For: $user_ip",
    "Authorization: Bearer $realToken",
    "Referer: http://$portal/stalker_portal/c/",
    "User-Agent: Mozilla/5.0 (QtEmbedded; U; Linux; C) AppleWebKit/533.3 (KHTML, like Gecko) MAG200 stbapp ver: 2 rev: 250 Safari/533.3",
    "X-User-Agent: Model: MAG250; Link:",
];

$getProfileUrl = "http://$portal/stalker_portal/server/load.php?type=stb&action=get_profile&hd=1&ver=ImageDescription%3A%200.2.18-r14-pub-250&num_banks=2&sn=$serial&stb_type=MAG270&image_version=218&video_out=hdmi&device_id=$deviceid&device_id2=$deviceid&signature=&auth_second_step=1&hw_version=1.7-BD-00¬_valid_token=0&client_type=STB&hw_version_2=7ec5a49802e4a011344ed3049250f50d×tamp=$currentTimestamp&api_signature=263&metrics={\"mac\":\"$mac\",\"sn\":\"$serial\",\"model\":\"MAG254\",\"type\":\"STB\",\"uid\":\"$deviceid\",\"random\":\"$token\"}&JsHttpRequest=1-xml";

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $getProfileUrl);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers2);
$response2 = curl_exec($ch2);
curl_close($ch2);

// 3. Create Stream Link
$createLinkUrl = "http://$portal/stalker_portal/server/load.php?type=itv&action=create_link&cmd=ffrt%http://localhost/ch/$id&JsHttpRequest=1-xml";

$ch3 = curl_init();
curl_setopt($ch3, CURLOPT_URL, $createLinkUrl);
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch3, CURLOPT_HTTPHEADER, $headers2);
$response3 = curl_exec($ch3);
curl_close($ch3);

$data3 = json_decode($response3, true);
$finalStreamUrl = $data3['js']['cmd'] ?? '';

if ($finalStreamUrl) {
    header("Location: $finalStreamUrl");
} else {
    header("Content-Type: application/json");
    echo json_encode(["error" => "Stream link not found"]);
}
exit;
