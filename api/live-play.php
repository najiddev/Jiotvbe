<?php

$id = @$_GET['id'];
$user_ip = $_SERVER['REMOTE_ADDR'];
$currentTimestamp = time();

// CONFIG
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
$ch1 = curl_init($handshakeUrl);
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers1);
$response1 = curl_exec($ch1);
curl_close($ch1);
$data1 = json_decode($response1, true);

if (!isset($data1['js']['random']) || !isset($data1['js']['token'])) {
    http_response_code(500);
    echo json_encode(["error" => "Handshake failed. Check portal or MAC."]);
    exit;
}

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
$getProfileUrl = "http://$portal/stalker_portal/server/load.php?type=stb&action=get_profile&hd=1&ver=ImageDescription%3A%200.2.18-r14-pub-250&num_banks=2&sn=$serial&stb_type=MAG270&image_version=218&video_out=hdmi&device_id=$deviceid&device_id2=$deviceid&signature=&auth_second_step=1&hw_version=1.7-BD-00&not_valid_token=0&client_type=STB&hw_version_2=7ec5a49802e4a011344ed3049250f50d&timestamp=$currentTimestamp&api_signature=263&metrics={\"mac\":\"$mac\",\"sn\":\"$serial\",\"model\":\"MAG254\",\"type\":\"STB\",\"uid\":\"$deviceid\",\"random\":\"$token\"}&JsHttpRequest=1-xml";

$ch2 = curl_init($getProfileUrl);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers2);
$response2 = curl_exec($ch2);
curl_close($ch2);

// 3. Resolve Channel ID
$channelId = null;
if (is_numeric($id)) {
    $channelId = $id;
} else {
    // Try to find channel name
    $channelListUrl = "http://$portal/stalker_portal/server/load.php?type=itv&action=get_all_channels&JsHttpRequest=1-xml";
    $ch3 = curl_init($channelListUrl);
    curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch3, CURLOPT_HTTPHEADER, $headers2);
    $resp = curl_exec($ch3);
    curl_close($ch3);
    $channels = json_decode($resp, true)['js']['data'] ?? [];
    foreach ($channels as $ch) {
        if (strtolower($ch['name']) == strtolower($id)) {
            $channelId = $ch['id'];
            break;
        }
    }
}

// 4. Generate Stream
if ($channelId) {
    $createUrl = "http://$portal/stalker_portal/server/load.php?type=itv&action=create_link&cmd=ffmpeg%20http://localhost/ch/$channelId&JsHttpRequest=1-xml";
    $ch4 = curl_init($createUrl);
    curl_setopt($ch4, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch4, CURLOPT_HTTPHEADER, $headers2);
    $result = curl_exec($ch4);
    curl_close($ch4);
    $finalUrl = json_decode($result, true)['js']['cmd'] ?? '';
    if ($finalUrl) {
        header("Location: $finalUrl");
        exit;
    }
}

// Return error if stream not found
http_response_code(404);
header("Content-Type: application/json");
echo json_encode(["error" => "Stream link not found or channel not matched"]);
exit;
