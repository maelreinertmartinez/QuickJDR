import axios from 'axios'
import VueCookies from 'vue-cookies'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  timeout: 10000,
  ...(VueCookies.isKey('session_token')
    ? {
        headers: {
          Authorization: `Bearer ${VueCookies.get('session_token')}`,
        },
      }
    : {}),
})

// Set the Authorization header of the API (called when the user logs in/registers)
const setToken = (token) => {
  VueCookies.set('session_token', token, '1d')
  api.defaults.headers.common['Authorization'] = `Bearer ${token}`
}

// Clear the Authorization header of the API (called when the user logs out)
const clearToken = () => {
  VueCookies.remove('session_token')
  delete api.defaults.headers.common['Authorization']
}

export { setToken, clearToken }
export default api
