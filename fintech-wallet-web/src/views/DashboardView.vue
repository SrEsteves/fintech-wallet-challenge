<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import api from '../services/api';

const authStore = useAuthStore();
const recentTransactions = ref([]);
const isLoading = ref(true);

const fetchDashboardData = async () => {
  isLoading.value = true;
  try {
    await authStore.fetchUser();
    
    const response = await api.get('/transactions');
    recentTransactions.value = response.data.data.slice(0, 5);
  } catch (error) {
    console.error("Erro ao carregar dados", error);
  } finally {
    isLoading.value = false;
  }
};

const formatCurrency = (value) => {
  return parseFloat(value).toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
};

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('pt-BR', { 
    day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit'
  });
};

onMounted(() => {
  fetchDashboardData();
});
</script>

<template>
  <div class="dashboard">
    <h2>Bem-vindo(a), {{ authStore.user?.name }}</h2>
    
    <div class="balance-card">
      <h3>Saldo Atual</h3>
      <p class="balance-amount" v-if="authStore.user?.wallet">
        {{ formatCurrency(authStore.user.wallet.balance) }}
      </p>
      <p v-else>Carregando...</p>
    </div>

    <div class="transactions-section">
      <h3>Últimas 5 Transações</h3>
      
      <div v-if="isLoading">Buscando transações...</div>
      
      <table v-else-if="recentTransactions.length > 0" class="data-table">
        <thead>
          <tr>
            <th>Data</th>
            <th>Tipo</th>
            <th>Usuário</th>
            <th>Valor</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="txn in recentTransactions" :key="txn.id">
            <td>{{ formatDate(txn.created_at) }}</td>
            <td>
              <span :class="txn.type === 'credit' ? 'badge-credit' : 'badge-debit'">
                {{ txn.type === 'credit' ? 'Recebido' : 'Enviado' }}
              </span>
            </td>
            <td>{{ txn.related_user?.name || 'Desconhecido' }}</td>
            <td :class="txn.type === 'credit' ? 'text-credit' : 'text-debit'">
              {{ txn.type === 'credit' ? '+' : '-' }} {{ formatCurrency(txn.amount) }}
            </td>
          </tr>
        </tbody>
      </table>
      
      <p v-else class="empty-state">Você ainda não possui transações.</p>
    </div>
  </div>
</template>

<style scoped>
.dashboard {
  display: flex;
  flex-direction: column;
  gap: 2rem;
}

.balance-card {
  background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
  color: white;
  padding: 2rem;
  border-radius: 8px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.1);
  text-align: center;
}

.balance-card h3 {
  margin: 0 0 1rem 0;
  font-weight: normal;
  opacity: 0.9;
}

.balance-amount {
  font-size: 2.5rem;
  font-weight: bold;
  margin: 0;
}

.transactions-section {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 1rem;
}

.data-table th, .data-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.data-table th {
  background-color: #f8f9fa;
  font-weight: 600;
}

.badge-credit {
  background-color: #d4edda;
  color: #155724;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.85rem;
}

.badge-debit {
  background-color: #f8d7da;
  color: #721c24;
  padding: 0.25rem 0.5rem;
  border-radius: 4px;
  font-size: 0.85rem;
}

.text-credit {
  color: #28a745;
  font-weight: bold;
}

.text-debit {
  color: #dc3545;
  font-weight: bold;
}

.empty-state {
  text-align: center;
  color: #6c757d;
  padding: 2rem 0;
}
</style>