<!-- ProductDetails.vue - Detailed Product Page -->

<template app>
    <div>
        <v-card>
            <v-card-title>{{ product.title }}</v-card-title>
            <v-card-subtitle>{{ product.category }}</v-card-subtitle>
            <v-card-text>
                <p>{{ product.description }}</p>
                <p>Stock Count: {{ product.stock_count }}</p>
                <p>Price: {{ product.price }}</p>
                <p>Selling Price: {{ product.selling_price }}</p>
            </v-card-text>
            <v-card-actions>
                <v-btn @click="addToCart">Add to Cart</v-btn>
                <button @click="handleBuyNow(product.selling_price)">Buy Now</button>
            </v-card-actions>
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
                const response = await this.$axios.get(`api/product/${productId}`);
                this.product = response.data.product;
                console.log(response);
            } catch (error) {
                console.log(error);
            }
        },
        addToCart() {
            // Add your logic here to handle adding the product to the cart
            alert('Product added to cart!');
        },
        handleBuyNow(productSellingPrice) {
            this.$axios.post('/api/razorpay/create-order', { amount: productSellingPrice })
                .then(response => {
                    const orderId = response.data.order;
                    const options = {
                        key: 'rzp_test_SLNQ86ADUNPn5L',
                        amount: productSellingPrice * 100,
                        currency: 'INR',
                        name: 'Vaibahv Mishra',
                        description: 'Product Purchase',
                        order_id: orderId,

                        handler: (response) => {
                            console.log(response);
                            this.$axios.post('/api/razorpay/capture-payment', { payment_id: response.razorpay_payment_id })
                                .then(captureResponse => {
                                    console.log(captureResponse);
                                    if(captureResponse.razorpay_order_id){
                                        this.$router.push("/userDashboard");
                                    }
                                })
                                .catch(error => {
                                    console.error('Error capturing payment:', error);
                                });
                        },
                    };

                    const rzp = new Razorpay(options);
                    rzp.open();
                })
                .catch(error => {
                    console.alert('Error creating order:', error);
                });
        },

    },
}

</script>
