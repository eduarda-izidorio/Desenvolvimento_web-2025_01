<template>
    <div class="filter-controls">
      <div class="filter-group">
        <label for="continent-filter">Continente:</label>
        <select id="continent-filter" v-model="filters.continent" @change="emitFilterChange">
          <option value="all">Todos</option>
          <option value="Europa">Europa</option>
          <option value="Asia">Ásia</option>
          <option value="America Norte">América do Norte</option>
          <option value="America Sul">América do Sul</option>
          {/* Adicione outros continentes se necessário */}
        </select>
      </div>
      <div class="filter-group">
        <label for="type-filter">Tipo de Viagem:</label>
        <select id="type-filter" v-model="filters.type" @change="emitFilterChange">
          <option value="all">Todos</option>
          <option value="Cidade">Cidade</option>
          <option value="Praia">Praia</option>
          <option value="Montanha">Montanha</option>
          {/* Adicione outros tipos */}
        </select>
      </div>
    </div>
  </template>
  
  <script setup>
  import { reactive } from 'vue';
  
  const emit = defineEmits(['filterChange']);
  
  const filters = reactive({
    continent: 'all',
    type: 'all'
  });
  
  function emitFilterChange() {
    // Emite uma cópia para evitar mutação direta pelo pai
    emit('filterChange', { ...filters });
  }
  </script>
  
  <style scoped>
  .filter-controls {
    display: flex;
    flex-wrap: wrap; /* Permite que os filtros quebrem linha em telas menores */
    gap: 20px; /* Espaço entre grupos de filtro */
    margin-bottom: 20px;
    padding: 15px;
    background-color: #eef2f7; /* Um fundo levemente diferente */
    border-radius: 6px;
  }
  
  .filter-group {
    display: flex;
    align-items: center;
    gap: 8px; /* Espaço entre label e select */
  }
  
  label {
    font-weight: bold;
    color: var(--primary-dark);
  }
  
  select {
    padding: 8px 12px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    background-color: var(--card-background);
  }
  </style>