<template>
    <div>
        <v-app-bar app>
            <v-toolbar-title>Hello {{ this.$auth.user.data.first_name }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn class="primary" @click.native.stop="addProductDialog = true">
                +Create Product
            </v-btn>
            <v-btn class="primary ml-3" @click="logout">Logout</v-btn>
            <div>
                <input class="mx-3" type="file" ref="fileInput" @change="handleFileChange" />
                <v-btn @click="uploadFile">Import</v-btn>
            </div>
        </v-app-bar>
        <v-app-bar class="mb-2">
            <v-btn class="primary ml-3" @click="viewSoldProducts">View Sold Products</v-btn>
            <v-btn class="primary ml-3" @click="viewTopSellingProducts">View Top Selling Products</v-btn>
            <v-btn class="primary ml-3" @click="viewPurchaseHistory">View Purchase History</v-btn>
        </v-app-bar>

        <v-layout row justify-end>
            <!-- confirm dialog -->
            <!-- dialog for add a new task -->
            <v-dialog v-model="addProductDialog" max-width="500">
                <v-card class="p-5">
                    <v-card-title>Add a Product</v-card-title>
                    <v-card-text class="pa-2">
                        <v-text-field v-model="product.title" label="Enter Title"></v-text-field>
                        <v-text-field v-model="product.description" label="Enter Description"></v-text-field>
                        <v-text-field v-model="product.stock_count" label="Stock Count" type="number"></v-text-field>
                        <v-text-field v-model="product.price" label="Price" type="number"></v-text-field>
                        <v-text-field v-model="product.selling_price" label="Selling Price" type="number"></v-text-field>
                        <v-select v-model="product.category" :items="categories" label="Select Category"></v-select>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="saveProduct" color="primary">Save</v-btn>
                        <v-btn @click="closeProductDialog">Close</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <!--------- edit a product----------- -->
            <v-dialog v-model="editProductDialog" max-width="500">
                <v-card class="p-5">
                    <v-card-title>Add a Product</v-card-title>
                    <v-card-text class="pa-2">
                        <v-text-field v-model="product.title" label="Enter Title"></v-text-field>
                        <v-text-field v-model="product.description" label="Enter Description"></v-text-field>
                        <v-text-field v-model="product.stock_count" label="Stock Count" type="number"></v-text-field>
                        <v-text-field v-model="product.price" label="Price" type="number"></v-text-field>
                        <v-text-field v-model="product.selling_price" label="Selling Price" type="number"></v-text-field>
                        <v-select v-model="product.category" :items="categories" label="Select Category"></v-select>
                    </v-card-text>
                    <v-card-actions>
                        <v-btn @click="updateProduct" :loading="loading" color="primary">Save</v-btn>
                        <v-btn @click="closeProductDialog">Close</v-btn>
                    </v-card-actions>
                </v-card>
            </v-dialog>

            <v-container>
                <v-row>
                    <v-col v-for="product in products" :key="product.id" cols="12" md="4">
                        <v-card>
                            <v-card-title>{{ product.title }}</v-card-title>
                            <v-card-subtitle>{{ product.category }}</v-card-subtitle>
                            <v-card-text>
                                <p>{{ product.description }}</p>
                                <p>Stock Count: {{ product.stock_count }}</p>
                                <p>Price: {{ product.price }}</p>
                                <p>Selling Price: {{ product.selling_price }}</p>
                            </v-card-text>
                            <v-checkbox class="label-set" height="50px" type="checkbox"
                                label="Mark as Out of stock" @click="updateStockStatus(product.product_id)"></v-checkbox>
                            <v-card-actions>
                                <v-btn @click="toggleProductStatus(product)" v-if="product.is_active == 0">Active</v-btn>
                                <v-btn @click="toggleProductStatus(product)" v-else>Inactive</v-btn>
                                <v-btn class="primary" @click="saveEditProduct(product)">
                                    Edit
                                </v-btn>
                                <v-btn class="primary" @click="deleteProduct(product.product_id)">
                                    Delete
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-col>
                </v-row>
            </v-container>
        </v-layout>
        <v-pagination v-if="totalPages > 1" v-model="currentPageProducts" :length="totalPages" @input="fetchProducts"></v-pagination>
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
            products: [],
            addProductDialog: false,
            editProductDialog: false,
            editProductId: "",
            snackbar: false,
            snackbarText: "",
            loading: true,
            currentPageProducts: 1,
            itemsPerPage: 6,
            product: {
                title: "",
                description: "",
                stock_count: null,
                price: null,
                selling_price: null,
                category: null,
                user_id: this.$auth.user.id,
            },
            categories: ['Electronics', 'Apparel', 'Footwear'],
        };
    },
    mounted() {
        this.fetchProducts();
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

        viewSoldProducts() {
            this.$router.push('/soldProducts');

        },

        async viewTopSellingProducts() {
            const endDate = new Date();
            const startDate = new Date();
            startDate.setDate(endDate.getDate() - 30);

            try {
                const response = await this.$axios.get('api/top-selling-products', {
                    params: { start_date: startDate.toISOString(), end_date: endDate.toISOString() },
                });

                console.log('Top Selling Products:', response.data.products);

                this.products = response.data.products;
            } catch (error) {
                console.error(error);
            }
        },

        viewPurchaseHistory() {
            this.$router.push('/purchaseHistory')

        },



        async saveProduct() {
            try {
                const response = await this.$axios.post("/api/product", {
                    title: this.product.title,
                    description: this.product.description,
                    stock_count: this.product.stock_count,
                    price: this.product.price,
                    selling_price: this.product.selling_price,
                    category: this.product.category,
                    created_by: this.$auth.user.data.user_id,
                });
                this.snackbarText = "you Product has been added";
                this.snackbar = true;
                console.log(response);
                this.fetchProducts();
            } catch (e) {
                console.log(e);
            }
            this.closeProductDialog();
        },

        async fetchProducts() {
            try {
                const response = await this.$axios.get('api/product?pages=1&perPage=6');
                this.products = response.data.product;
                console.log(response.data.product);

            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }
        },
        async toggleProductStatus(product) {
            try {
                product.is_active = !product.is_active;
                await this.$axios.patch(`api/product-status`, {
                    id: product.product_id
                });
                this.snackbarText = `Product status toggled successfully for ${product.title}`;
                this.snackbar = true;
            } catch (error) {
                console.error(error);
            }
        },
        closeProductDialog() {
            this.addProductDialog = false;
            this.editProductDialog = false;
            this.product = {
                title: "",
                description: "",
                stock_count: null,
                price: null,
                selling_price: null,
                category: null,
            };
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

        saveEditProduct(product) {
            console.log(product);
            this.editProductId = product.product_id;
            this.product.title = product.title;
            this.product.description = product.description;
            this.product.stock_count = product.stock_count;
            this.product.price = product.price;
            this.product.selling_price = product.selling_price;
            this.product.category = product.category;
            this.editProductDialog = true;
        },

        async updateProduct() {
            const updatedValues = {
                title: this.product.title,
                description: this.product.description,
                stock_count: this.product.stock_count,
                price: this.product.price,
                selling_price: this.product.selling_price,
                category: this.product.category,
            };
            console.log(updatedValues);

            try {
                this.loading = true;
                const res = await this.$axios.patch(
                    `/api/product-update/${this.editProductId}`,
                    updatedValues
                );
                console.log(res);
                this.editProductDialog = false;
                this.fetchProducts();
            } catch (e) {
                console.log(e);
            } finally {
                this.loading = false;
            }
        },

        async deleteProduct(productId) {
            try {
                const res = await this.$axios.delete(`/api/delete-product`, { params: { id: productId } });
                console.log(res);
                this.snackbarText = res.data.message;
                this.snackbar = true;
                this.fetchProducts();
            } catch (error) {
                console.log(error);
                alert(error);
            } finally {
            }
        },

        async updateStockStatus(productId) {
            try {
                const res = await this.$axios.patch(`/api/product-stock-status/`, { params: { id: productId } });
                this.snackbarText = "Update out of stock status";
                this.snackbar = true;
                this.fetchProducts();
            } catch (e) {
                console.log(e);
            };
        },
        handleFileChange(event) {
            this.selectedFile = event.target.files[0];
        },
        uploadFile() {
            if (!this.selectedFile) {
                alert("Please select a file");
                return;
            }
            this.handleFileUpload(this.selectedFile);
        },
        handleFileUpload(file) {
            console.log(file);
            const formData = new FormData();
            formData.append("file", file);
            console.log(formData);
            this.$axios
                .post("/api/upload", formData)
                .then((response) => {
                    console.log("File uploaded successfully");
                    this.selectedFile = "";
                    this.snackbarText = "your product has been uploaded successfully";
                    this.snackbar = true;
                    this.fetchTasks();
                })
                .catch((error) => {
                    console.error("Error uploading file", error);
                });
        },
    },
};
</script>