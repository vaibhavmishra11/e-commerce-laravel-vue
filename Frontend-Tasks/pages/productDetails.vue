<!-- ProductDetails.vue - Detailed Product Page -->

<template app>
    <div>
        <v-app-bar app>
            <v-toolbar-title>Hello {{ this.$auth.user.data.first_name }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn class="primary ml-3" @click="dashboard">Home</v-btn>
            <v-btn class="primary ml-3" @click="logout">Logout</v-btn>
        </v-app-bar>
        <v-card>
            <v-card-title>{{ product.title }}</v-card-title>
            <v-card-subtitle>{{ product.category }}</v-card-subtitle>
            <v-card-text>
                <p>{{ product.description }}</p>
                <p>Stock Count: {{ product.stock_count }}</p>
                <p>Price: {{ product.price }}</p>
                <p>Selling Price: {{ product.selling_price }}</p>
            </v-card-text>
        </v-card>
    </div>
</template>


<script>

export default {
    data() {
        return {
            product: {},
            productId: this.$route.params.id,
        };
    },
    mounted() {
        const script = document.createElement('script');
        script.src = 'https://checkout.razorpay.com/v1/checkout.js';
        document.head.appendChild(script);
        this.fetchProductDetails(this.productId);
    },
    methods: {
        async fetchProductDetails(productId) {
            try {
                console.log(this.$route.params.id);
                const response = await this.$axios.get(`/api/product-detail`, { params: { id: productId } });
                this.product = response.data.product;

            } catch (error) {
                console.log(error);
            }
        },
        dashboard() {
            this.$router.push('/userDashboard')
        },
        async logout() {
            try {
                this.snackbarText = "logged out successfully";
                this.snackbar = true;
                await this.$auth.logout();
                this.$router.push("/login");
            } catch (e) {
                console.log(e);
            }
        },
        sendPaymentIdToBackend(paymentId) {
            this.$axios.post('/api/razorpay/payment-failure', { payment_id: paymentId })
                .then(response => {
                    console.log('Payment ID sent to backend:', paymentId);
                })
                .catch(error => {
                    console.error('Error sending payment ID to backend:', error);
                });
        },
        handleBuyNow(product) {
            this.$axios.post('/api/razorpay/create-order', { amount: product.selling_price })
                .then(response => {
                    const orderId = response.data.order;
                    const options = {
                        key: 'rzp_test_pfzp7o8PLlLAVP',
                        amount: product.selling_price * 100, // Adjust amount to match Razorpay format (in paise)
                        currency: 'INR',
                        name: 'Vaibhav Mishra',
                        description: 'Product Purchase',
                        order_id: orderId,

                        handler: (response) => {
                            console.log('Razorpay Payment Response:', response);

                            if (response.razorpay_payment_id) {
                                // If payment is successful, capture the payment
                                this.$axios.post('/api/razorpay/capture-payment', {
                                    payment_id: response.razorpay_payment_id
                                    , productId: product.product_id,
                                    orderId: response.razorpay_order_id,

                                })
                                    .then(captureResponse => {
                                        console.log('Capture Payment Response:', captureResponse);

                                        if (captureResponse.data.payment) {
                                            console.log('Payment Captured Successfully!');
                                            this.$router.push("/userDashboard");
                                        } else {
                                            console.error('Error capturing payment:', captureResponse.data.error);
                                            this.handlePaymentFailure(response); // Call the failure handling method
                                        }
                                    })
                                    .catch(captureError => {
                                        console.error('Error capturing payment:', captureError);
                                        this.handlePaymentFailure(response); // Call the failure handling method
                                    });
                            } else {
                                console.error('Razorpay Payment failed:', response.error_description);
                                this.handlePaymentFailure(response); // Call the failure handling method
                            }
                        },
                    };
                    const rzp = new Razorpay(options);
                    rzp.open();
                })
                .catch(error => {
                    console.error('Error creating order:', error);
                    this.handlePaymentFailure(); // Call the failure handling method
                });
        },

        handlePaymentFailure(response) {
            const failureData = {
                payment_id: response ? response.razorpay_payment_id : null,
                order_id: response ? response.razorpay_order_id : null,
                code: response ? response.error.code : null,
                description: response ? response.error.description : null,
            };

            this.$axios.post("/api/razorpay/payment-failure", failureData)
                .then(function (response) {
                    // Show failed message or handle as needed
                    console.log('failed');
                })
                .catch(function (error) {
                    console.log('error');
                });
        },







    },
}

</script>
