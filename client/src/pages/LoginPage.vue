<script setup>
import { ref } from 'vue'
import axios from 'axios'

const username = ref('')
const password = ref('')

const error = ref('dzadaz')

const login = () => {
  axios
    .post('http://localhost:8000/auth/login', {
      username: username.value,
      password: password.value,
    })
    .then(() => {
      // TODO: redirect to dashboard
    })
    .catch((error) => {
      error.value = error.response.data.message
    })
}
</script>

<template>
  <div>
    <h1>Login</h1>
    <form @submit.prevent="login">
      <div v-if="error">
        <p class="">{{ error }}</p>
      </div>
      <div>
        <label for="username">Username</label>
        <input type="text" id="username" v-model="username" />
      </div>
      <div>
        <label for="password">Password</label>
        <input type="password" id="password" v-model="password" />
      </div>
      <button type="submit">Login</button>
    </form>
  </div>
</template>

<style scoped></style>
