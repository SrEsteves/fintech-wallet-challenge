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
  <div class="auth-box">
    <h2>Criar Conta - Teck Wallet</h2>

    <div v-if="authStore.error" class="error-msg">
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

      <button type="submit" :disabled="isLoading">
        {{ isLoading ? 'Cadastrando...' : 'Criar Minha Conta' }}
      </button>
    </form>

    <p class="login-link">
      Já tem uma conta? <RouterLink to="/login">Faça login</RouterLink>
    </p>
  </div>
</template>

<style scoped>
.auth-box {
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
button {
  width: 100%;
  padding: 0.75rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  cursor: pointer;
  font-weight: bold;
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
.login-link {
  text-align: center;
  margin-top: 1rem;
  font-size: 0.9rem;
}
</style>