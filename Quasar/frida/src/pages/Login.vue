<template>
  <img src="~assets/wave.png" class="wave" alt="login-wave">
  <div class="row" style="height: 90vh">
    <div class="col-0 col-md-6 flex justify-center content-center">
      <img src="~assets/login.svg" class="responsive" alt="login-image">
    </div>
    <div class="col-12 col-md-6 flex content-center">
      <q-card style='width: 80%'>
        <q-card-section>
          <q-avatar size="103px" class="absolute-center shadow-10">
            <img src="~assets/avatar.svg" alt="avatar">
          </q-avatar>
        </q-card-section>
        <q-card-section>
          <div class="q-pt-lg">
            <div class="col text-h6 ellipsis flex justify-center">
              <h2 class="text-h2 text-uppercase q-my-none text-weight-regular">Login</h2>
            </div>
          </div>
        </q-card-section>
        <q-card-section>
          <q-form class="q-gutter-md" @submit.prevent="submitLogin">
            <q-input label="Email" v-model="login.email" :rules="[
              val => !!val || 'Nome obrigatório'
            ]">
            </q-input>
            <q-input label="Senha" :type="isPwd ? 'password' : 'text'" v-model="login.password" :rules="[
              val => !!val || 'Nome obrigatório'
            ]">
              <template v-slot:append>
                <q-icon
                  :name="isPwd ? 'visibility_off' : 'visibility'"
                  class="cursor-pointer"
                  @click="isPwd = !isPwd"
                />
              </template>
            </q-input>
            <div>
              <q-btn class="full-width" color="primary" label="Login" type="submit" rounded></q-btn>
              <div class="text-center q-mt-sm q-gutter-lg">
                <router-link class="text-white" to="/login">Esqueceu a senha?</router-link>
                <router-link class="text-white" to="/register">Criar conta</router-link>
              </div>
            </div>
          </q-form>
        </q-card-section>
      </q-card>
    </div>
  </div>
</template>

<script>
import { Notify } from 'quasar'
import { defineComponent } from 'vue'
import { mapActions, mapGetters } from 'vuex'

export default defineComponent({
  name: 'Login',
  data () {
    return {
      login: {
        email: 'michael@gmail.com',
        password: '12345'
      },
      isPwd: true
    }
  },
  computed: {
    ...mapGetters('auth', ['getMe']),
    ...mapGetters('auth', ['isAuthenticated']),
    ...mapGetters('auth', ['apiData'])
  },
  methods: {
    ...mapActions('auth', ['doLogin']),
    async submitLogin () {
      try {
        await this.doLogin(this.login)
        const toPath = this.$route.query.to || '/Users'
        this.$router.push(toPath)
        Notify.create({
          type: 'positive',
          message: this.apiData.response.data.message,
          timeout: 1000
        })
      } catch (err) {
        console.log(err)
        Notify.create({
          type: 'negative',
          message: this.apiData.response.data.error,
          timeout: 1000
        })
      }
    }
  }
})
</script>

<style scoped>
.wave{
  position: fixed;
  height: 100%;
  left: 0;
  bottom: 0;
  z-index: -1;
}
</style>
