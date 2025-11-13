<?php
// Simple test to verify JavaScript template literals are working
$pageTitle = 'JavaScript Test';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JavaScript Template Literal Test</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1>JavaScript Template Literal Test</h1>
        
        <div class="card">
            <div class="card-body">
                <h5>Test Results:</h5>
                <div id="testResults"></div>
                
                <button class="btn btn-primary mt-3" onclick="testTemplateStrings()">
                    Run Template String Test
                </button>
                
                <button class="btn btn-success mt-3" onclick="testBargainModal('Test Product', 99.99)">
                    Test Bargain Modal Function
                </button>
            </div>
        </div>
    </div>

    <?php
    $additionalJS = "
    <script>
    function testTemplateStrings() {
        const productName = 'Test Product with \"quotes\" and \\'apostrophes\\'';
        const price = 99.99;
        
        const result = \`Product: \${productName}, Price: \$\${price.toFixed(2)}\`;
        
        document.getElementById('testResults').innerHTML = \`
            <div class='alert alert-success'>
                <h6>✅ Template Literals Working!</h6>
                <p>Result: \${result}</p>
            </div>
        \`;
    }
    
    function testBargainModal(productName, price) {
        const testDiv = document.createElement('div');
        testDiv.innerHTML = \`
            <div class='alert alert-info'>
                <h6>✅ Bargain Modal Function Test</h6>
                <p>Product: \${productName}</p>
                <p>Price: \$\${price.toFixed(2)}</p>
                <p>Discount (20%): \$\${(price * 0.8).toFixed(2)}</p>
            </div>
        \`;
        
        document.getElementById('testResults').appendChild(testDiv);
    }
    </script>
    ";
    
    echo $additionalJS;
    ?>
</body>
</html>
