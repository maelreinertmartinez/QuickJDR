import axios from 'axios'
import VueCookies from 'vue-cookies'

// Check if the user has a session token (called before making authenticated requests)
const hasToken = () => {
  return VueCookies.isKey('session_token')
}

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  timeout: 10000,
  ...(hasToken()
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
  VueCookies.remove('session_roles')
  delete api.defaults.headers.common['Authorization']
}

const setRoles = (roles) => {
  VueCookies.set('session_roles', roles.join(','), '1d')
}

const clearRoles = () => {
  VueCookies.remove('session_roles')
}

const hasRole = (role) => {
  return VueCookies.get('session_roles')?.split(',').includes(role)
}

export { clearRoles, clearToken, hasRole, hasToken, setRoles, setToken }
export default api
