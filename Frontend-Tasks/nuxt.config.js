import colors from "vuetify/es5/util/colors";

export default {
  // Disable server-side rendering: https://go.nuxtjs.dev/ssr-mode
  ssr: false,

  middleware: "auth",

  // Global page headers: https://go.nuxtjs.dev/config-head
  head: {
    titleTemplate: "%s - user-authentication-project" ,  
    //hi   
    title: "user-authentication-project",
    htmlAttrs: {
      lang: "en",
    },
    meta: [
      { charset: "utf-8" },
      { name: "viewport", content: "width=device-width, initial-scale=1" },
      { hid: "description", name: "description", content: "" },
      { name: "format-detection", content: "telephone=no" },
    ],
    link: [{ rel: "icon", type: "image/x-icon", href: "/favicon.ico" }],
  },

  // Global CSS: https://go.nuxtjs.dev/config-css
  css: [],

  // Plugins to run before rendering page: https://go.nuxtjs.dev/config-plugins
  plugins: ["~/plugins/vue_confirm_dialog.client.js"],

  // Auto import components: https://go.nuxtjs.dev/config-components
  components: true,

  // Modules for dev and build (recommended): https://go.nuxtjs.dev/config-modules
  buildModules: [
    // https://go.nuxtjs.dev/vuetify
    "@nuxtjs/vuetify",
  ],

  // Modules: https://go.nuxtjs.dev/config-modules
  modules: ["@nuxtjs/axios", "@nuxtjs/auth-next", "@nuxtjs/dayjs"],

  dayjs: {
    locales: ["en", "fr"],
    plugins: ["utc", "timezone"],
    defaultLocale: "en",
    defaultTimezone: "Asia/Kolkata",
  },

  router: {
    middleware: ["auth"],
  },

  // auth: {
  //   strategies: {
  //     local: {
  //       token: {
  //         property: "token",
  //         global: true,
  //         // required: true,
  //         // type: 'Bearer'
  //       },
  //       user: {
  //         property: "user",
  //         // autoFetch: true
  //       },
  //       endpoints: {
  //         login: { url: "https://reqres.in/api/login", method: "post" },
  //         logout: { url: "/api/auth/logout", method: "post" },
  //         user: { url: "/api/auth/user", method: "get" },
  //       },
  //     },
  //   },
  // },
  axios: {
    baseUrl: "http://localhost:8000",
    credentials: true,
  },

  auth: {
    strategies: {
      laravelSanctum: {
        provider: "laravel/sanctum",
        url: "http://localhost:8000",
        endpoints: {
          login: {
            url: "/login",
          },
          logout: {
            url: "/logout",
          },
          user: {
            url: "/api/user",
          },
        },
      },
    },
  },

  // Vuetify module configuration: https://go.nuxtjs.dev/config-vuetify
  vuetify: {
    customVariables: ["~/assets/variables.scss"],
    theme: {
      dark: false,
      themes: {
        dark: {
          primary: colors.blue.darken2,
          accent: colors.grey.darken3,
          secondary: colors.amber.darken3,
          info: colors.teal.lighten1,
          warning: colors.amber.base,
          error: colors.deepOrange.accent4,
          success: colors.green.accent3,
        },
      },
    },
  },

  // Build Configuration: https://go.nuxtjs.dev/config-build
  build: {},

  store: true,
};
