import axios from 'axios';

const api = axios.create({
//   baseURL: "http://localhost:8000/api",
  baseURL: "https://wasiyki-api.zettasky.com/api",
  headers: {
    "Content-Type": "application/json",
    Accept: "application/json",
  },
});

// Interceptor para inyectar el token en las peticiones protegidas
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('token');
    if (token) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
}, (error) => {
    return Promise.reject(error);
});

export default api;