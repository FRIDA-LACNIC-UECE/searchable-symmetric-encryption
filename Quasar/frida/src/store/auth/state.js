export default function () {
  return {
    me: {},
    token: '',
    isAuthenticated: false,
    isAdmin: false,
    api: {
      fetch: {
        inProgress: false,
        completed: false,
        success: false,
        error: false
      },
      response: {}
    }
  }
}
