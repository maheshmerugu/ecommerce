<!DOCTYPE html>
<html>
<head>
    <title>Image URL Test</title>
</head>
<body>
    <h1>Testing Image URLs</h1>
    
    <h2>Method 1: Direct Path</h2>
    <p>URL: http://localhost/ecommerce/storage/products/smartphone-pro-max-128gb-1.svg</p>
    <img src="http://localhost/ecommerce/storage/products/smartphone-pro-max-128gb-1.svg" alt="Test 1" style="max-width: 300px; border: 1px solid red;">
    
    <h2>Method 2: Relative Path</h2>
    <p>URL: /ecommerce/storage/products/smartphone-pro-max-128gb-1.svg</p>
    <img src="/ecommerce/storage/products/smartphone-pro-max-128gb-1.svg" alt="Test 2" style="max-width: 300px; border: 1px solid blue;">
    
    <h2>Method 3: Laravel Asset Function</h2>
    <p>URL: {{ asset('storage/products/smartphone-pro-max-128gb-1.svg') }}</p>
    <img src="{{ asset('storage/products/smartphone-pro-max-128gb-1.svg') }}" alt="Test 3" style="max-width: 300px; border: 1px solid green;">
    
    <hr>
    
    <h2>Debug Information</h2>
    <p><strong>APP_URL:</strong> {{ config('app.url') }}</p>
    <p><strong>Asset URL:</strong> {{ asset('storage/products/smartphone-pro-max-128gb-1.svg') }}</p>
    
    <h3>Check if file exists</h3>
    <?php
        $filePath = public_path('storage/products/smartphone-pro-max-128gb-1.svg');
        echo "<p>File path: $filePath</p>";
        echo "<p>File exists: " . (file_exists($filePath) ? 'YES' : 'NO') . "</p>";
        
        if (file_exists($filePath)) {
            echo "<p>File size: " . filesize($filePath) . " bytes</p>";
        }
        
        // List all files in storage/products
        $productDir = public_path('storage/products');
        if (is_dir($productDir)) {
            $files = scandir($productDir);
            echo "<h4>Files in storage/products:</h4><ul>";
            foreach ($files as $file) {
                if ($file != '.' && $file != '..') {
                    echo "<li>$file</li>";
                }
            }
            echo "</ul>";
        }
    ?>
</body>
</html>