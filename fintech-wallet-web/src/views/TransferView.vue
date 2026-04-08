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

// Função para deixar o saldo bonitinho igual ao Dashboard
const formatCurrency = (val) => {
  return parseFloat(val).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

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
  <div class="card transfer-card">
    <h2 class="form-title">Nova Transferência</h2>
    <p class="text-muted">Envie dinheiro para outros usuários de forma instantânea.</p>

    <div v-if="message.text" :class="['alert mt-md', message.type === 'success' ? 'alert-success' : 'alert-error']">
      {{ message.text }}
    </div>

    <form @submit.prevent="handleTransfer" class="mt-md">
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
        <label>Valor</label>
        <input 
          type="number" 
          step="0.01" 
          v-model="amount" 
          required 
          placeholder="0,00"
        />
        <small v-if="authStore.user?.wallet" class="text-muted">
          Saldo disponível: <strong>{{ formatCurrency(authStore.user.wallet.balance) }}</strong>
        </small>
      </div>

      <button type="submit" class="btn-primary w-100 mt-md" :disabled="isLoading">
        {{ isLoading ? 'Processando...' : 'Confirmar Transferência' }}
      </button>
    </form>
  </div>
</template>

<style scoped>
/* Aqui mantemos apenas o que é exclusivo desta tela. 
  Inputs, botões e alertas foram apagados porque o base.css já resolve.
*/
.transfer-card {
  max-width: 500px;
  margin: 0 auto;
}

.form-title {
  color: var(--vt-c-indigo);
  margin-bottom: var(--spacing-sm);
  font-weight: 700;
}

.text-muted {
  color: var(--color-text-muted);
}

.form-group {
  margin-bottom: var(--spacing-md);
}

.form-group label {
  display: block;
  margin-bottom: 0.3rem;
  font-weight: 600;
  color: var(--vt-c-indigo);
}

.form-group small {
  display: block;
  margin-top: 0.5rem;
  font-size: 0.85rem;
}

.w-100 {
  width: 100%;
  padding: 0.8rem;
  font-weight: 600;
}
</style>