import LoginPage from '@/pages/LoginPage.vue'
import MaitreJeuViewPage from '@/pages/MaitreJeuViewPage.vue'
import PartiesPage from '@/pages/PartiesPage.vue'
import PartyCreationPage from '@/pages/PartyCreationPage.vue'
import RegisterPage from '@/pages/RegisterPage.vue'
import { hasToken } from '@/utils/api'
import { createRouter, createWebHistory } from 'vue-router'

const routes = [
  {
    path: '/',
    redirect: () => (hasToken() ? '/party/list' : '/login'),
  },
  { path: '/login', name: 'Login', component: LoginPage },
  { path: '/register', name: 'Register', component: RegisterPage },
  { path: '/gamemaster/dashboard', name: 'MaitreJeuView', component: MaitreJeuViewPage },
  { path: '/party/list', name: 'PartiesPage', component: PartiesPage },
  { path: '/party/create', name: 'PartyCreate', component: PartyCreationPage },
]

export default createRouter({
  history: createWebHistory(),
  routes,
})
