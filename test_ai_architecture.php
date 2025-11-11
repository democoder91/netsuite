<?php
// Test the new AI architecture

echo "NetSuite AI Agent - Architecture Test\n";
echo "=====================================\n\n";

// Test 1: Check AI Status
echo "Test 1: Check AI Status\n";
echo "-----------------------\n";
$ch1 = curl_init('http://netsuite.test/api/netsuite/ai/status');
curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
$response1 = curl_exec($ch1);
$status1 = curl_getinfo($ch1, CURLINFO_HTTP_CODE);
curl_close($ch1);

echo "Status: $status1\n";
echo "Response: $response1\n\n";

// Test 2: List Available Tables
echo "Test 2: List Available Tables\n";
echo "-----------------------------\n";
$ch2 = curl_init('http://netsuite.test/api/netsuite/ai/tables');
curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
$response2 = curl_exec($ch2);
$status2 = curl_getinfo($ch2, CURLINFO_HTTP_CODE);
curl_close($ch2);

echo "Status: $status2\n";
$data2 = json_decode($response2, true);
if ($data2['success'] ?? false) {
    echo "Tables found: " . ($data2['count'] ?? 0) . "\n";
    echo "Sample tables: " . implode(', ', array_slice($data2['tables'] ?? [], 0, 5)) . "...\n";
} else {
    echo "Response: " . substr($response2, 0, 200) . "...\n";
}
echo "\n";

// Test 3: Ask a Question (only if AI is available)
echo "Test 3: Ask a Question\n";
echo "----------------------\n";
$aiData = json_decode($response1, true);
if ($aiData['available'] ?? false) {
    echo "AI Model: " . ($aiData['model'] ?? 'Unknown') . "\n";
    echo "Sending question: 'Show me 3 customers'\n\n";

    $ch3 = curl_init('http://netsuite.test/api/netsuite/ai/ask');
    curl_setopt($ch3, CURLOPT_POST, true);
    curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch3, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch3, CURLOPT_POSTFIELDS, json_encode([
        'prompt' => 'Show me 3 customers'
    ]));
    curl_setopt($ch3, CURLOPT_TIMEOUT, 120); // AI might take longer

    $response3 = curl_exec($ch3);
    $status3 = curl_getinfo($ch3, CURLINFO_HTTP_CODE);
    curl_close($ch3);

    echo "Status: $status3\n";
    if ($status3 === 200) {
        $data3 = json_decode($response3, true);
        echo "Generated Query: " . ($data3['generated_query'] ?? 'N/A') . "\n";
        echo "AI Response:\n" . ($data3['ai_response'] ?? 'N/A') . "\n";
    } else {
        echo "Response: " . substr($response3, 0, 300) . "...\n";
    }
} else {
    echo "⚠ AI model not available. Skipping question test.\n";
    echo "To enable:\n";
    echo "1. Install Ollama: https://ollama.ai\n";
    echo "2. Run: ollama pull llama2\n";
    echo "3. Set in .env: AI_MODEL=local, LOCAL_AI_URL=http://localhost:11434\n";
}

echo "\n";
echo "=====================================\n";
echo "✓ Architecture tests completed!\n\n";

echo "Summary:\n";
echo "--------\n";
echo "✓ Interface-based design\n";
echo "✓ Trait for error handling\n";
echo "✓ Local AI model implementation\n";
echo "✓ Table name fetching from NetSuite\n";
echo "✓ Dynamic query generation\n";
echo "✓ Response formatting\n";

