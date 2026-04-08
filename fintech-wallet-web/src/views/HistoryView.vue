<script setup>
import { ref, onMounted, watch } from 'vue';
import api from '../services/api';

const transactions = ref([]);
const pagination = ref({});
const isLoading = ref(true);

// Estados dos filtros conforme exigido no PDF
const filters = ref({
  type: '',
  start_date: '',
  end_date: '',
  page: 1
});

const fetchHistory = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('/transactions', { params: filters.value });
    transactions.value = response.data.data;
    pagination.value = response.data;
  } catch (error) {
    console.error("Erro ao carregar histórico", error);
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

const changePage = (page) => {
  filters.value.page = page;
  fetchHistory();
};

// Aplica os filtros automaticamente ao mudar os valores
watch([() => filters.value.type, () => filters.value.start_date, () => filters.value.end_date], () => {
  filters.value.page = 1;
  fetchHistory();
});

onMounted(fetchHistory);
</script>

<template>
  <div class="card history-card">
    <header class="history-header">
      <h2 class="section-title">Histórico de Transações</h2>
      <p class="text-muted">Acompanhe todas as suas entradas e saídas.</p>
    </header>

    <div class="filters-bar mt-md">
      <div class="filter-group">
        <label>Tipo</label>
        <select v-model="filters.type" class="form-control">
          <option value="">Todos</option>
          <option value="credit">Recebidos (Crédito)</option>
          <option value="debit">Enviados (Débito)</option>
        </select>
      </div>

      <div class="filter-group">
        <label>De:</label>
        <input type="date" v-model="filters.start_date" class="form-control" />
      </div>

      <div class="filter-group">
        <label>Até:</label>
        <input type="date" v-model="filters.end_date" class="form-control" />
      </div>
    </div>

    <div v-if="isLoading" class="text-center text-muted mt-lg loading-pulse">
      Carregando transações...
    </div>

    <div v-else class="mt-md">
      <div class="table-responsive" v-if="transactions.length > 0">
        <table class="data-table">
          <thead>
            <tr>
              <th>Data</th>
              <th>Tipo</th>
              <th>Usuário Relacionado</th>
              <th class="text-right">Valor</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="txn in transactions" :key="txn.id">
              <td class="text-muted">{{ formatDate(txn.created_at) }}</td>
              <td>
                <span class="badge" :class="txn.type === 'credit' ? 'badge-credit' : 'badge-debit'">
                  {{ txn.type === 'credit' ? 'Crédito' : 'Débito' }}
                </span>
              </td>
              <td>
                <strong>{{ txn.related_user?.name || 'Desconhecido' }}</strong> <br/>
                <small class="text-muted">{{ txn.related_user?.email || '-' }}</small>
              </td>
              <td class="text-right" :class="txn.type === 'credit' ? 'text-credit' : 'text-debit'">
                {{ txn.type === 'credit' ? '+' : '-' }} {{ formatCurrency(txn.amount) }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="transactions.length === 0" class="text-center text-muted mt-lg">
        Nenhuma transação encontrada para os filtros selecionados.
      </div>

      <div class="pagination mt-lg" v-if="pagination.last_page > 1">
        <button 
          class="btn-outline"
          :disabled="filters.page === 1" 
          @click="changePage(filters.page - 1)"
        >Anterior</button>
        
        <span class="page-info">Página {{ filters.page }} de {{ pagination.last_page }}</span>

        <button 
          class="btn-outline"
          :disabled="filters.page === pagination.last_page" 
          @click="changePage(filters.page + 1)"
        >Próxima</button>
      </div>
    </div>
  </div>
</template>

<style scoped>
.history-card {
  max-width: 1000px;
  margin: 0 auto;
}

.history-header {
  margin-bottom: var(--spacing-md);
}

.section-title {
  color: var(--vt-c-indigo);
  font-weight: 700;
}

.text-muted {
  color: var(--color-text-muted);
}

.text-right {
  text-align: right !important;
}

.filters-bar {
  display: flex;
  gap: var(--spacing-md);
  padding: var(--spacing-md);
  background: var(--color-background-soft);
  border-radius: var(--radius);
  flex-wrap: wrap;
  align-items: flex-end;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.3rem;
  flex: 1;
  min-width: 200px;
}

.filter-group label {
  font-size: 0.85rem;
  font-weight: 600;
  color: var(--vt-c-indigo);
}

.form-control {
  width: 100%;
  padding: 0.6rem;
  border: 1px solid var(--color-border);
  border-radius: var(--radius);
  font-size: 0.95rem;
  background: var(--color-background);
  color: var(--color-text);
}

.form-control:focus {
  outline: none;
  border-color: var(--color-primary);
}

.table-responsive {
  overflow-x: auto;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: var(--spacing-md);
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

.badge {
  padding: 0.3rem 0.6rem;
  border-radius: var(--radius);
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
}

.badge-credit {
  background-color: rgba(40, 167, 69, 0.1);
  color: var(--color-primary);
}

.badge-debit {
  background-color: rgba(220, 53, 69, 0.1);
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

.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: var(--spacing-md);
}

.btn-outline {
  background: transparent;
  border: 1px solid var(--vt-c-indigo);
  color: var(--vt-c-indigo);
  font-weight: 600;
}

.btn-outline:hover:not(:disabled) {
  background: var(--vt-c-indigo);
  color: white;
}

.page-info {
  font-weight: 600;
  color: var(--color-text-muted);
}

.loading-pulse {
  animation: pulse 1.5s infinite;
}

@keyframes pulse {
  0% { opacity: 0.5; }
  50% { opacity: 1; }
  100% { opacity: 0.5; }
}
</style>