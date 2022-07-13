export const setToken = (state, token) => {
  state.token = token
  state.isAuthenticated = true
}

export const removeToken = (state, token) => {
  state.token = ''
  state.isAuthenticated = false
}

export const setMe = (state, me) => {
  state.me = me
}

export const SET_API_DATA_FETCH_IN_PROGRESS = (state) => {
  state.api.fetch.inProgress = true
  state.api.fetch.completed = false
  state.api.fetch.success = false
  state.api.fetch.error = false
  state.api.response = {}
}

export const SET_API_DATA_FETCH_SUCCESS = (state, payload) => {
  state.api.fetch.inProgress = false
  state.api.fetch.completed = true
  state.api.fetch.success = true
  state.api.fetch.error = false
  state.api.response = payload
}
export const SET_API_DATA_FETCH_ERROR = (state, payload) => {
  state.api.fetch.inProgress = false
  state.api.fetch.completed = true
  state.api.fetch.success = false
  state.api.fetch.error = true
  state.api.response = payload
}
