<script setup>
import axios from 'axios'
import { ref, onMounted } from 'vue'

const username = ref('')
const password = ref('')

const error = ref('Quelque chose a mal tourné...')

const login = () => {
  axios
    .post('http://localhost:8000/auth/login', {
      username: 'mael',
      password: 'test',
    })
    .then((res) => {
      console.log(res)
    })
    .catch((error) => {
      error.value = error.response.data.message
    })
}

onMounted(() => {
  login()
})
</script>

<template>
  <div>
    <!-- main container -->
    <div class="min-h-screen flex items-center justify-center flex-col">
      <!-- if error -->
      <div v-if="error" class="text-red-800 py-4 font-bold">
        <p>{{ error }}</p>
      </div>
      <!-- title -->
      <h1 class="text-2xl text-creamy-jdr mb-6 font-semibold">Se connecter</h1>
      <!-- main card -->
      <div
        class="bg-green-jdr rounded-lg shadow-md p-8 w-full max-w-sm border-olive-jdr/40 border-2"
      >
        <!-- form -->
        <form @submit.prevent="login" class="flex flex-col">
          <!-- username -->
          <div class="flex flex-col mb-2">
            <label for="username" class="text-creamy-jdr">Identifiant</label>
            <input
              type="text"
              id="username"
              v-model="username"
              placeholder="ShyGoblin456"
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
          <!-- button -->
          <button
            type="submit"
            class="mt-4 mb-2 bg-orange-jdr text-creamy-jdr px-1 py-2 rounded-lg hover:bg-brown-jdr"
          >
            Reprendre votre Aventure
          </button>
          <!-- link to register page -->
          <a href="" class="mt-2 text-creamy-jdr text-center text-xs hover:text-orange-jdr"
            >Pas encore aventurier ?</a
          >
        </form>
      </div>
      <!-- sub-title -->
      <div>
        <h2 class="mt-20 text-creamy-jdr">©Quick JDR</h2>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
