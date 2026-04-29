import LoginPage from '@/pages/LoginPage.vue'
import MaitreJeuViewPage from '@/pages/MaitreJeuViewPage.vue'
import PartiesPage from '@/pages/PartiesPage.vue'
import RegisterPage from '@/pages/RegisterPage.vue'
import { createRouter, createWebHistory } from 'vue-router'
import { hasToken, hasRole } from '@/utils/api'
import JoueurPage from '@/pages/JoueurPage.vue'
import CharacterCreatePage from '@/pages/CharacterCreatePage.vue'

const routes = [
  {
    path: '/',
    redirect: () =>
      hasToken() ? (hasRole('game_master') ? '/gamemaster/dashboard' : '/dashboard') : '/login',
  },
  { path: '/login', name: 'Login', component: LoginPage },
  { path: '/register', name: 'Register', component: RegisterPage },

  { path: '/gamemaster/dashboard', name: 'MaitreJeuDashboard', component: MaitreJeuViewPage },
  { path: '/party/list', name: 'PartiesPage', component: PartiesPage },
 
  { path: '/joueur', name: 'Joueur', component: JoueurPage },
  { path: '/character/create', name: 'CharacterCreate', component: CharacterCreatePage },
]

export default createRouter({
  history: createWebHistory(),
  routes,
})
