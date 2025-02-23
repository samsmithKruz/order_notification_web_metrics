<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API Key & Script Generator</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen py-4">
    <div class="bg-white p-8 rounded-lg shadow-lg max-w-lg w-full text-center">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Generate API Key</h2>
        <p class="text-gray-600 text-sm mb-4">Click the button below to generate your unique API key.</p>
        <div class="block mb-2">
            <input id="url" type="url" class="w-full px-4 py-2 border rounded-lg text-gray-700 bg-gray-100" placeholder="Enter your website url">
        </div>
        <div class="relative">
            <input id="apiKey" type="text" class="w-full px-4 py-2 border rounded-lg text-gray-700 bg-gray-100" readonly placeholder="Click to generate API key">
            <button onclick="generateApiKey()" class="absolute top-1/2 right-2 transform -translate-y-1/2 bg-blue-600 text-white px-4 py-1 rounded hover:bg-blue-700 transition">Generate</button>
        </div>
        <button id="saveApiKeyBtn" onclick="saveApiKey()" class="mt-4 bg-green-600 text-white px-6 py-2 rounded hover:bg-green-700 transition hidden">Save API Key</button>

        <h2 class="text-2xl font-semibold text-gray-800 mt-6 mb-4">Copy Script URL</h2>
        <p class="text-gray-600 text-sm mb-4">Use the script below to track site metrics.</p>

        <div class="relative">
            <input id="scriptUrl" type="text" class="w-full px-4 py-2 border rounded-lg text-gray-700 bg-gray-100" readonly value='<script id="order_metrics_tracker" src="<?= DOMAIN; ?>/tracker.js"></script>'>
            <button onclick="copyScript()" class="absolute top-1/2 right-2 transform -translate-y-1/2 bg-green-600 text-white px-4 py-1 rounded hover:bg-green-700 transition">Copy</button>
        </div>

        <!-- How to Use Section -->
        <div class="bg-white mt-8 text-left max-w-md w-full">
            <h3 class="text-2xl font-semibold text-gray-800 mb-4">How to Use</h3>
            <ol class="list-decimal list-inside text-gray-700 space-y-4">
                <li>
                    <strong>Generate your API Key</strong> - Click the "Generate" button above to create a unique API key that will be used to authenticate your requests.
                </li>
                <li>
                    <strong>Copy the Script URL</strong> - Copy the provided script URL by clicking the "Copy" button. This script will track your site's metrics (page views, visitors, etc.).
                </li>
                <li>
                    <strong>Embed the Script on Your Website</strong> - Paste the copied script into the `

                    <head>` or `

                    <body>` section of your website's HTML. This will start tracking your site's metrics immediately.
                </li>
                <li>
                    <strong>Use the API Key</strong> - The API key you generated can be used to authenticate requests for accessing your metrics data.
                </li>
            </ol>
        </div>
    </div>

    <script>
        function generateApiKey() {
            const apiKey = 'sk-' + Math.random().toString(36).substr(2, 16);
            document.getElementById('apiKey').value = apiKey;
            document.getElementById('saveApiKeyBtn').classList.remove('hidden'); // Show the save button
        }

        function copyScript() {
            const scriptInput = document.getElementById('scriptUrl');
            scriptInput.select();
            scriptInput.setSelectionRange(0, 99999);
            document.execCommand("copy");

            // Toastify success notification
            Toastify({
                text: "Script copied to clipboard!",
                backgroundColor: "green",
                close: true,
                gravity: "top",
                position: "center",
                duration: 3000,
                stopOnFocus: true
            }).showToast();
        }

        async function saveApiKey() {
            const apiKey = document.getElementById('apiKey').value;
            const urlInput = document.getElementById('url').value;

            // Check if API key is provided
            if (!apiKey) {
                showToast("Please generate an API key first.", "red");
                return;
            }

            // Validate URL format
            let url;
            try {
                url = new URL(urlInput);
            } catch (_) {
                showToast("Please enter a valid URL.", "red");
                return;
            }

            // Prepare request body
            const requestBody = {
                apiKey,
                url: url.host
            };

            try {
                const response = await fetch('/api/key', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestBody),
                });

                if (response.ok) {
                    showToast("API key saved successfully!", "green");
                } else {
                    const data = await response.json();
                    throw new Error(data.message || 'Failed to save API key');
                }
            } catch (error) {
                showToast(error.message, "red");
            }
        }

        // Helper function to show toast notifications
        function showToast(message, backgroundColor) {
            Toastify({
                text: message,
                backgroundColor: backgroundColor,
                close: true,
                gravity: "top",
                position: "center",
                duration: 3000,
                stopOnFocus: true
            }).showToast();
        }
    </script>
</body>

</html>