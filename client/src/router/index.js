import JoueurPage from '@/pages/JoueurPage.vue'
import LoginPage from '@/pages/LoginPage.vue'
import MaitreJeuViewPage from '@/pages/MaitreJeuViewPage.vue'
import PartiesPage from '@/pages/PartiesPage.vue'
import PartyCreationPage from '@/pages/PartyCreationPage.vue'
import RegisterPage from '@/pages/RegisterPage.vue'
import { hasRole, hasToken } from '@/utils/api'
import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    redirect: () => (hasToken() ? '/party/list' : '/login'),
  },
  { path: '/login', name: 'Login', component: LoginPage },
  { path: '/register', name: 'Register', component: RegisterPage },
  {
    path: '/party/:id',
    name: 'MaitreJeuView',
    component: hasRole('game_master') ? MaitreJeuViewPage : JoueurPage,
  },
  { path: '/party/list', name: 'PartiesPage', component: PartiesPage },
  { path: '/party/create', name: 'PartyCreate', component: PartyCreationPage },
  // { path: '/player/dashboard', name: 'JoueurPage', component: JoueurPage },
]

export default createRouter({
  history: createWebHistory(),
  routes,
})
