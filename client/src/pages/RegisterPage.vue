<script setup>
import axios from 'axios'
import { ref } from 'vue'
import { setToken, setRoles } from '@/utils/api'

const error = ref(null)

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
    .then((res) => {
      error.value = null
      setToken(res.data.session)
      setRoles(res.data.roles)
      window.location.href = '/'
    })
    .catch((err) => {
      error.value = err.response.data.message
    })
}
</script>

<template>
  <!-- main container -->
  <div class="min-h-screen flex items-center justify-center flex-col">
    <!-- if error -->
    <div v-if="error" class="text-red-800 py-4 font-bold">
      <p>{{ error }}</p>
    </div>
    <!-- title -->
    <h1 class="text-2xl text-creamy-jdr mb-6 font-semibold">S'inscrire</h1>
    <!-- main card -->
    <div class="bg-green-jdr rounded-lg shadow-md p-8 w-full max-w-sm border-olive-jdr/40 border-2">
      <!-- form -->
      <form @submit.prevent="register" class="flex flex-col">
        <!-- username -->
        <div class="flex flex-col mb-2">
          <label for="username" class="text-creamy-jdr">Identifiant</label>
          <input
            type="text"
            id="username"
            v-model="username"
            placeholder="MasterDwarf123"
            class="bg-dark-green-jdr text-olive-jdr border px-2 py-1 rounded-lg"
          />
        </div>
        <!-- password -->
        <div class="flex flex-col my-2">
          <label for="password" class="text-creamy-jdr">Mot de passe</label>
          <input
            type="password"
            id="password"
            v-model="password"
            placeholder="••••••••••••"
            class="bg-dark-green-jdr text-olive-jdr border px-2 py-1 rounded-lg"
          />
        </div>
        <!-- confirm password -->
        <div class="flex flex-col my-2">
          <label for="confirm-password" class="text-creamy-jdr">Confirmez votre mot de passe</label>
          <input
            type="password"
            id="confirm-password"
            v-model="confirmPassword"
            placeholder="••••••••••••"
            class="bg-dark-green-jdr text-olive-jdr border px-2 py-1 rounded-lg"
          />
        </div>
        <!-- checkbox -->
        <div class="my-2">
          <input
            type="checkbox"
            id="as-game-master"
            v-model="asGameMaster"
            class="appearance-none w-4 h-4 bg-dark-green-jdr outline outline-olive-jdr p-1 rounded mx-2 checked:bg-orange-jdr hover:outline-creamy-jdr"
          />
          <label for="as-game-master" class="text-creamy-jdr">Vous êtes MJ</label>
        </div>
        <!-- button -->
        <button
          type="submit"
          class="mt-4 mb-2 bg-orange-jdr text-creamy-jdr px-1 py-2 rounded-lg hover:bg-brown-jdr"
        >
          Commencez votre Aventure
        </button>
        <!-- link to login page -->
        <a href="/login" class="mt-2 text-creamy-jdr text-center text-xs hover:text-orange-jdr"
          >Déjà aventurier ?</a
        >
      </form>
    </div>
    <!-- sub-title -->
    <div>
      <h2 class="mt-20 text-creamy-jdr">©Quick JDR</h2>
    </div>
  </div>
</template>

<style scoped></style>
