import { createApp } from 'vue'
import VueCookies from 'vue-cookies'
import App from './App.vue'
import router from './router'
import './index.css'

const app = createApp(App)
app.use(router)
app.use(VueCookies)
app.mount('#app')
