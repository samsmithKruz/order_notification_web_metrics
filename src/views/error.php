<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?=$error_title??"404 - Page Not Found";?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex items-center justify-center h-screen">
    <div class="text-center">
        <h1 class="text-9xl font-bold text-gray-800"><?=$error_code??"404";?></h1>
        <p class="text-2xl font-semibold text-gray-600 mt-4"><?=$error_title??"Oops! Page not found.";?></p>
        <p class="text-gray-500 mt-2"><?=$error??"The page you are looking for might have been removed or does not exist.";?></p>
        <a href="/" class="mt-6 inline-block bg-blue-600 text-white px-6 py-3 rounded-lg shadow hover:bg-blue-700 transition">
            Go Home
        </a>
        
    </div>
</body>
</html>
