<template >
    <div>
    <v-app-bar app>
            <v-toolbar-title>Hello {{ this.$auth.user.data.first_name }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn class="primary ml-3" @click="home">Home</v-btn>
            <v-btn class="primary ml-3" @click="logout">Logout</v-btn>
        </v-app-bar>
        <v-container style="background-color: #D7D7D7;">
        <v-row>
            <v-col v-for="order in orders" :key="order.payment_purchased_id" cols="12">
                <v-card>
                    <v-card-title>
                          {{ order.product.title}}
                    </v-card-title>
                    <v-card-subtitle>
                          Description:- {{ order.product.description}}
                    </v-card-subtitle>

                    <!-- <v-card-subtitle>
                        Transaction ID: {{ order }}
                    </v-card-subtitle>
                    <v-list dense>
                        <v-list-item-group v-if="order.product">
                            <v-list-item v-for="(prod, index) in order.product" :key="index">
                                <v-list-item-content>
                                    <v-list-item-title>
                                        {{ product.title }}
                                    </v-list-item-title>
                                    <v-list-item-subtitle>
                                        Quantity: {{ product.quantity }}
                                    </v-list-item-subtitle>
                                    <v-list-item-subtitle>
                                        Price: {{ product.price }}
                                    </v-list-item-subtitle>
                                </v-list-item-content>
                            </v-list-item>
                        </v-list-item-group>
                    </v-list> --> 
                    <v-divider></v-divider>
                    <v-card-subtitle style="color:black;">
                          Order ID:- {{ order.payment_purchased_id}}
                    </v-card-subtitle>
                    <v-card-subtitle class="text">
                        Total Amount: {{ order.amount }}
                    </v-card-subtitle>

                    <v-card-subtitle class="text">
                        Payment Method: {{ order.payment_method }}
                    </v-card-subtitle>
                    <v-card-subtitle class="text" style="font-size: 1.1rem;">
                        Order Date: {{ order.created_at }}
                    </v-card-subtitle>
                </v-card>
            </v-col>
        </v-row>
    </v-container></div>
</template>

<script>
export default {
    data() {
        return {
            orders: [],

        }
    },
    mounted() {
        this.purchases();

    },

    methods: {
        async purchases() {
            try {
                const res = await this.$axios.get(`/api/purchases`, { params: { id:this.$auth.user.data.user_id } });
                this.orders = res.data.products;
                console.log(this.orders)
            } catch (error) {
                console.error(error);
            }
        },
        home(){
            this.$router.push(`/userDashboard`);

        }

    }
}
</script>