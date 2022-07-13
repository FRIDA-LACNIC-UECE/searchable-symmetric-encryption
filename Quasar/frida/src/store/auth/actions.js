import { api } from 'src/boot/axios'

export const doLogin = async ({ commit, dispatch }, payload) => {
  await api.post('/login', payload).then(response => {
    const token = response.data.token
    commit('setToken', token)
    api.defaults.headers.common.Authorization = 'JWT' + token.access
    dispatch('getMe', token)
    console.log(response.data)
    commit('SET_API_DATA_FETCH_SUCCESS', response)
    return response
  }).catch(err => {
    commit('SET_API_DATA_FETCH_ERROR', err.response)
    throw err
  })
}

export const signOut = ({ commit }) => {
  api.defaults.headers.common.Authorization = ''
  commit('removeToken')
}

export const getMe = async ({ commit }, token) => {
  await api.get('/getUser', {
    headers: {
      Authorization: `Bearer ${token}`
    }
  }).then(response => {
    commit('setMe', response.data)
  }).catch(err => {
    console.log(err)
  })
}
