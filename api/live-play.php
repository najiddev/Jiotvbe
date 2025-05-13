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

// 2. Get Profile (required by some portals to unlock stream)
$headers2 = $headers1;
$headers2[] = "Authorization: Bearer $realToken";

$getProfileUrl = "http://$portal/stalker_portal/server/load.php?type=stb&action=get_profile&hd=1&sn=$serial&device_id=$deviceid&device_id2=$deviceid&auth_second_step=1&random=$token&JsHttpRequest=1-xml";

$ch2 = curl_init();
curl_setopt($ch2, CURLOPT_URL, $getProfileUrl);
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers2);
$response2 = curl_exec($ch2);
curl_close($ch2);

// 3. Try to detect whether $id is a full stream path or just a channel name
if (strpos($id, '/') !== false || strpos($id, '.m3u8') !== false) {
    // Full stream link
    header("Location: http://$portal/$id");
    exit;
}

// 4. Try to create stream link
$createLinkUrl = "http://$portal/stalker_portal/server/load.php?type=itv&action=create_link&cmd=ffrt%3Ahttp://localhost/ch/$id&JsHttpRequest=1-xml";

$ch3 = curl_init();
curl_setopt($ch3, CURLOPT_URL, $createLinkUrl);
curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch3, CURLOPT_HTTPHEADER, $headers2);
$response3 = curl_exec($ch3);
curl_close($ch3);

$data3 = json_decode($response3, true);
$finalStreamUrl = $data3['js']['cmd'] ?? '';

if ($finalStreamUrl) {
    header("Location: $finalStreamUrl");
} else {
    header("Content-Type: application/json");
    echo json_encode(["error" => "Stream link not found or channel not matched"]);
}
exit;
