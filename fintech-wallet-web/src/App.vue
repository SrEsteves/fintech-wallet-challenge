<script setup>
import { onMounted, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from './stores/auth'

const authStore = useAuthStore()
const router = useRouter()

const isAuthenticated = computed(() => !!authStore.token)
const userName = computed(() => authStore.user?.name || '')

onMounted(async () => {
  // evita chamada desnecessária
  if (authStore.token && !authStore.user) {
    try {
      await authStore.fetchUser()
    } catch (e) {
      // se o token for inválido, força logout
      await authStore.logout()
      router.push('/login')
    }
  }
})

const handleLogout = async () => {
  try {
    await authStore.logout()
  } finally {
    router.push('/login')
  }
}
</script>

<template>
  <div>
    <header v-if="isAuthenticated" class="navbar">
      <nav class="nav-content">
        <div class="nav-left">
          <RouterLink to="/">Dashboard</RouterLink>
          <RouterLink to="/transfer">Nova Transferência</RouterLink>
          <RouterLink to="/history">Histórico</RouterLink>
        </div>

        <div class="nav-right">
          <span class="user-name" v-if="userName">
            Olá, {{ userName }}
          </span>
          <button class="logout-btn" @click="handleLogout">
            Sair
          </button>
        </div>
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
}

.nav-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.nav-left a {
  color: white;
  text-decoration: none;
  margin-right: 15px;
  font-weight: bold;
}

.nav-left a:hover {
  text-decoration: underline;
}

.nav-right {
  display: flex;
  align-items: center;
  gap: 10px;
}

.user-name {
  color: #ddd;
  font-size: 0.9rem;
}

.logout-btn {
  background-color: #dc3545;
  color: white;
  border: none;
  padding: 0.4rem 0.7rem;
  border-radius: 4px;
  cursor: pointer;
}

.logout-btn:hover {
  opacity: 0.9;
}

.container {
  width: 1200px;
  margin: 2rem auto;
  padding: 0 1rem;
}
</style>