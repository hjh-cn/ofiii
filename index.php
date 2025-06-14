<?php

function generateRandomDeviceId() {
    return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand(0, 0xffff), mt_rand(0, 0xffff),
        mt_rand(0, 0xffff),
        mt_rand(0, 0x0fff) | 0x4000,
        mt_rand(0, 0x3fff) | 0x8000,
        mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
    );
}

$baseUrl = 'https://cdi.ofiii.com/ofiii_cdi/video/urls';
$deviceId = generateRandomDeviceId();
$assetId = $_GET['asset_id'] ?? 'iNEWS';

$url = "$baseUrl?device_type=pc&device_id=$deviceId&media_type=channel&asset_id=$assetId";

$headers = [
    'accept: application/json, text/plain, */*',
    'accept-language: zh-CN,zh;q=0.9,en;q=0.8,en-GB;q=0.7,en-US;q=0.6',
    'cache-control: no-cache',
    'content-type: text/plain',
    'origin: https://www.ofiii.com',
    'pragma: no-cache',
    'priority: u=1, i',
    'referer: https://www.ofiii.com/',
    'sec-ch-ua: "Microsoft Edge";v="131", "Chromium";v="131", "Not_A Brand";v="24"',
    'sec-ch-ua-mobile: ?0',
    'sec-ch-ua-platform: "macOS"',
    'sec-fetch-dest: empty',
    'sec-fetch-mode: cors',
    'sec-fetch-site: same-site',
    'user-agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36 Edg/131.0.0.0'
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    $data = json_decode($response, true);
    if (isset($data['asset_urls'][0])) {
        $assetUrl = $data['asset_urls'][0];
        
        // Perform a second curl request to the asset URL
        $ch2 = curl_init();
        curl_setopt($ch2, CURLOPT_URL, $assetUrl);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

        $assetResponse = curl_exec($ch2);

        if (curl_errno($ch2)) {
            echo 'Error fetching asset URL: ' . curl_error($ch2);
        } else {
            header('Location: ' . $assetUrl);
            exit;
        }

        curl_close($ch2);
    } else {
        echo $response;
    }
}

curl_close($ch);

?>
