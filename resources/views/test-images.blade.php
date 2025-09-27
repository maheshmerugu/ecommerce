<!DOCTYPE html>
<html>
<head>
    <title>Image Test</title>
</head>
<body>
    <h1>Testing Product Images</h1>
    
    <h2>Test 1: Direct Storage Access</h2>
    <img src="/ecommerce/storage/products/smartphone-pro-max-128gb-1.jpg" alt="Test 1" style="max-width: 200px;">
    
    <h2>Test 2: Laravel Asset Helper</h2>
    <img src="{{ asset('storage/products/smartphone-pro-max-128gb-1.jpg') }}" alt="Test 2" style="max-width: 200px;">
    
    <h2>Test 3: Storage URL Helper</h2>
    <img src="{{ Storage::url('products/smartphone-pro-max-128gb-1.jpg') }}" alt="Test 3" style="max-width: 200px;">
    
    <hr>
    <p><strong>Expected URLs:</strong></p>
    <ul>
        <li>Direct: http://localhost/ecommerce/storage/products/smartphone-pro-max-128gb-1.jpg</li>
        <li>Asset: {{ asset('storage/products/smartphone-pro-max-128gb-1.jpg') }}</li>
        <li>Storage: {{ Storage::url('products/smartphone-pro-max-128gb-1.jpg') }}</li>
    </ul>
</body>
</html>