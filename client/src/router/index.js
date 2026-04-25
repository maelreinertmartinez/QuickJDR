import LoginPage from '@/pages/LoginPage.vue'
import MaitreJeuViewPage from '@/pages/MaitreJeuViewPage.vue'
import PartiesPage from '@/pages/PartiesPage.vue'
import RegisterPage from '@/pages/RegisterPage.vue'
import { createRouter, createWebHistory } from 'vue-router'
import JoueurPage from '@/pages/JoueurPage.vue'

const routes = [
  { path: '/', name: 'Parties', component: PartiesPage },
  { path: '/login', name: 'Login', component: LoginPage },
  { path: '/register', name: 'Register', component: RegisterPage },
  { path: '/gamemaster/dashboard', name: 'MaitreJeuView', component: MaitreJeuViewPage },
  { path: '/joueur', name: 'Joueur', component: JoueurPage },
]

export default createRouter({
  history: createWebHistory(),
  routes,
})
