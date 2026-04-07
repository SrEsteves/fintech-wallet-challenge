import { defineStore } from 'pinia';
import { ref } from 'vue';
import api from '../services/api';

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null);
  const token = ref(localStorage.getItem('token') || null);
  const error = ref(null);

  const login = async (email, password) => {
    try {
      error.value = null;
      const response = await api.post('/login', { email, password });
      token.value = response.data.token;
      user.value = response.data.user;
      localStorage.setItem('token', token.value);
      return true;
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao fazer login';
      return false;
    }
  };

  const register = async (name, email, password) => {
    try {
      error.value = null;
      const response = await api.post('/register', { name, email, password });
      token.value = response.data.token;
      user.value = response.data.user;
      localStorage.setItem('token', token.value);
      return true;
    } catch (err) {
      error.value = err.response?.data?.message || 'Erro ao registrar';
      return false;
    }
  };

  const logout = async () => {
    try {
      if (token.value) {
        await api.post('/logout');
      }
    } finally {
      user.value = null;
      token.value = null;
      localStorage.removeItem('token');
    }
  };

  const fetchUser = async () => {
    if (!token.value) return;
    try {
      const response = await api.get('/me');
      user.value = response.data;
    } catch (err) {
      logout();
    }
  };

  return { user, token, error, login, register, logout, fetchUser };
});