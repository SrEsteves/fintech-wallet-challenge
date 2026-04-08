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
    <header class="dashboard-header">
      <h2>Bem-vindo(a), <span>{{ authStore.user?.name }}</span></h2>
      <p class="text-muted">Acompanhe seu saldo e movimentações recentes.</p>
    </header>
    
    <div class="card balance-card">
      <h3>Saldo Atual</h3>
      <p class="balance-amount" v-if="authStore.user?.wallet">
        {{ formatCurrency(authStore.user.wallet.balance) }}
      </p>
      <div v-else class="loading-pulse">Carregando saldo...</div>
    </div>

    <div class="card mt-lg">
      <h3 class="section-title">Últimas Movimentações</h3>
      
      <div v-if="isLoading" class="text-center text-muted mt-md">
        Buscando transações...
      </div>
      
      <div v-else-if="recentTransactions.length > 0" class="table-responsive">
        <table class="data-table">
          <thead>
            <tr>
              <th>Data</th>
              <th>Tipo</th>
              <th>Usuário</th>
              <th class="text-right">Valor</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="txn in recentTransactions" :key="txn.id">
              <td class="text-muted">{{ formatDate(txn.created_at) }}</td>
              <td>
                <span class="badge" :class="txn.type === 'credit' ? 'badge-credit' : 'badge-debit'">
                  {{ txn.type === 'credit' ? 'Recebido' : 'Enviado' }}
                </span>
              </td>
              <td>{{ txn.related_user?.name || 'Desconhecido' }}</td>
              <td class="text-right" :class="txn.type === 'credit' ? 'text-credit' : 'text-debit'">
                {{ txn.type === 'credit' ? '+' : '-' }} {{ formatCurrency(txn.amount) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <p v-else class="text-center text-muted mt-md">Você ainda não possui transações.</p>
    </div>
  </div>
</template>

<style scoped>
.dashboard-header {
  margin-bottom: var(--spacing-lg);
}

.dashboard-header h2 {
  color: var(--vt-c-indigo);
  font-weight: 700;
}

.dashboard-header span {
  color: var(--color-primary);
}

.text-muted {
  color: var(--color-text-muted);
}

.text-right {
  text-align: right !important;
}

/* Modificando o card do base.css apenas para o Saldo */
.balance-card {
  background: linear-gradient(135deg, var(--vt-c-indigo) 0%, #1a252f 100%);
  color: white;
  text-align: center;
  border: none;
}

.balance-card h3 {
  font-weight: 500;
  opacity: 0.9;
  font-size: 1.1rem;
}

.balance-amount {
  font-size: 2.5rem;
  font-weight: 700;
  margin-top: var(--spacing-sm);
}

.section-title {
  color: var(--vt-c-indigo);
  margin-bottom: var(--spacing-md);
  font-weight: 600;
}

.table-responsive {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th, .data-table td {
  padding: var(--spacing-md);
  text-align: left;
  border-bottom: 1px solid var(--color-border);
  font-size: 0.95rem;
}

.data-table th {
  color: var(--color-text-muted);
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.8rem;
  letter-spacing: 0.5px;
}

/* Estilização das Badges usando rgba para fundo suave e cor sólida no texto */
.badge {
  padding: 0.3rem 0.6rem;
  border-radius: var(--radius);
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
}

.badge-credit {
  background-color: rgba(40, 167, 69, 0.1); /* Transparência sobre a cor primária */
  color: var(--color-primary);
}

.badge-debit {
  background-color: rgba(220, 53, 69, 0.1); /* Transparência sobre a cor danger */
  color: var(--color-danger);
}

.text-credit {
  color: var(--color-primary);
  font-weight: 700;
}

.text-debit {
  color: var(--color-danger);
  font-weight: 700;
}

/* Pequena animação enquanto carrega o saldo */
.loading-pulse {
  animation: pulse 1.5s infinite;
  opacity: 0.7;
  margin-top: var(--spacing-sm);
}

@keyframes pulse {
  0% { opacity: 0.5; }
  50% { opacity: 1; }
  100% { opacity: 0.5; }
}
</style>