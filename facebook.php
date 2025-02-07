<?php

echo $_SERVER["HTTP_USER_AGENT"];
// exit;
$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://graph.facebook.com/v12.0/952025535749682/events?access_token=EAANMiW59o9YBABXxrkd2Fnfy0xUk0G4HfJp1DWjSD2ArgvDV8jbW1vZB8Bdj3AFz2jJZBpgK8wc0BXrulL93hyVvqrDx85aE6QjyobZB6UU1yoSBQU8dW723YPVmTRs9bmA8MRrIXJX56L2IzqAMBzuAMfQkVfHeT19xDmpx93oHLZBwrQ71',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => '{
        "data": [
            {
                "event_name": "InitiateCheckout",
                "event_time": 1638303210,
                "action_source": "other",
                "event_source_url": null,
                "user_data": {
                    "em": [
                        "7b17fb0bd173f625b58636fb796407c22b3d16fc78302d79f0fd30c2fc2fc068"
                    ],
                    "ph": [
                        "2c06473a4686a6180d3c1346e79a3750108c3d97e0761db9eeb8d7d19d762a0a"
                    ],
                    "fn": [
                        "a432d817e884eb8bd3953177b68c747b64d8d6a705b79787445750bf80fe55b6"
                    ],
                    "st": [
                        "6f496e9738ca13d116978af2d9a3b15922fd01087585ba27f0aae705fa77bc65"
                    ],
                    "zp": [
                        "31432cebcce56d422aa0e7eded519c83a5463ec7ee79df2703ffe0470c0c6128"
                    ],
                    "client_ip_address": "177.128.86.96",
                    "client_user_agent": "chrome"
                },
                "custom_data": {
                    "currency": "BRL",
                    "value": "142.52"
                }
            }
        ]
    }',
    CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
    ),
));

$response = curl_exec($curl);

curl_close($curl);
echo $response;
