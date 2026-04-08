<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const name = ref('');
const email = ref('');
const password = ref('');
const isLoading = ref(false);
const authStore = useAuthStore();
const router = useRouter();

const handleRegister = async () => {
  if (!name.value || !email.value || !password.value) return;

  isLoading.value = true;
  const success = await authStore.register(name.value, email.value, password.value);
  isLoading.value = false;

  if (success) {
    router.push('/');
  }
};
</script>

<template>
  <div class="card auth-card">
    <h2 class="form-title text-center">Criar Conta</h2>
    <p class="text-center text-muted mb-lg">Bem-vindo(a) à Teck Wallet</p>

    <div v-if="authStore.error" class="alert alert-error mt-md">
      {{ authStore.error }}
    </div>

    <form @submit.prevent="handleRegister">
      <div class="form-group">
        <label>Nome Completo</label>
        <input type="text" v-model="name" required placeholder="Seu nome" />
      </div>

      <div class="form-group">
        <label>E-mail</label>
        <input type="email" v-model="email" required placeholder="seu@email.com" />
      </div>

      <div class="form-group">
        <label>Senha</label>
        <input type="password" v-model="password" required placeholder="Mínimo 8 caracteres" />
      </div>

      <button type="submit" class="btn-primary w-100 mt-md" :disabled="isLoading">
        {{ isLoading ? 'Cadastrando...' : 'Criar Minha Conta' }}
      </button>
    </form>

    <p class="login-link mt-md">
      Já tem uma conta? <RouterLink to="/login">Faça login</RouterLink>
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

.text-muted {
  color: var(--color-text-muted);
}

.mb-lg {
  margin-bottom: var(--spacing-lg);
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

.w-100 {
  width: 100%;
  padding: 0.8rem;
  font-weight: 600;
}

.login-link {
  text-align: center;
  font-size: 0.9rem;
  color: var(--color-text-muted);
}

.login-link a {
  color: var(--color-primary);
  font-weight: 600;
  text-decoration: none;
  transition: opacity 0.2s;
}

.login-link a:hover {
  text-decoration: underline;
  opacity: 0.8;
}
</style>