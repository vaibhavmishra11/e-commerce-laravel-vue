<template>
  <nuxt-link to="/dashboard">dashboard</nuxt-link>
</template>
<template>
  <section class="section">
    <div class="container">
      <h1 class="title">Dashboard</h1>

      <a href="#" @click.prevent="logout">Logout</a>
    </div>
  </section>
</template>

<script>
export default {
  data() {
    return {
      user: this.$auth.user.data,
    }
  },
  methods: {
    async logout() {
      await this.$auth.logout()

      this.$router.push('/login')
    },
  },
}
</script>

<!-- <template>
  <div>
      <v-app-bar app>
          <v-toolbar-title>Hello {{ this.$auth.user.data.first_name }}</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn class="primary">
              My orders
          </v-btn>
          <v-btn class="primary ml-3">
              My cart
          </v-btn>
          <v-btn class="primary ml-3" @click="logout">Logout</v-btn>
      </v-app-bar>

      <v-layout row justify-end>
          <v-container>
              <v-row>
                  <v-col v-for="product in products" :key="product.id" cols="12" md="4">
                      <v-card @click="viewProductDetails(product.product_id)">
                          <v-card-title>{{ product.title }}</v-card-title>
                          <v-card-subtitle>{{ product.category }}</v-card-subtitle>
                          <v-card-text>
                              <p>{{ product.description }}</p>
                              <p>Stock Count: {{ product.stock_count }}</p>
                              <p>Price: {{ product.price }}</p>
                              <p>Selling Price: {{ product.selling_price }}</p>
                          </v-card-text>
                          <v-card-actions>
                              <v-btn>Checkout</v-btn>
                          </v-card-actions>
                      </v-card>
                  </v-col>
              </v-row>
          </v-container>

      </v-layout>
  </div>
</template>

<script>
export default {
  data() {
      return {
          products: [],

      }
  },
  mounted() {
      this.fetchAllProducts();
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
              const response = await this.$axios.get('api/all-products');
              this.products = response.data.products;
              console.log(response.data.products);

          } catch (error) {
              console.log(error);
          } finally {
              this.loading = false;
          }
      },
      viewProductDetails(productId) {
          this.$router.push({ name: 'productDetails', params: { id: productId } });
      },
  }
}
</script> -->
