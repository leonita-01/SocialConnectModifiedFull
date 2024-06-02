import axios from 'axios';

const instance = axios.create({
  baseURL: 'http://localhost:8000/api', // Update with your API URL
});

export default instance;
