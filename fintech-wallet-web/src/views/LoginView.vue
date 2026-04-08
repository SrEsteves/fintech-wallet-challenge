<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '../stores/auth'

const email = ref('')
const password = ref('')
const isLoading = ref(false)
const localError = ref('')

const authStore = useAuthStore()
const router = useRouter()

const submitLogin = async () => {
  localError.value = ''
  authStore.error = ''

  if (!email.value || !password.value) {
    localError.value = 'Preencha todos os campos.'
    return
  }

  try {
    isLoading.value = true

    const success = await authStore.login(email.value, password.value)

    if (success) {
      router.push('/')
    } else {
      localError.value = authStore.error || 'Falha ao fazer login.'
    }
  } catch (error) {
    localError.value = 'Erro inesperado. Tente novamente.'
  } finally {
    isLoading.value = false
  }
}
</script>

<template>
  <div class="login-box">
    <h2>Login - Teck Wallet</h2>
    
    <!-- erro -->
    <div v-if="localError" class="error-msg">
      {{ localError }}
    </div>

    <form @submit.prevent="submitLogin">
      <div class="form-group">
        <label>E-mail</label>
        <input 
          type="email" 
          v-model="email" 
          placeholder="Digite seu e-mail"
          :disabled="isLoading"
        />
      </div>

      <div class="form-group">
        <label>Senha</label>
        <input 
          type="password" 
          v-model="password" 
          placeholder="Sua senha"
          :disabled="isLoading"
        />
      </div>

      <button type="submit" :disabled="isLoading">
        <span v-if="isLoading">Entrando...</span>
        <span v-else>Entrar</span>
      </button>
    </form>
    
    <p class="register-link">
      Não tem conta? <RouterLink to="/register">Cadastre-se</RouterLink>
    </p>
  </div>
</template>

<style scoped>
.login-box {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  max-width: 400px;
  margin: 0 auto;
}

.form-group {
  margin-bottom: 1rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
}

.form-group input {
  width: 100%;
  padding: 0.5rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

.form-group input:disabled {
  background-color: #f5f5f5;
  cursor: not-allowed;
}

button {
  width: 100%;
  padding: 0.75rem;
  background-color: #28a745;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  transition: 0.2s ease;
}

button:hover:not(:disabled) {
  opacity: 0.9;
}

button:disabled {
  background-color: #6c757d;
}

.error-msg {
  color: #dc3545;
  background-color: #f8d7da;
  padding: 0.5rem;
  border-radius: 4px;
  margin-bottom: 1rem;
  text-align: center;
}

.register-link {
  text-align: center;
  margin-top: 1rem;
  font-size: 0.9rem;
}
</style>