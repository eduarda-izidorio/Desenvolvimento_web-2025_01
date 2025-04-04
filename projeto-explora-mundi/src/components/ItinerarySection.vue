<template>
    <section id="itinerary" class="section itinerary-section">
      <h2><i class="fas fa-map-marked-alt"></i> Meu Roteiro</h2>
      <div v-if="destinations.length === 0" class="empty-itinerary">
        Seu roteiro está vazio. Adicione destinos da lista acima!
      </div>
      <ul v-else class="itinerary-list">
        <li v-for="dest in destinations" :key="dest.id" class="itinerary-item">
          <span>
              <img :src="dest.image" :alt="dest.name" class="item-thumbnail">
               <strong>{{ dest.name }}</strong> ({{ dest.type }})
          </span>
          <button @click="$emit('removeFromItinerary', dest.id)" class="btn-remove" title="Remover do roteiro">
              <i class="fas fa-trash-alt"></i>
          </button>
        </li>
      </ul>
      <button
        v-if="destinations.length > 0"
        @click="$emit('clearItinerary')"
        class="btn-clear">
        <i class="fas fa-times-circle"></i> Limpar Roteiro
      </button>
    </section>
  </template>
  
  <script setup>
  defineProps({
    destinations: {
      type: Array,
      required: true
    }
  });
  defineEmits(['removeFromItinerary', 'clearItinerary']);
  </script>
  
  <style scoped>
  .itinerary-section {
    background-color: #e6e6fa; /* Lavender - um roxo bem claro para destacar */
  }
  h2 {
      color: var(--primary-dark);
      border-bottom-color: var(--primary-dark);
  }
  .fa-map-marked-alt {
      margin-right: 10px;
      color: var(--primary-color);
  }
  
  .empty-itinerary {
    text-align: center;
    color: #666;
    padding: 20px;
    font-style: italic;
  }
  
  .itinerary-list {
    list-style: none;
    padding: 0;
    margin: 0 0 20px 0;
  }
  
  .itinerary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    border-bottom: 1px solid #d8cff0; /* Linha divisória lilás */
    transition: background-color 0.2s ease;
  }
  .itinerary-item:last-child {
      border-bottom: none;
  }
  .itinerary-item:hover {
      background-color: #f0ebf9;
  }
  
  
  .itinerary-item span {
      display: flex;
      align-items: center;
      gap: 10px;
  }
  .item-thumbnail {
      width: 40px;
      height: 40px;
      object-fit: cover;
      border-radius: 50%; /* Imagem redonda */
      border: 1px solid var(--border-color);
  }
  
  
  .btn-remove {
    background: none;
    border: none;
    color: #e74c3c; /* Vermelho para remover */
    cursor: pointer;
    font-size: 1.1em;
    padding: 5px;
  }
  .btn-remove:hover {
    color: #c0392b; /* Vermelho mais escuro */
    background: none; /* Sobrescreve hover geral */
    transform: scale(1.1);
  }
  
  .btn-clear {
    display: block; /* Ocupa a largura total */
    margin: 15px auto 0 auto; /* Centraliza e adiciona espaço */
    background-color: #bdc3c7; /* Cinza */
    color: #34495e; /* Cinza escuro */
  }
  .btn-clear:hover {
    background-color: #abb5bd;
  }
  .fa-times-circle {
      margin-right: 5px;
  }
  </style>