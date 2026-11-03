<?php
header("Content-Type: text/plain; charset=utf-8");

if (!isset($_GET["query"]) || trim($_GET["query"]) === "") {
    http_response_code(400);
    echo "No question sent.";
    exit;
}

$query = trim($_GET["query"]);
// Replace this placeholder privately in VS Code.
const OPENAI_API_KEY = "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";

$headerParameters = array(
    "Content-Type: application/json",
    "Authorization: Bearer " . OPENAI_API_KEY
);

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,
    "https://api.openai.com/v1/chat/completions");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headerParameters);
$parameters = array(
    "model" => "gpt-3.5-turbo",
    "messages" => array(
        array("role" => "user", "content" => $query)
    ),
    "max_tokens" => 400,
    "temperature" => 0.2
);

curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($parameters));

$response = curl_exec($ch);

if ($response === false) {
    http_response_code(502);
    echo "Could not connect to OpenAI.";
    curl_close($ch);
    exit;
}

$httpStatusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$result = json_decode($response, true);
curl_close($ch);

if ($httpStatusCode !== 200) {
    http_response_code(502);
    $errorCode = $result["error"]["code"] ?? "unknown";
echo "OpenAI error: " . $httpStatusCode . " — " . $errorCode;
    exit;
}

if (!isset($result["choices"][0]["message"]["content"])) {
    http_response_code(502);
    echo "No response text was received.";
    exit;
}

echo $result["choices"][0]["message"]["content"];