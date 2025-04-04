<template>
    <div>
      <!-- Funcionalidade Interativa 1: Filtros -->
      <DestinationFilter @filter-change="updateFilters" />
  
      <div v-if="filteredDestinations.length === 0" class="no-results">
        Nenhum destino encontrado com os filtros selecionados.
      </div>
  
      <div v-else class="destination-grid">
        <DestinationCard
          v-for="destination in filteredDestinations"
          :key="destination.id"
          :destination="destination"
          @add-to-itinerary="$emit('addToItinerary', destination)"
          @show-details="$emit('showDetails', destination)"
        />
      </div>
    </div>
  </template>
  
  <script setup>
  import { ref, reactive, computed } from 'vue';
  import DestinationFilter from './DestinationFilter.vue';
  import DestinationCard from './DestinationCard.vue';
  
  // Definir os emits que este componente pode disparar
  const emit = defineEmits(['addToItinerary', 'showDetails']);
  
  // Dados Mockados (Substitua por dados reais ou de API se quiser)
  const allDestinations = ref([
    { id: 1, name: 'Paris, França', continent: 'Europa', type: 'Cidade', image: '/images/paris.jpg', shortDescription: 'A cidade da luz, romance e arte.', longDescription: 'Explore museus icônicos, a Torre Eiffel e charmosos cafés parisienses.', attractions: ['Torre Eiffel', 'Louvre', 'Notre Dame'], bestTime: 'Primavera/Outono' },
    { id: 2, name: 'Tóquio, Japão', continent: 'Asia', type: 'Cidade', image: '/images/toquio.jpg', shortDescription: 'Metrópole vibrante de cultura e tecnologia.', longDescription: 'Descubra templos antigos ao lado de arranha-céus futuristas e culinária excepcional.', attractions: ['Cruzamento de Shibuya', 'Templo Senso-ji', 'Palácio Imperial'], bestTime: 'Primavera/Outono' },
    { id: 3, name: 'Rio de Janeiro, Brasil', continent: 'America Sul', type: 'Praia', image: '/images/rio.jpg', shortDescription: 'Praias famosas, montanhas e samba.', longDescription: 'Visite o Cristo Redentor, Pão de Açúcar e relaxe nas praias de Copacabana e Ipanema.', attractions: ['Cristo Redentor', 'Pão de Açúcar', 'Praia de Copacabana'], bestTime: 'Verão (Dez-Mar)' },
    { id: 4, name: 'Banff, Canadá', continent: 'America Norte', type: 'Montanha', image: '/images/banff.jpg', shortDescription: 'Paisagens alpinas deslumbrantes e lagos.', longDescription: 'Explore o Parque Nacional de Banff, caminhe por trilhas e admire lagos de cor turquesa.', attractions: ['Lake Louise', 'Moraine Lake', 'Banff Gondola'], bestTime: 'Verão (Jun-Ago)' },
    { id: 5, name: 'Roma, Itália', continent: 'Europa', type: 'Cidade', image: '/images/roma.png', shortDescription: 'História antiga, Vaticano e comida deliciosa.', longDescription: 'Mergulhe na história no Coliseu e Fórum Romano, visite o Vaticano e saboreie a autêntica culinária italiana.', attractions: ['Coliseu', 'Fórum Romano', 'Vaticano', 'Fontana di Trevi'], bestTime: 'Primavera/Outono' },
    { id: 6, name: 'Maui, Havaí', continent: 'America Norte', type: 'Praia', image: '/images/maui.jpg', shortDescription: 'Praias paradisíacas, vulcões e surf.', longDescription: 'Relaxe em praias de areia dourada, explore o vulcão Haleakala e aventure-se na Road to Hana.', attractions: ['Haleakala National Park', 'Road to Hana', 'Kaanapali Beach'], bestTime: 'Ano todo (menos chuvoso Abril-Outubro)' },
  
  ]);
  
  // Estado para os filtros ativos
  const activeFilters = reactive({
    continent: 'all',
    type: 'all'
  });
  
  // Função para atualizar os filtros quando o componente filho emitir
  function updateFilters(newFilters) {
    activeFilters.continent = newFilters.continent;
    activeFilters.type = newFilters.type;
  }
  
  // Propriedade Computada para filtrar os destinos
  const filteredDestinations = computed(() => {
    return allDestinations.value.filter(dest => {
      const continentMatch = activeFilters.continent === 'all' || dest.continent === activeFilters.continent;
      const typeMatch = activeFilters.type === 'all' || dest.type === activeFilters.type;
      return continentMatch && typeMatch;
    });
  });
  
  </script>
  
  <style scoped>
  .destination-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); /* Ajuste o minmax conforme necessário */
    gap: 25px;
    margin-top: 30px;
  }
  .no-results {
    text-align: center;
    color: #666;
    margin-top: 40px;
    font-style: italic;
  }
  </style>