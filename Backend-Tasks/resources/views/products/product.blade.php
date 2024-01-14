<!-- resources/views/products/product.blade.php -->
<html>
<head>
    <!-- Add Razorpay JS library -->
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <h1>Product Page</h1>
    <!-- Your product details and UI elements go here -->

    <!-- Razorpay Buy Now button or form -->
    <div id="app">
        <button @click="buyNow">Buy Now</button>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/vue@2"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script> <!-- Include jQuery -->

    <script>
        new Vue({
            el: '#app',
            methods: {
                buyNow: function() {
                    // Make an AJAX request to create the order
                    $.post('/razorpay/create-order', { amount: /* Your product amount */ }, function(response) {
                        // Initialize Razorpay checkout
                        var options = {
                            key: /* Your Razorpay Key */,
                            amount: response.order.amount,
                            currency: 'INR',
                            order_id: response.order.id,
                            name: /* Your Company Name */,
                            description: /* Product Description */,
                            handler: function(response) {
                                // Make an AJAX request to capture the payment
                                $.post('/razorpay/capture-payment', { payment_id: response.razorpay_payment_id }, function(capturedPayment) {
                                    // Handle the success response (e.g., show a success message)
                                });
                            },
                        };

                        var rzp = new Razorpay(options);
                        rzp.open();
                    });
                }
            }
        });
    </script>
</body>
</html>
