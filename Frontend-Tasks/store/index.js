export const state = () => ({
  role: [],
  user: null
});

export const mutations = {
  setRole(state, role) {
    state.role = role;
  },
  setUser(state, user){
    state.user = user
  }
};
