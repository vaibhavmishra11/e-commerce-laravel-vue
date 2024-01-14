<template>
    <div>
        <v-app-bar app>
            <v-toolbar-title>Hello {{ this.$auth.user.data.first_name }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn class="primary  mr-2" @click="dashboard">
                Home
            </v-btn>
            <v-btn class="primary" @click ="orders">
                My orders
            </v-btn>
            <v-btn class="primary ml-3" @click="logout">Logout</v-btn>
        </v-app-bar>
        <v-card width="50%" v-if="totalItems !=0">
            <v-card-title>
                <v-list>
                    <v-list-item v-for="item in cartItems" :key="item.id">
                        <v-list-item-content>
                            <v-list-item-title>{{ item.product.title }}</v-list-item-title>
                            <v-list-item-subtitle>{{ item.product.description }}</v-list-item-subtitle>
                            <v-list-item-subtitle>Price: {{ item.product.price }}</v-list-item-subtitle>

                            <!-- Quantity controls -->
                            <v-row align="center" class="mt-2">
                                <v-btn icon @click="updateQuantity(item, 'decrement')">
                                    <v-icon small>mdi-minus</v-icon>
                                </v-btn>
                                <v-text-field v-model="item.quantity" class="mx-1 mt-1" outlined dense></v-text-field>
                                <v-btn icon @click="updateQuantity(item, 'increment')">
                                    <v-icon small>mdi-plus</v-icon>
                                </v-btn>
                            </v-row>
                        </v-list-item-content>
                        <v-list-item-action>
                            <v-btn @click="removeFromCart(item.product.product_id)" icon>
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </v-list-item-action>
                    </v-list-item>
                </v-list>
            </v-card-title>

            <!-- Total quantity, total price, and Buy Now button -->
            <v-card-actions class="justify-end">
                <v-row>
                    <v-col>
                        <div>Total Items: {{ totalItems }}</div>
                        <div>Total Price: {{ totalPrice }}</div>
                    </v-col>
                    <v-col>
                        <v-btn @click="handleBuyNow(product)" class="primary">Buy Now</v-btn>
                    </v-col>
                </v-row>
            </v-card-actions>
        </v-card>

        <v-card v-else="" style="background-color: #D7D7D7; width: 5rem; text-decoration: none;">
            <v-card>Your cart is empty.</v-card>
           
            <v-btn  text v-bind="attrs" @click=" dashboard" style="background-color: blueviolet;">
                    Please Select Product
            </v-btn>
        </v-card>
        <v-snackbar v-model="snackbar" :timeout="2000">
            {{ snackbarText }}
            <template v-slot:action="{ attrs }">
                <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">
                    Close
                </v-btn>
            </template>
        </v-snackbar>
    </div>
</template>
  
<script>
export default {
    data() {
        return {
            dialog: false,
            cartItems: [],
            cartCount: null,
            snackbar: false,
            snackbarText: "",

        };
    },
    computed: {
        // Calculate total quantity and total price
        totalItems() {
            return this.cartItems.reduce((total, item) => total + item.quantity, 0);
        },
        totalPrice() {
            return this.cartItems.reduce((total, item) => total + item.product.price * item.quantity, 0);
        },
    },
    mounted() {
        const script = document.createElement('script');
        script.src = 'https://checkout.razorpay.com/v1/checkout.js';
        document.head.appendChild(script);
        this.fetchUserCartItems();
    },
    methods: {

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
        async fetchUserCartItems() {
            try {
                const response = await this.$axios.get(`/api/cart-items`,{ params: { id:this.$auth.user.data.user_id } });
                this.cartItems = response.data.cartItems.map(item => ({ ...item, quantity: 1 }));
                console.log(response);
            } catch (error) {
                console.error(error);
            }
        },
        async removeFromCart(productId) {
            try {
                const response = await this.$axios.delete(`api/remove-from-cart/${productId}`);
                this.snackbarText = "cart item deleted successfully";
                this.snackbar = true;
                this.fetchUserCartItems();
            } catch (error) {
                console.log(error);
            }
        },

        updateQuantity(item, action) {
            if (action === 'increment') {
                item.quantity++;
            } else if (action === 'decrement' && item.quantity > 1) {
                item.quantity--;
            }
        },

        handleBuyNow(product) {
            const totalPrice = this.cartItems.reduce((total, item) => total + item.product.price * item.quantity, 0);
            const payload = {
                totalItems: this.totalItems,
                totalPrice: totalPrice,
                cartItems: this.cartItems.map(item => ({
                    productId: item.product.product_id,
                    quantity: item.quantity,
                    price: item.product.price,
                })),
            };
            this.$axios.post('/api/razorpay/create-order', { amount: totalPrice })
                .then(response => {
                    const orderId = response.data.order;
                    const options = {
                        key: 'rzp_test_HSMiyxVS1q5Gmw',
                        amount: totalPrice * 100, // Adjust amount to match Razorpay format (in paise)
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
                                    , productId: '9a8e2c37-80cf-4e8f-b686-683f19d531de',
                                    orderId: response.razorpay_order_id,
                                    orderItems:payload,
                                })
                                    .then(captureResponse => {
                                        console.log('Capture Payment Response:', captureResponse);

                                        if (captureResponse.data.payment) {
                                            console.log('Payment Captured Successfully!');
                                            this.saveOrderProducts(response.razorpay_payment_id,response.razorpay_order_id,payload);
                                            this.$router.push("/userDashboard");
                                        } else {
                                            console.error('Error capturing payment:', captureResponse.data.error);
                                        }
                                    })
                                    .catch(captureError => {
                                        console.error('Error capturing payment:', captureError);
                                    });
                            } else {
                                console.error('Razorpay Payment failed:', response.error_description);
                            }
                        },
                    };
                    const rzp = new Razorpay(options);
                    rzp.open();
                })
                .catch(error => {
                    console.error('Error creating order:', error);
               });
        },

        async saveOrderProducts(paymentId,orderId,payload){
            const res = await this.$axios.post('/api/save-order-data',{
                paymentId:paymentId,
                orderId : orderId,
                payload : payload,
            });
            console.log(res);
            
        },
        orders(){
            this.$router.push('/userOrdersHistory')
        }
    },
    
};
</script>
  

<style scoped>
.v-text-field {
    width: 100px;
}
</style>