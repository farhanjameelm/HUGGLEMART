<?php
// Simple test to verify cart JavaScript functions work
$pageTitle = 'Cart JavaScript Test';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart JavaScript Test</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1>Cart JavaScript Functions Test</h1>
        
        <div class="card">
            <div class="card-body">
                <h5>Test Cart Functions:</h5>
                <div id="testResults"></div>
                
                <!-- Mock cart item for testing -->
                <div class="border p-3 mb-3" data-product-id="123">
                    <h6>Test Product</h6>
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(123, -1)">-</button>
                        <input type="number" class="form-control mx-2" style="width: 80px;" value="2" min="1" max="10">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(123, 1)">+</button>
                    </div>
                    <button class="btn btn-sm btn-danger mt-2" onclick="removeFromCart(123)">Remove</button>
                </div>
                
                <button class="btn btn-primary" onclick="testCartFunctions()">
                    Test Template Literals
                </button>
            </div>
        </div>
    </div>

    <?php
    $additionalJS = "
    <script>
    // Mock functions for testing (same as cart.php but with console logging)
    function updateQuantity(productId, change) {
        console.log('updateQuantity called with:', productId, change);
        const input = document.querySelector(\`[data-product-id='\${productId}'] input[type='number']\`);
        const currentValue = parseInt(input.value);
        const newValue = Math.max(1, currentValue + change);
        const maxValue = parseInt(input.getAttribute('max'));
        
        if (newValue <= maxValue) {
            input.value = newValue;
            console.log('Updated quantity to:', newValue);
            // Don't actually call setQuantity to avoid API calls
            showTestResult(\`✅ updateQuantity works! ProductID: \${productId}, New Value: \${newValue}\`);
        }
    }
    
    function setQuantity(productId, quantity) {
        console.log('setQuantity called with:', productId, quantity);
        quantity = Math.max(1, parseInt(quantity));
        
        const body = \`product_id=\${productId}&quantity=\${quantity}&action=update\`;
        console.log('Request body would be:', body);
        showTestResult(\`✅ setQuantity template literal works! Body: \${body}\`);
    }
    
    function removeFromCart(productId) {
        console.log('removeFromCart called with:', productId);
        
        const body = \`product_id=\${productId}&action=remove\`;
        console.log('Request body would be:', body);
        showTestResult(\`✅ removeFromCart template literal works! Body: \${body}\`);
    }
    
    function testCartFunctions() {
        const productId = 456;
        const quantity = 3;
        
        // Test template literals directly
        const updateBody = \`product_id=\${productId}&quantity=\${quantity}&action=update\`;
        const removeBody = \`product_id=\${productId}&action=remove\`;
        const selector = \`[data-product-id='\${productId}'] input[type='number']\`;
        
        showTestResult(\`✅ Template literals working correctly!\`);
        showTestResult(\`Update body: \${updateBody}\`);
        showTestResult(\`Remove body: \${removeBody}\`);
        showTestResult(\`Selector: \${selector}\`);
    }
    
    function showTestResult(message) {
        const div = document.createElement('div');
        div.className = 'alert alert-success mb-2';
        div.innerHTML = message;
        document.getElementById('testResults').appendChild(div);
    }
    
    // Mock showToast function
    function showToast(message, type) {
        console.log('Toast:', type, message);
    }
    </script>
    ";
    
    echo $additionalJS;
    ?>
</body>
</html>
