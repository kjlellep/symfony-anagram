import axios from 'axios';

const api = axios.create({
  baseURL: import.meta.env.VITE_API_URL
});

export const importWordbase = async () => {
  const response = await api.get('/import-wordbase');
  return response.data;
};

export const findAnagrams = async (word) => {
  const response = await api.get('/anagram', {
    params: { word }
  });
  return response.data;
};

export default api;
