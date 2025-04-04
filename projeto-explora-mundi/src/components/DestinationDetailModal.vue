<template>
    <div class="modal-overlay" @click.self="$emit('close')"> {/* Fecha ao clicar fora */}
      <div class="modal-content">
        <button class="modal-close" @click="$emit('close')">×</button>
        <h2>{{ destination.name }}</h2>
        <img :src="destination.image" :alt="destination.name" class="modal-image">
        <p><strong>Continente:</strong> {{ destination.continent }}</p>
        <p><strong>Tipo:</strong> {{ destination.type }}</p>
        <p>{{ destination.longDescription }}</p>
        <div v-if="destination.attractions && destination.attractions.length">
            <strong>Principais Atrações:</strong>
            <ul>
                <li v-for="attraction in destination.attractions" :key="attraction">{{ attraction }}</li>
            </ul>
        </div>
        <p v-if="destination.bestTime"><strong>Melhor Época para Visitar:</strong> {{ destination.bestTime }}</p>
         <button @click="$emit('close')" class="btn-modal-ok">Fechar</button>
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
  defineEmits(['close']);
  </script>
  
  <style scoped>
  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6); /* Fundo escuro semi-transparente */
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000; /* Garante que fique acima de tudo */
  }
  
  .modal-content {
    background-color: var(--card-background);
    padding: 30px;
    border-radius: 8px;
    max-width: 600px; /* Largura máxima do modal */
    width: 90%; /* Responsivo */
    max-height: 85vh; /* Altura máxima */
    overflow-y: auto; /* Permite scroll se o conteúdo for grande */
    position: relative;
    box-shadow: 0 5px 15px rgba(0,0,0,0.3);
  }
  
  .modal-close {
    position: absolute;
    top: 10px;
    right: 15px;
    background: none;
    border: none;
    font-size: 2em;
    color: #aaa;
    cursor: pointer;
    line-height: 1;
  }
  .modal-close:hover {
      color: #333;
      background: none; /* Sobrescreve hover geral */
      transform: none;
  }
  
  h2 {
    margin-top: 0;
    color: var(--primary-color);
    border-bottom: 2px solid var(--primary-light);
    padding-bottom: 10px;
    margin-bottom: 20px;
  }
  
  .modal-image {
    width: 100%;
    max-height: 250px; /* Limita altura da imagem no modal */
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 20px;
  }
  
  ul {
      list-style: disc;
      margin-left: 20px;
  }
  
  strong {
      color: var(--primary-dark);
  }
  
  .btn-modal-ok {
      display: block;
      width: fit-content;
      margin: 25px auto 0 auto; /* Centraliza o botão de fechar */
      /* Usa estilo padrão de botão */
  }
  </style>