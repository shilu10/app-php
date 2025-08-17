<?php

if (!function_exists('e')) {
    /**
     * Escape HTML special characters to prevent XSS
     */
    function e($value): string
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
    }
}
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Profile - <?= e($currentUserDetails['name'] ?? $currentUserDetails['email']) ?></title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css">
</head>
<body class="bg-gray-100 min-h-screen">
  <nav class="bg-blue-600 text-white p-4">
    <div class="container mx-auto flex justify-between items-center">
      <div class="font-semibold">Workopia</div>
      <div class="space-x-4">
        <a href="/" class="hover:underline">Home</a>
        <a href="/users/logout" class="hover:underline">Logout</a>
      </div>
    </div>
  </nav>

  <main class="container mx-auto mt-10">
    <div class="max-w-2xl mx-auto bg-white rounded shadow p-6">
      <h1 class="text-2xl font-bold mb-4">Profile</h1>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <label class="text-sm text-gray-600">Name</label>
          <div class="mt-1 text-lg font-medium"><?= e($currentUserDetails['name'] ?? '') ?></div>
        </div>

        <div>
          <label class="text-sm text-gray-600">Email</label>
          <div class="mt-1 text-lg"><?= e($currentUserDetails['email'] ?? '') ?></div>
        </div>

        <div>
          <label class="text-sm text-gray-600">City</label>
          <div class="mt-1"><?= e($currentUserDetails['city'] ?? '') ?></div>
        </div>

        <div>
          <label class="text-sm text-gray-600">State</label>
          <div class="mt-1"><?= e($currentUserDetails['state'] ?? '') ?></div>
        </div>

      

      <div class="mt-6 flex space-x-3">
        <a href="/users/edit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Edit Profile</a>
        <a href="/users/logout" class="px-4 py-2 border rounded hover:bg-gray-50">Logout</a>
      </div>
    </div>
  </main>
</body>
</html>