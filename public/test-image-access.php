<?php
// Test direct file access
$imagePath = 'C:\xampp\htdocs\ecommerce\public\storage\products\smartphone-pro-max-128gb-1.jpg';

echo "<h1>Image Access Test</h1>";

if (file_exists($imagePath)) {
    echo "<p style='color: green;'>✓ Image file exists at: $imagePath</p>";
    echo "<p>File size: " . filesize($imagePath) . " bytes</p>";
    echo "<p>File permissions: " . substr(sprintf('%o', fileperms($imagePath)), -4) . "</p>";
} else {
    echo "<p style='color: red;'>✗ Image file does NOT exist at: $imagePath</p>";
}

echo "<hr>";
echo "<h2>Test Image Display</h2>";
echo "<img src='/ecommerce/storage/products/smartphone-pro-max-128gb-1.jpg' style='max-width: 300px; border: 1px solid #ccc;' alt='Test Image'>";

echo "<hr>";
echo "<h2>Available Images</h2>";
$files = glob('C:\xampp\htdocs\ecommerce\public\storage\products\*.jpg');
foreach($files as $file) {
    $filename = basename($file);
    echo "<p>$filename</p>";
}

echo "<hr>";
echo "<h2>Server Info</h2>";
echo "<p>Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Request URI: " . $_SERVER['REQUEST_URI'] . "</p>";
echo "<p>Script Name: " . $_SERVER['SCRIPT_NAME'] . "</p>";
?>