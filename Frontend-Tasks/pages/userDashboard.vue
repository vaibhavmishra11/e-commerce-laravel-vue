<template>
    <div>
        <v-app-bar app>
            <v-toolbar-title>Hello {{ this.$auth.user.data.first_name }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <!-- <v-text-field v-model="title" label="Search" variant="outlined" @keydown.enter="fetchData"></v-text-field> -->
            <v-text-field v-model="title" label="Search" variant="outlined" @keydown.enter="fetchData"
                style="width: calc(20% - 8rem); margin-left: -40rem; margin-top: 1rem;"></v-text-field>
            <v-btn class="primary" @click="myOrder">
                My orders
            </v-btn>

            <v-btn class="primary ml-3" @click="showWallet">My Wallet</v-btn>
            <v-btn class="primary ml-3" @click="showCart">My cart ({{ this.cartCount }})</v-btn>
            <v-btn class="primary ml-3" @click="logout">Logout</v-btn>
        </v-app-bar>

        <v-layout row justify-end>
            <v-container>
                <v-row>
                    <v-col v-for="product in paginatedProducts" :key="product.id" cols="12" md="4">
                        <v-card color="#fff">
                            <v-card-title>{{ product.title }}</v-card-title>
                            <v-card-subtitle>{{ product.category }}</v-card-subtitle>
                            <v-card-text>
                                <p>{{ product.description }}</p>
                                <!-- <p>Stock Count: {{ product.stock_count }}</p> -->
                                <p>Price: {{ product.price }}</p>
                                <p>Selling Price: {{ product.selling_price }}</p>
                            </v-card-text>
                            <v-card-actions v-if="!product.out_of_stock">
                                <v-btn @click="addToCart(product.product_id)" class="info">Add to Cart</v-btn>
                                <!-- <v-btn class="primary ml-3" @click="buttonAction(product.product_id)">
                                    {{ buttonText }}
                                </v-btn> -->
                                <v-btn class="success" @click="viewProductDetails(product.product_id)">Checkout</v-btn>
                            </v-card-actions>
                            <v-card-actions v-else>
                                <div style="color:darkorange">Out of Stock</div>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-layout>
        <v-snackbar v-model="snackbar" :timeout="2000">
            {{ snackbarText }}
            <template v-slot:action="{ attrs }">
                <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">
                    Close
                </v-btn>
            </template>
        </v-snackbar>
        <v-pagination v-if="totalPages > 1" v-model="currentPage" :length="totalPages"
            @input="fetchAllProducts"></v-pagination>
        <!--  v-dialog for wallet details -->
        <v-dialog v-model="walletDialog" max-width="400">
            <v-card>
                <v-card-title>Wallet Details</v-card-title>
                <v-card-text>
                    <p>Balance: {{ walletBalance }}</p>

                </v-card-text>
                <v-card-actions>
                    <v-btn @click="closeWalletDialog">Close</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </div>
</template>

<script>

export default {
    data() {
        return {
            title: "",
            products: [],
            cartItem: [],
            cartCount: null,
            snackbar: false,
            snackbarText: "",
            buttonText: 'Add to Cart',
            currentPage: 1,
            itemsPerPage: 6,
            walletBalance: 0,
            walletDialog: false,
        }
    },
    mounted() {
        this.fetchAllCartItems();
        this.fetchAllProducts();
    },
    computed: {
        totalPages() {
            return Math.ceil(this.products.length / this.itemsPerPage);
        },
        paginatedProducts() {
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;
            return this.products.slice(startIndex, endIndex);
        },
    },
    methods: {
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

        async fetchAllProducts() {
            try {
                const response = await this.$axios.get(`api/products?pages=1&perPage=6`);
                this.products = response.data.products;

            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        viewProductDetails(productId) {
            this.$router.push({ name: 'productDetails', params: { id: productId } });
        },

        async addToCart(productId) {
            try {
                const response = await this.$axios.post(`api/add-to-cart/${productId}`);
                this.snackbarText = "Item has been added to cart";
                this.snackbar = true;
                this.buttonText = "Go to Cart";
                this.fetchAllCartItems();
            } catch (error) {
                console.log(error);
            }
        },
        showCart() {
            this.$router.push('/userCartItems');
        },

        // buttonAction(productId) {
        //     console.log("Button clicked for productId:", productId);
        //     const product = this.products.find(p => p.product_id === productId);
        //     if (product) {
        //         console.log("Found product:", product);

        //         this.buttonText = product.inCart ? "Go to Cart" : "Add to Cart";
        //         this.addToCart(productId);
        //     } else {
        //         console.log("Product not found");
        //     }
        // },
        async fetchAllCartItems() {
            try {
                const response = await this.$axios.get(`/api/cart-items`,{ params: { id:this.$auth.user.data.user_id } });
                this.cartCount = response.data.cartItems.length;
            } catch (error) {
                console.error(error);
            }
        },
        fetchData() {
            if (this.title.length != " ") {
                const filteredData = this.paginatedProducts.filter((item) =>
                    this.matchesSearchCriteria(item)
                );
                this.products = filteredData;
            }
            else {
                this.fetchAllProducts();
            }
        },

        matchesSearchCriteria(item) {
            const searchTitle = item.title.includes(this.title);
            return searchTitle;
        },
        myOrder() {
            this.$router.push(`/userOrdersHistory`);
        },

        async showWallet() {
            try {
                
                const res = await this.$axios.get(`/api/wallet/details`, { params: { id:this.$auth.user.data.user_id } });

                this.walletBalance = res.data.data.wallet_balance;
                this.walletDialog = true; // Show the wallet dialog
            } catch (error) {
                console.error(error);
            }
        },

        closeWalletDialog() {
            this.walletDialog = false; // Hide the wallet dialog
        },


    }
}
</script>
