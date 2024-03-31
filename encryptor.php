<?php
// Function to generate a random string for the script filename
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

// The script content to be executed on Node.js
$script_content = "
    const adyenEncrypt = require('node-adyen-encrypt')(adyenVersion);
    const options = {};
    const genTime = new Date().toISOString();
    const ccData = {
        generationtime: genTime,
        holderName: '\$firstname \$lastnamess',
        number: '\$cc'
    }
    const mmData = {
        generationtime: genTime,
        holderName: '\$lastnames \$firstname',
        expiryMonth: '\$mm'
    }
    const yyyyData = {
        holderName: '\$lastnamess \$firstname',
        generationtime: genTime,
        expiryYear: '\$yyyy'
    }
    const cvvData = {
        holderName: '\$lastnames \$firstname',
        generationtime: genTime,
        cvc: '\$cvc'
    }
    const encCCData = adyenEncrypt.createEncryption(adyenKey, options).encrypt(ccData)
    const encMMData = adyenEncrypt.createEncryption(adyenKey, options).encrypt(mmData)
    const encYYYYData = adyenEncrypt.createEncryption(adyenKey, options).encrypt(yyyyData)
    const encCVVData = adyenEncrypt.createEncryption(adyenKey, options).encrypt(cvvData)
    console.log('EncData Credit Card Number:', encCCData)
    console.log('Encrypted Expiry Month:', encMMData)
    console.log('Encrypted Expiry Year:', encYYYYData)
    console.log('Encrypted CVV:', encCVVData)
";

// Generate a random filename for the Node.js script
$adyen_script = generateRandomString(6) . '.js';

// Write the script content to a file
file_put_contents($adyen_script, $script_content);

// Execute the Node.js script and capture the output
$output = shell_exec('node ' . $adyen_script);

// Remove the temporary Node.js script file
unlink($adyen_script);

// Parse the output to get individual encrypted values
list($encCC, $encMM, $encYYYY, $encCVV) = explode("\n", $output);

// Data to be sent to the API
$data = array(
    'encCC' => trim($encCC),
    'encMM' => trim($encMM),
    'encYYYY' => trim($encYYYY),
    'encCVV' => trim($encCVV),
    'adyenKey' => $_POST['adyenKey'], // Adyen Key provided by user in API request
    'adyenVersion' => $_POST['adyenVersion'] // Adyen Version provided by user in API request
);

// API endpoint URL
$api_url = 'YOUR_RAILWAY_APP_API_ENDPOINT';

// Initialize cURL session
$ch = curl_init($api_url);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

// Execute cURL session
$response = curl_exec($ch);

// Close cURL session
curl_close($ch);

// Handle the response
echo $response;
?>
