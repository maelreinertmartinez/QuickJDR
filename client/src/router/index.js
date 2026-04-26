import LoginPage from '@/pages/LoginPage.vue'
import MaitreJeuViewPage from '@/pages/MaitreJeuViewPage.vue'
import RegisterPage from '@/pages/RegisterPage.vue'
import { createRouter, createWebHistory } from 'vue-router'
import { hasToken, hasRole } from '@/utils/api'

const routes = [
  {
    path: '/',
    redirect: () =>
      hasToken() ? (hasRole('game_master') ? '/gamemaster/dashboard' : '/dashboard') : '/login',
  },
  { path: '/login', name: 'Login', component: LoginPage },
  { path: '/register', name: 'Register', component: RegisterPage },
  { path: '/gamemaster/dashboard', name: 'MaitreJeuView', component: MaitreJeuViewPage },
]

export default createRouter({
  history: createWebHistory(),
  routes,
})
