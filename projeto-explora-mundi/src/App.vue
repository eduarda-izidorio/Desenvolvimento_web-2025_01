<template>
  <TheHeader />

  <main>
    <!-- Seção 1: Hero/Inspiração -->
    <HeroSection />

    <!-- Seção 2: Explorar Destinos (com lista e filtros) -->
    <section id="explore" class="section">
      <h2>Explore Nossos Destinos</h2>
      <DestinationList
        @add-to-itinerary="addDestination"
        @show-details="showDestinationDetails"
      />
    </section>

    <!-- Seção 3: Meu Roteiro -->
    <ItinerarySection
        :destinations="itinerary"
        @remove-from-itinerary="removeDestination"
        @clear-itinerary="clearItinerary"
    />

    <!-- Seção 4: Formulário de Contato -->
    <section id="contact" class="section">
      <h2>Fale Conosco</h2>
      <p>Tem dúvidas sobre um destino ou quer ajuda para planejar? Envie sua mensagem!</p>
      <ContactForm />
    </section>

  </main>

  <TheFooter />

  <!-- Modal para detalhes do destino (Renderizado condicionalmente) -->
  <!-- Este componente só existe no DOM quando selectedDestination não for null -->
  <DestinationDetailModal
    v-if="selectedDestination"
    :destination="selectedDestination"
    @close="closeDestinationDetails"
  />

</template>

<script setup>
// --- Imports ---
import { ref } from 'vue'; // Importa a função ref para criar estado reativo
// Importa os componentes filhos que serão usados no template
import TheHeader from './components/TheHeader.vue';
import HeroSection from './components/HeroSection.vue';
import DestinationList from './components/DestinationList.vue';
import ItinerarySection from './components/ItinerarySection.vue';
import TheFooter from './components/TheFooter.vue';
import DestinationDetailModal from './components/DestinationDetailModal.vue';
import ContactForm from './components/ContactForm.vue';



// --- Estado Reativo ---
// Array que armazena os destinos adicionados ao roteiro pelo usuário
const itinerary = ref([]);
// Armazena o objeto do destino selecionado para exibição no modal.
// Inicia como null, o que significa que o modal está fechado.
const selectedDestination = ref(null);

// --- Funções (Manipuladores de Eventos e Lógica) ---

/**
 * Adiciona um destino ao array 'itinerary'.
 * Verifica se o destino já existe para evitar duplicatas.
 * @param {object} destination - O objeto de destino a ser adicionado.
 */
function addDestination(destination) {
  // Verifica se algum item no itinerário atual já tem o mesmo ID do destino a ser adicionado
  const alreadyExists = itinerary.value.some(item => item.id === destination.id);

  if (!alreadyExists) {
    // Se não existe, adiciona o destino ao final do array 'itinerary'
    itinerary.value.push(destination);
    console.log('Adicionado ao roteiro:', destination.name); // Log para depuração
     // Poderia adicionar um feedback visual (ex: toast notification) aqui
  } else {
    // Se já existe, apenas informa no console (poderia ser um alerta na UI)
    console.warn(`Destino "${destination.name}" já está no roteiro!`);
  }
}

/**
 * Remove um destino do array 'itinerary' com base no seu ID.
 * @param {number} destinationId - O ID do destino a ser removido.
 */
function removeDestination(destinationId) {
  console.log('Tentando remover destino com ID:', destinationId); // Log para depuração
  // Cria um NOVO array contendo apenas os itens cujo ID NÃO é igual ao destinationId fornecido.
  // Atribuir este novo array a itinerary.value dispara a reatividade do Vue.
  itinerary.value = itinerary.value.filter(item => item.id !== destinationId);
}

/**
 * Limpa completamente o array 'itinerary', removendo todos os destinos.
 */
function clearItinerary() {
  itinerary.value = []; // Simplesmente atribui um array vazio
  console.log('Roteiro limpo.'); // Log para depuração
}

/**
 * Define qual destino deve ser exibido no modal de detalhes.
 * Atualiza a variável reativa 'selectedDestination'.
 * @param {object} destination - O objeto de destino clicado.
 */
function showDestinationDetails(destination) {
  selectedDestination.value = destination; // Define o destino a ser mostrado
  console.log('Mostrando detalhes de:', destination.name); // Log para depuração
}

/**
 * Fecha o modal de detalhes.
 * Reseta a variável reativa 'selectedDestination' para null.
 */
function closeDestinationDetails() {
  selectedDestination.value = null; // Reseta para null, escondendo o modal (v-if="selectedDestination")
  console.log('Modal de detalhes fechado.'); // Log para depuração
}
</script>

<style scoped>
/* Estilos específicos para o layout dentro de App.vue */
.section {
  margin-bottom: 40px; /* Espaçamento entre as seções principais */
  padding: 25px; /* Espaçamento interno das seções */
  border-radius: 8px; /* Bordas arredondadas */
  background-color: var(--card-background); /* Fundo branco ou definido em main.css */
  box-shadow: 0 3px 6px rgba(0,0,0,0.08); /* Sombra suave */
}

h2 {
  /* Estilo padrão para títulos de seção */
  color: var(--primary-dark); /* Cor escura da paleta */
  border-bottom: 3px solid var(--primary-color); /* Linha inferior na cor principal */
  padding-bottom: 10px; /* Espaço entre o texto e a linha */
  margin-top: 0; /* Remove margem superior padrão do h2 */
  margin-bottom: 25px; /* Espaço abaixo do título */
}

/* Ajustes de responsividade se necessário para o layout geral do App.vue */
@media (max-width: 768px) {
  .section {
    padding: 20px;
    margin-bottom: 30px;
  }
  h2 {
    font-size: 1.5em; /* Reduz tamanho da fonte em telas menores */
    margin-bottom: 20px;
  }
  #contact p { 
    font-size: 1em; margin-bottom: 25px; 
  }
}

#contact p {
    text-align: center;
    max-width: 600px;
    margin: 0 auto 30px auto; /* Centraliza e adiciona espaço abaixo */
    color: #555;
    font-size: 1.05em;
}

</style>