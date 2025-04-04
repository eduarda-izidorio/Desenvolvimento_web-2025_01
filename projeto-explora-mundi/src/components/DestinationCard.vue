<template>
    <div class="destination-card">
      <img :src="destination.image" :alt="`Imagem de ${destination.name}`" class="card-image">
      <div class="card-content">
        <h3>{{ destination.name }}</h3>
        <p class="card-description">{{ destination.shortDescription }}</p>
        <div class="card-tags">
            <span class="tag">{{ destination.continent }}</span>
            <span class="tag tag-type">{{ destination.type }}</span>
        </div>
        <div class="card-actions">
          <!-- Funcionalidade Interativa 3 (Gatilho): Botão Detalhes -->
          <button @click="$emit('showDetails')" class="btn-details">
              <i class="fas fa-info-circle"></i> Detalhes
          </button>
          <!-- Funcionalidade Interativa 2 (Gatilho): Botão Adicionar -->
          <button @click="$emit('addToItinerary')">
              <i class="fas fa-plus-circle"></i> Roteiro
          </button>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  defineProps({
    destination: {
      type: Object,
      required: true
    }
  });
  defineEmits(['addToItinerary', 'showDetails']);
  </script>
  
  <style scoped>
  .destination-card {
    background-color: var(--card-background);
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    overflow: hidden; /* Garante que a imagem não ultrapasse as bordas arredondadas */
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    display: flex;
    flex-direction: column;
  }
  
  .destination-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
  }
  
  .card-image {
    width: 100%;
    height: 180px; /* Altura fixa para a imagem */
    object-fit: cover; /* Garante que a imagem cubra a área sem distorcer */
  }
  
  .card-content {
    padding: 15px;
    flex-grow: 1; /* Faz este div ocupar o espaço restante */
    display: flex;
    flex-direction: column;
  }
  
  h3 {
    margin-top: 0;
    margin-bottom: 10px;
    color: var(--primary-dark);
    font-size: 1.3em;
  }
  
  .card-description {
      font-size: 0.95em;
      color: #555;
      flex-grow: 1; /* Empurra as ações para baixo */
      margin-bottom: 15px;
  }
  
  .card-tags {
      margin-bottom: 15px;
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
  }
  
  .tag {
      background-color: #e0e0e0;
      color: #555;
      padding: 3px 8px;
      border-radius: 4px;
      font-size: 0.8em;
      font-weight: bold;
  }
  .tag-type {
       background-color: var(--accent-color);
       color: var(--primary-dark);
  }
  
  
  .card-actions {
    margin-top: auto; /* Empurra as ações para o final do card */
    display: flex;
    justify-content: space-between; /* Espaça os botões */
    gap: 10px;
  }
  
  .card-actions button {
      font-size: 0.9em;
      padding: 8px 12px;
  }
  .btn-details {
      background-color: transparent;
      color: var(--primary-color);
      border: 1px solid var(--primary-color);
  }
  .btn-details:hover {
       background-color: var(--primary-color);
       color: var(--text-light);
  }
  
  .fa-info-circle, .fa-plus-circle {
      margin-right: 5px;
  }
  </style>