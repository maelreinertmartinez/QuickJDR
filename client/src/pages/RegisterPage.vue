<script setup>
import { ref } from 'vue'
import axios from 'axios'

const username = ref('')
const password = ref('')
const confirmPassword = ref('')
const asGameMaster = ref(false)

const register = () => {
  const data = {
    username: username.value,
    password: password.value,
    confirm_password: confirmPassword.value,
    as_game_master: asGameMaster.value,
  }

  axios
    .post('http://localhost:8000/auth/register', data)
    .then((response) => {
      console.log(response.data)
    })
    .catch((error) => {
      console.error(error.message)
    })
}
</script>

<template>
  <div>
    <h1>Register</h1>
    <form @submit.prevent="register">
      <div>
        <label for="username">Username</label>
        <input type="text" id="username" v-model="username" />
      </div>
      <div>
        <label for="password">Password</label>
        <input type="password" id="password" v-model="password" />
      </div>
      <div>
        <label for="confirm-password">Confirm Password</label>
        <input type="password" id="confirm-password" v-model="confirmPassword" />
      </div>
      <div>
        <label for="as-game-master">As game master</label>
        <input type="checkbox" id="as-game-master" v-model="asGameMaster" />
      </div>
      <button type="submit">Register</button>
    </form>
  </div>
</template>

<style scoped></style>
