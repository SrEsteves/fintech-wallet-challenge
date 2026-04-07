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
  <div class="history-container">
    <h2>Histórico de Transações</h2>

    <div class="filters-bar">
      <div class="filter-group">
        <label>Tipo</label>
        <select v-model="filters.type">
          <option value="">Todos</option>
          <option value="credit">Recebidos (Crédito)</option>
          <option value="debit">Enviados (Débito)</option>
        </select>
      </div>

      <div class="filter-group">
        <label>De:</label>
        <input type="date" v-model="filters.start_date" />
      </div>

      <div class="filter-group">
        <label>Até:</label>
        <input type="date" v-model="filters.end_date" />
      </div>
    </div>

    <div v-if="isLoading" class="loader">Carregando transações...</div>

    <div v-else>
      <table class="data-table">
        <thead>
          <tr>
            <th>Data</th>
            <th>Tipo</th>
            <th>Usuário Relacionado</th>
            <th>Valor</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="txn in transactions" :key="txn.id">
            <td>{{ formatDate(txn.created_at) }}</td>
            <td>
              <span :class="txn.type === 'credit' ? 'badge-credit' : 'badge-debit'">
                {{ txn.type === 'credit' ? 'Crédito' : 'Débito' }}
              </span>
            </td>
            <td>
              {{ txn.related_user?.name }} <br/>
              <small>{{ txn.related_user?.email }}</small>
            </td>
            <td :class="txn.type === 'credit' ? 'text-credit' : 'text-debit'">
              {{ txn.type === 'credit' ? '+' : '-' }} {{ formatCurrency(txn.amount) }}
            </td>
          </tr>
        </tbody>
      </table>

      <div class="pagination" v-if="pagination.last_page > 1">
        <button 
          :disabled="filters.page === 1" 
          @click="changePage(filters.page - 1)"
        >Anterior</button>
        
        <span>Página {{ filters.page }} de {{ pagination.last_page }}</span>

        <button 
          :disabled="filters.page === pagination.last_page" 
          @click="changePage(filters.page + 1)"
        >Próxima</button>
      </div>

      <div v-if="transactions.length === 0" class="empty">
        Nenhuma transação encontrada para os filtros selecionados.
      </div>
    </div>
  </div>
</template>

<style scoped>
.history-container {
  background: white;
  padding: 1.5rem;
  border-radius: 8px;
  box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.filters-bar {
  display: flex;
  gap: 1rem;
  margin-bottom: 2rem;
  padding: 1rem;
  background: #f8f9fa;
  border-radius: 4px;
  flex-wrap: wrap;
}

.filter-group {
  display: flex;
  flex-direction: column;
  gap: 0.25rem;
}

.filter-group label {
  font-size: 0.8rem;
  font-weight: bold;
  color: #666;
}

.filter-group select, .filter-group input {
  padding: 0.5rem;
  border: 1px solid #ddd;
  border-radius: 4px;
}

.data-table {
  width: 100%;
  border-collapse: collapse;
}

.data-table th, .data-table td {
  padding: 1rem;
  text-align: left;
  border-bottom: 1px solid #eee;
}

.badge-credit { background: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px; }
.badge-debit { background: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px; }
.text-credit { color: #28a745; font-weight: bold; }
.text-debit { color: #dc3545; font-weight: bold; }

.pagination {
  margin-top: 2rem;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 1rem;
}

.pagination button {
  padding: 0.5rem 1rem;
  cursor: pointer;
}

.loader, .empty {
  text-align: center;
  padding: 3rem;
  color: #666;
}
</style>