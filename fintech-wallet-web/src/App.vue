<script setup>
import { onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from './stores/auth';

const authStore = useAuthStore();
const router = useRouter();

onMounted(() => {
  if (authStore.token) {
    authStore.fetchUser();
  }
});

const handleLogout = async () => {
  await authStore.logout();
  router.push('/login');
};
</script>

<template>
  <div>
    <header v-if="authStore.token" class="navbar">
      <nav>
        <RouterLink to="/">Dashboard</RouterLink> |
        <RouterLink to="/transfer">Nova Transferência</RouterLink> |
        <RouterLink to="/history">Histórico Completo</RouterLink> |
        <a href="#" @click.prevent="handleLogout">Sair</a>
      </nav>
    </header>

    <main class="container">
      <RouterView />
    </main>
  </div>
</template>

<style>
body {
  font-family: Arial, sans-serif;
  background-color: #f4f4f9;
  margin: 0;
  padding: 0;
}
.navbar {
  background-color: #333;
  padding: 1rem;
  text-align: center;
}
.navbar a {
  color: white;
  text-decoration: none;
  margin: 0 10px;
  font-weight: bold;
}
.navbar a:hover {
  text-decoration: underline;
}
.container {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 0 1rem;
}
</style>