<template>
    <div>
        <v-app-bar app>
            <v-toolbar-title>Hello {{ this.$auth.user.data.first_name }}</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn class="primary" @click="backToDashboard">Back to Dashboard</v-btn>
        </v-app-bar>

        <v-layout row justify-end>


            <div class="card">
                <div class="card-inner">
                    <ul class="list">
                        <ul class="mx-5px">
                            <!-- <Task></Task> -->
                            <v-container id="table">
                                <v-data-table :loading="loading" :headers="headers" :items="products" :footer-props="{
                                    'items-per-page-options': [15, 30, 50, 100, -1],
                                }" class="elevation-1" :server-items-length="total" @update:page="handlePageUpdate"
                                    @update:items-per-page="handlePerPageUpdate" :sort-by.sync="sortBy"
                                    :sort-desc.sync="sortDesc" @update:sort-desc="fetchSoldProducts">
                                    <template #item.index="{ index }">
                                        {{ index + 1 }}
                                    </template>
                                    <template #item.created_at="{ item }">
                                        {{ $dayjs(item.created_at).format("YYYY/MM/DD") }}
                                    </template>

                                </v-data-table>
                            </v-container>
                        </ul>
                    </ul>
                </div>
            </div>
        </v-layout>
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
            sortBy: "created_at",
            sortDesc: true,
            snackbar: false,
            snackbarText: "",
            page: 1,
            perPage: 10,
            total: 0,
            products: [],
            loading: false,

            headers: [
                { text: "S. No.", value: "index", sortable: false },
                { text: "Title", value: "title", sortable: true },
                { text: "Description", value: "description", sortable: true },
                { text: "Quantity", value: "total_quantity", sortable: true },
                { text: "Created At", value: "created_at", sortable: true },
            ],
        };
    },
    mounted() {
        this.fetchSoldProducts();
    },
    methods: {
        handleSort() {
            console.log("asd");
        },
        handlePageUpdate(page) {
            this.page = page;
            this.fetchSoldProducts();
        },
        handlePerPageUpdate(perPage) {
            this.perPage = perPage;
            this.fetchSoldProducts();
        },

        async fetchSoldProducts() {
            try {
                this.loading = true;
                const { data } = await this.$axios.get(
                    `api/seller/sold-products?page=${this.page}&perPage=${this.perPage}&sortBy=${this.sortBy}&desc=${this.sortDesc}`
                );
                this.products = data.data.data;
                this.total = data.data.total;
            } catch (error) {
                console.log(error);
            } finally {
                this.loading = false;
            }

        },
        backToDashboard() {
            this.$router.push('/sellerDashboard');
        },
    },
};
</script>
  
<style scoped>
#table {
    margin-top: 2rem;
    margin-right: 33rem;
    background-color: #d7d7d7;
}

</style>
  