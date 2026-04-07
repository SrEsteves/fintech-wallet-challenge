<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';

const authStore = useAuthStore();
const router = useRouter();

const email = ref('');
const amount = ref('');
const isLoading = ref(false);
const message = ref({ type: '', text: '' });

const handleTransfer = async () => {
  isLoading.value = false;
  message.value = { type: '', text: '' };

  if (amount.value <= 0) {
    message.value = { type: 'error', text: 'O valor deve ser maior que zero.' };
    return;
  }

  isLoading.value = true;
  try {
    const response = await api.post('/transfer', {
      email: email.value,
      amount: parseFloat(amount.value)
    });

    message.value = { type: 'success', text: response.data.message };
    
    email.value = '';
    amount.value = '';
    
    await authStore.fetchUser();

    setTimeout(() => router.push('/'), 2000);

  } catch (error) {
    const errorMsg = error.response?.data?.error || error.response?.data?.message || 'Erro ao realizar transferência.';
    message.value = { type: 'error', text: errorMsg };
  } finally {
    isLoading.value = false;
  }
};
</script>

<template>
  <div class="transfer-container">
    <h2>Nova Transferência</h2>
    <p>Envie dinheiro para outros usuários de forma instantânea.</p>

    <div v-if="message.text" :class="['alert', message.type === 'success' ? 'alert-success' : 'alert-error']">
      {{ message.text }}
    </div>

    <form @submit.prevent="handleTransfer" class="transfer-form">
      <div class="form-group">
        <label>E-mail do Destinatário</label>
        <input 
          type="email" 
          v-model="email" 
          required 
          placeholder="exemplo@teck.com"
        />
      </div>

      <div class="form-group">
        <label>Valor (R$)</label>
        <input 
          type="number" 
          step="0.01" 
          v-model="amount" 
          required 
          placeholder="0,00"
        />
        <small v-if="authStore.user?.wallet">
          Saldo disponível: <strong>R$ {{ authStore.user.wallet.balance }}</strong>
        </small>
      </div>

      <button type="submit" :disabled="isLoading">
        {{ isLoading ? 'Processando...' : 'Confirmar Transferência' }}
      </button>
    </form>
  </div>
</template>

<style scoped>
.transfer-container {
  background: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
  max-width: 500px;
  margin: 0 auto;
}

.transfer-form {
  margin-top: 1.5rem;
}

.form-group {
  margin-bottom: 1.5rem;
}

.form-group label {
  display: block;
  margin-bottom: 0.5rem;
  font-weight: bold;
}

.form-group input {
  width: 100%;
  padding: 0.75rem;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

.form-group small {
  display: block;
  margin-top: 0.5rem;
  color: #666;
}

button {
  width: 100%;
  padding: 1rem;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 4px;
  font-size: 1rem;
  font-weight: bold;
  cursor: pointer;
}

button:disabled {
  background-color: #ccc;
}

.alert {
  padding: 1rem;
  border-radius: 4px;
  margin-bottom: 1.5rem;
  text-align: center;
}

.alert-success {
  background-color: #d4edda;
  color: #155724;
}

.alert-error {
  background-color: #f8d7da;
  color: #721c24;
}
</style>