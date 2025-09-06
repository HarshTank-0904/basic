const express = require('express');
const app = express();
const port = 3000;

require('dotenv').config();
const PORT = process.env.PORT;

app.get('/', (req, res) => {
  res.send('Hello World!');
});

app.get('/twitter', (req, res) => {
  res.send('Twitter Page');
});

app.get ('/login', (req, res) => {
  res.send('Login Page');
});

app.listen(PORT, () => {
  console.log(`Server is running at http://localhost:${PORT}`);
});

