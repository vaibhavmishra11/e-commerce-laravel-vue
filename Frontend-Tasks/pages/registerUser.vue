<template>
  <div>
    <v-snackbar v-model="snackbar" :timeout="2000">
      {{ snackbarText }}
      <template v-slot:action="{ attrs }">
        <v-btn color="blue" text v-bind="attrs" @click="snackbar = false">
          Close
        </v-btn>
      </template>
    </v-snackbar>

    <div class="register-container">
      <label for="username">First name:</label>
      <input type="text" v-model="user.first_name" required />

      <label for="username">Last name:</label>
      <input type="text" v-model="user.last_name" required />

      <label for="username">Email Address:</label>
      <input type="email" v-model="user.email" required />

      <label for="password">Password:</label>
      <input type="password" v-model="user.password" required />

      <v-btn color="primary" @click="register" :loading="loading">
        Register
      </v-btn>
      <h4 class="mt-5 text-center">
        if already Register <nuxt-link to="/login"> login here</nuxt-link>
      </h4>
    </div>
  </div>
</template>

  <script>
export default {
  auth: false,
  data() {
    return {
      loading: false,
      user: {
        first_name: "",
        last_name:"",
        email: "",
        password: "",
      },
    };
  },
  methods: {
    async register() {
      try {
        this.loading = true;
        await this.$axios.get("/sanctum/csrf-cookie");
        const res = await this.$axios.post("http://localhost:8000/api/register", this.user);
        if (res.data.status == true) {
          this.$router.push("/login");
          console.log(res, "res");
          this.snackbarText = "Registered successfully";
          this.snackbar = true;
        }
      } catch (e) {
        console.log(e);
      } finally {
        this.loading = false;
      }
    },
  },
};
</script>
<style scoped>
.register-container {
  max-width: 300px;
  margin: 0 auto;
  padding: 20px;
  background-color: #fff;
  border-radius: 8px;
  box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
}

label {
  display: block;
  margin-bottom: 8px;
}

input {
  width: 100%;
  padding: 8px;
  margin-bottom: 16px;
  box-sizing: border-box;
}

button {
  background-color: #4caf50;
  color: #fff;
  padding: 10px 15px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}
</style>