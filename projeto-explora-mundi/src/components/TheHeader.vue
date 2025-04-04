<template>
    <header class="main-header">
      <h1><i class="fas fa-compass"></i> Explora Mundi</h1>
      <p>Seu ponto de partida para a próxima aventura!</p>
    </header>
  </template>
  
  <script setup>
  // Importar Font Awesome se for usar ícones (via main.js ou index.html)
  </script>
  
  <style scoped>
  .main-header {
    background: linear-gradient(to right, var(--primary-dark), var(--primary-color));
    color: var(--text-light);
    padding: 20px 30px;
    text-align: center;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
  }
  h1 {
    margin: 0 0 5px 0;
    font-size: 2.2em;
  }
  p {
      margin: 0;
      font-size: 1.1em;
      opacity: 0.9;
  }
  .fa-compass { /* Estilo para o ícone (exemplo) */
      margin-right: 10px;
      color: var(--accent-color);
  }
  </style>