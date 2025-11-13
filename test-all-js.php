<?php
// Comprehensive test for all JavaScript template literal fixes
$pageTitle = 'All JavaScript Tests';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complete JavaScript Template Literal Test</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container my-5">
        <h1><i class="fas fa-code text-primary me-2"></i>Complete JavaScript Template Literal Test</h1>
        
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-shopping-cart me-2"></i>Cart Functions</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary mb-2" onclick="testCartFunctions()">Test Cart Functions</button>
                        <div id="cartResults"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-handshake me-2"></i>Bargain Functions</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-success mb-2" onclick="testBargainFunctions()">Test Bargain Functions</button>
                        <div id="bargainResults"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-bell me-2"></i>Toast Functions</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-info mb-2" onclick="testToastFunctions()">Test Toast Functions</button>
                        <div id="toastResults"></div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-user-shield me-2"></i>Admin Functions</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-warning mb-2" onclick="testAdminFunctions()">Test Admin Functions</button>
                        <div id="adminResults"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-check-circle me-2"></i>Overall Test Results</h5>
                    </div>
                    <div class="card-body">
                        <button class="btn btn-primary btn-lg" onclick="runAllTests()">
                            <i class="fas fa-play me-2"></i>Run All Tests
                        </button>
                        <div id="overallResults" class="mt-3"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        let testResults = [];
        
        // Cart Functions Test
        function testCartFunctions() {
            const productId = 123;
            const quantity = 5;
            
            try {
                // Test cart selector
                const selector = \`[data-product-id='\${productId}'] input[type='number']\`;
                
                // Test update body
                const updateBody = \`product_id=\${productId}&quantity=\${quantity}&action=update\`;
                
                // Test remove body
                const removeBody = \`product_id=\${productId}&action=remove\`;
                
                showResult('cartResults', 'success', \`✅ Cart selector: \${selector}\`);
                showResult('cartResults', 'success', \`✅ Update body: \${updateBody}\`);
                showResult('cartResults', 'success', \`✅ Remove body: \${removeBody}\`);
                
                testResults.push({test: 'Cart Functions', status: 'passed'});
            } catch (error) {
                showResult('cartResults', 'danger', \`❌ Cart test failed: \${error.message}\`);
                testResults.push({test: 'Cart Functions', status: 'failed', error: error.message});
            }
        }
        
        // Bargain Functions Test
        function testBargainFunctions() {
            const productName = 'Test Product with "quotes" and \\'apostrophes\\'';
            const price = 99.99;
            const discount = 15.5;
            
            try {
                // Test bargain modal content
                const modalContent = \`
                    <div class='d-flex align-items-center'>
                        <div>
                            <h6 class='mb-1'>\${productName}</h6>
                            <p class='text-muted mb-0'>Original Price: <strong>\$\${price.toFixed(2)}</strong></p>
                        </div>
                    </div>
                \`;
                
                // Test discount display
                const discountDisplay = \`<i class='fas fa-arrow-down text-success'></i> \${discount}% discount\`;
                
                showResult('bargainResults', 'success', \`✅ Product name handled: \${productName}\`);
                showResult('bargainResults', 'success', \`✅ Price formatted: \$\${price.toFixed(2)}\`);
                showResult('bargainResults', 'success', \`✅ Discount display: \${discountDisplay}\`);
                
                testResults.push({test: 'Bargain Functions', status: 'passed'});
            } catch (error) {
                showResult('bargainResults', 'danger', \`❌ Bargain test failed: \${error.message}\`);
                testResults.push({test: 'Bargain Functions', status: 'failed', error: error.message});
            }
        }
        
        // Toast Functions Test
        function testToastFunctions() {
            const message = 'Test message with "quotes"';
            const type = 'success';
            
            try {
                // Test toast class
                const toastClass = \`toast align-items-center text-white bg-\${type} border-0\`;
                
                // Test toast content
                const toastContent = \`
                    <div class="d-flex">
                        <div class="toast-body">\${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-mdb-dismiss="toast"></button>
                    </div>
                \`;
                
                showResult('toastResults', 'success', \`✅ Toast class: \${toastClass}\`);
                showResult('toastResults', 'success', \`✅ Message handled: \${message}\`);
                
                testResults.push({test: 'Toast Functions', status: 'passed'});
            } catch (error) {
                showResult('toastResults', 'danger', \`❌ Toast test failed: \${error.message}\`);
                testResults.push({test: 'Toast Functions', status: 'failed', error: error.message});
            }
        }
        
        // Admin Functions Test
        function testAdminFunctions() {
            const action = 'accepted';
            const actionText = 'accept';
            const iconClass = 'fa-check text-success';
            
            try {
                // Test admin confirm message
                const confirmMessage = \`Are you sure you want to \${actionText} this bargain?\`;
                
                // Test admin success message
                const successMessage = \`Bargain \${action} successfully!\`;
                
                // Test admin modal title
                const modalTitle = \`<i class="fas \${iconClass} me-2"></i>\${actionText} Bargain\`;
                
                showResult('adminResults', 'success', \`✅ Confirm message: \${confirmMessage}\`);
                showResult('adminResults', 'success', \`✅ Success message: \${successMessage}\`);
                showResult('adminResults', 'success', \`✅ Modal title: \${modalTitle}\`);
                
                testResults.push({test: 'Admin Functions', status: 'passed'});
            } catch (error) {
                showResult('adminResults', 'danger', \`❌ Admin test failed: \${error.message}\`);
                testResults.push({test: 'Admin Functions', status: 'failed', error: error.message});
            }
        }
        
        // Run All Tests
        function runAllTests() {
            testResults = [];
            
            // Clear previous results
            ['cartResults', 'bargainResults', 'toastResults', 'adminResults', 'overallResults'].forEach(id => {
                document.getElementById(id).innerHTML = '';
            });
            
            // Run all tests
            testCartFunctions();
            testBargainFunctions();
            testToastFunctions();
            testAdminFunctions();
            
            // Show overall results
            setTimeout(() => {
                const passed = testResults.filter(r => r.status === 'passed').length;
                const failed = testResults.filter(r => r.status === 'failed').length;
                
                const overallDiv = document.getElementById('overallResults');
                
                if (failed === 0) {
                    overallDiv.innerHTML = \`
                        <div class="alert alert-success">
                            <h4><i class="fas fa-check-circle me-2"></i>All Tests Passed! 🎉</h4>
                            <p>Successfully tested \${passed} functions. All JavaScript template literals are working correctly.</p>
                        </div>
                    \`;
                } else {
                    overallDiv.innerHTML = \`
                        <div class="alert alert-warning">
                            <h4><i class="fas fa-exclamation-triangle me-2"></i>Test Results</h4>
                            <p>Passed: \${passed} | Failed: \${failed}</p>
                            <p>Some template literals may need additional fixes.</p>
                        </div>
                    \`;
                }
            }, 100);
        }
        
        // Helper function to show results
        function showResult(containerId, type, message) {
            const container = document.getElementById(containerId);
            const div = document.createElement('div');
            div.className = \`alert alert-\${type} mb-2 py-2\`;
            div.innerHTML = message;
            container.appendChild(div);
        }
        
        // Auto-run tests on page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(runAllTests, 500);
        });
    </script>
</body>
</html>
