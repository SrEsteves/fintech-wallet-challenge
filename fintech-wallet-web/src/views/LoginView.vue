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
  <div class="card auth-card">
    <h2 class="form-title text-center">Login - Teck Wallet</h2>
    
    <div v-if="localError" class="alert alert-error mt-md">
      {{ localError }}
    </div>

    <form @submit.prevent="submitLogin" class="mt-md">
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

      <button type="submit" class="btn-primary w-100 mt-md" :disabled="isLoading">
        <span v-if="isLoading">Entrando...</span>
        <span v-else>Entrar</span>
      </button>
    </form>
    
    <p class="register-link mt-md">
      Não tem conta? <RouterLink to="/register">Cadastre-se</RouterLink>
    </p>
  </div>
</template>

<style scoped>
.auth-card {
  max-width: 400px;
  margin: 0 auto;
}

.form-title {
  color: var(--vt-c-indigo);
  font-weight: 700;
  margin-bottom: 0.2rem;
}

.form-group {
  margin-bottom: var(--spacing-md);
}

.form-group label {
  display: block;
  margin-bottom: 0.3rem;
  font-weight: 600;
  color: var(--vt-c-indigo);
  font-size: 0.9rem;
}

/* O base.css formata inputs globalmente, tratamos apenas o estado :disabled aqui */
.form-group input:disabled {
  background-color: var(--color-background-soft);
  cursor: not-allowed;
}

.w-100 {
  width: 100%;
  padding: 0.8rem;
  font-weight: 600;
}

.register-link {
  text-align: center;
  font-size: 0.9rem;
  color: var(--color-text-muted);
}

.register-link a {
  color: var(--color-primary);
  font-weight: 600;
  text-decoration: none;
  transition: opacity 0.2s;
}

.register-link a:hover {
  text-decoration: underline;
  opacity: 0.8;
}
</style>