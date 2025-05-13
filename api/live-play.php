<?php

$id = @$_GET['id'];
$user_ip = $_SERVER['REMOTE_ADDR'];
$currentTimestamp = time();
$portal = "jiotv.be";
$mac = "00:1A:79:25:5B:B4";
$deviceid = "75AEBED8B8AAFBDB0B82529522413FA773DC3CF7F975C833FF296702CA5EAC18";
$deviceid2 = "75AEBED8B8AAFBDB0B82529522413FA773DC3CF7F975C833FF296702CA5EAC18";
$serial = "FFDE8EE8C4D1A";
$sig = "";

$n1 = "http://$portal/stalker_portal/server/load.php?type=stb&action=handshake&prehash=false&JsHttpRequest=1-xml";

$h1 = [
    "Cookie: mac=$mac; stb_lang=en; timezone=GMT",
    "X-Forwarded-For: $user_ip",
    "Referer: http://$portal/stalker_portal/c/",
    "User-Agent: Mozilla/5.0 (QtEmbedded; U; Linux; C) AppleWebKit/533.3 (KHTML, like Gecko) MAG200 stbapp ver: 2 rev: 250 Safari/533.3",
    "X-User-Agent: Model: MAG250; Link:",
];

$c1_curl = curl_init();
curl_setopt($c1_curl, CURLOPT_URL, $n1);
curl_setopt($c1_curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($c1_curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($c1_curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c1_curl, CURLOPT_HTTPHEADER, $h1);
curl_setopt($c1_curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (QtEmbedded; U; Linux; C) AppleWebKit/533.3 (KHTML, like Gecko) MAG200 stbapp ver: 2 rev: 250 Safari/533.3');
$res1 = curl_exec($c1_curl);
curl_close($c1_curl);

$response = json_decode($res1, true);
$token = $response['js']['random'];
$real = $response['js']['token'];

$h2 = [
    "Cookie: mac=$mac; stb_lang=en; timezone=GMT",
    "X-Forwarded-For: $user_ip",
    "Authorization: Bearer EC5897889BE161532C08D180A24B3707",
    "Referer: http://$portal/stalker_portal/c/",
    "User-Agent: Mozilla/5.0 (QtEmbedded; U; Linux; C) AppleWebKit/533.3 (KHTML, like Gecko) MAG200 stbapp ver: 2 rev: 250 Safari/533.3",
    "X-User-Agent: Model: MAG250; Link:",
];

$n2 = "http://jiotv.be/stalker_portal/server/load.php?type=stb&action=get_profile&hd=1&ver=ImageDescription%3A%200.2.18-r14-pub-250%3B%20ImageDate%3A%20Fri%20Jan%2015%2015%3A20%3A44%20EET%202016%3B%20PORTAL%20version%3A%205.1.0%3B%20API%20Version%3A%20JS%20API%20version%3A%20328%3B%20STB%20API%20version%3A%20134%3B%20Player%20Engine%20version%3A%200x566&num_banks=2&sn=FFDE8EE8C4D1A&stb_type=MAG270&image_version=218&video_out=hdmi&device_id=75AEBED8B8AAFBDB0B82529522413FA773DC3CF7F975C833FF296702CA5EAC18&device_id2=75AEBED8B8AAFBDB0B82529522413FA773DC3CF7F975C833FF296702CA5EAC18&signature=&auth_second_step=1&hw_version=1.7-BD-00¬_valid_token=0&client_type=STB&hw_version_2=7ec5a49802e4a011344ed3049250f50d×tamp=1746163583&api_signature=263&metrics={\"mac\":\"$mac\",\"sn\":\"$serial\",\"model\":\"MAG254\",\"type\":\"STB\",\"uid\":\"$deviceid\",\"random\":\"$token\"}&JsHttpRequest=1-xml";


$c2_curl = curl_init();
curl_setopt($c2_curl, CURLOPT_URL, $n2);
curl_setopt($c2_curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($c2_curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($c2_curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c2_curl, CURLOPT_HTTPHEADER, $h2);
curl_setopt($c2_curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (QtEmbedded; U; Linux; C) AppleWebKit/533.3 (KHTML, like Gecko) MAG200 stbapp ver: 2 rev: 250 Safari/533.3');
$res2 = curl_exec($c2_curl);
curl_close($c2_curl);

$n3 = "http://$portal/stalker_portal/server/load.php?type=itv&action=create_link&cmd=ffrt%http://localhost/ch/$id&JsHttpRequest=1-xml";

$c3_curl = curl_init();
curl_setopt($c3_curl, CURLOPT_URL, $n3);
curl_setopt($c3_curl, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($c3_curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($c3_curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($c3_curl, CURLOPT_HTTPHEADER, $h2);
curl_setopt($c3_curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (QtEmbedded; U; Linux; C) AppleWebKit/533.3 (KHTML, like Gecko) MAG200 stbapp ver: 2 rev: 250 Safari/533.3');
$res3 = curl_exec($c3_curl);
curl_close($c3_curl);

$i6 = json_decode($res3, true);
$d7 = $i6["js"]["cmd"];

header("Location: ".$d7);
die;
