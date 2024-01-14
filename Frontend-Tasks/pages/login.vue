<template>
  <div>
    <div class="login-container">
      <form method="post" @submit.prevent="login">
        <v-text-field
          v-model="email"
          label="Username"
          type="email"
          required
        ></v-text-field>
        <v-text-field
          v-model="password"
          label="Password"
          type="password"
          required
        ></v-text-field>
        <v-btn color="success" block type="submit" :loading="loading"
          >Login</v-btn
        >
      </form>
      <h4 class="mt-5 text-center">
        if new user <nuxt-link to="/registerUser"> register here</nuxt-link>
      </h4>
    </div>
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
      email: "",
      password: "",
      loading: false,
      snackbarText: "",
    };
  },

  methods: {
    async login() {
      try {
        this.loading = true;
        const response = await this.$auth.loginWith("laravelSanctum", {
          data: {
            email: this.email,
            password: this.password,
          },
        });
        console.log('after login', response)
        if (response.status == 200) {
          if(this.$auth.user.data.user_type=="seller"){
            this.$router.push("/sellerDashboard");
          }else{
            this.$router.push("/userDashboard");
          }
          this.snackbarText = "logged in successfully";
          this.snackbar = true;
        }
      } catch (error) {
        this.snackbarText = "invalid Credentials";
        this.snackbar = true;
        console.log(error);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>


<style scoped>
.login-container {
  max-width: 300px;
  margin-top: 9rem;
  margin-left: 25rem;
  padding: 20px;
  background-color: #dbd6d6;
  border-radius: 8px;
  border-color: black;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}
</style>
