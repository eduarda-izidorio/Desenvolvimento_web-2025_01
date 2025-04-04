# Vue 3 + Vite

This template should help get you started developing with Vue 3 in Vite. The template uses Vue 3 `<script setup>` SFCs, check out the [script setup docs](https://v3.vuejs.org/api/sfc-script-setup.html#sfc-script-setup) to learn more.

Learn more about IDE Support for Vue in the [Vue Docs Scaling up Guide](https://vuejs.org/guide/scaling-up/tooling.html#ide-support).



# 📄 Documentação do Projeto - Explora Mundi 🌍

**Aluno:** [Seu Nome Completo Aqui]
**Matéria:** Desenvolvimento Web
**Curso:** Sistemas de Informação
**Framework/Biblioteca Utilizada:** Vue.js 3 (com Vite)
**Tema:** Planejador de Viagens Simplificado
**Paleta de Cores:** Roxo-Azulado (`#6A5ACD`, `#483D8B`, etc.)

---

## 📜 Tabela de Conteúdos

1.  [Introdução](#1-introdução-)
2.  [Escolha da Biblioteca/Framework (Vue.js)](#2-escolha-da-bibliotecaframework-vuejs-)
3.  [Estrutura do Projeto](#3-estrutura-do-projeto-)
4.  [Funcionalidades Implementadas](#4-funcionalidades-implementadas-)
5.  [Utilização do Vue.js no Projeto](#5-utilização-do-vuejs-no-projeto-)
6.  [Design Responsivo](#6-design-responsivo-)
7.  [Instruções para Execução e Teste](#7-instruções-para-execução-e-teste-%EF%B8%8F)
8.  [Desafios Encontrados e Soluções](#8-desafios-encontrados-e-soluções-)
9.  [Conclusão](#9-conclusão-)

---

## 1. Introdução 🚀

Este documento detalha o processo de desenvolvimento da página web interativa "Explora Mundi", um planejador de viagens simplificado, como parte da avaliação da disciplina de Desenvolvimento Web. O objetivo foi criar uma página com múltiplas seções, funcionalidades dinâmicas (exploração de destinos, montagem de roteiro, formulário de contato) e design responsivo, utilizando HTML, CSS, JavaScript e o framework Vue.js, seguindo uma paleta de cores roxo-azulado.

## 2. Escolha da Biblioteca/Framework (Vue.js) 💡

A escolha do **Vue.js** (versão 3, utilizando a Composition API com `<script setup>`) foi motivada pelos seguintes fatores:

*   **Curva de Aprendizado:** Vue.js é conhecido por sua curva de aprendizado acessível, ideal para projetos acadêmicos com prazos definidos.
*   **Reatividade:** O sistema de reatividade do Vue (`ref`, `reactive`, `computed`) simplifica a sincronização entre os dados da aplicação e a interface do usuário, tornando o desenvolvimento de funcionalidades como filtros e listas dinâmicas mais intuitivo.
*   **Componentização:** A estrutura baseada em componentes (`.vue` files) permitiu dividir a interface em partes reutilizáveis e gerenciáveis (Header, Footer, Card de Destino, Lista, Filtro, Modal, Seção de Roteiro, Formulário), promovendo código organizado e de fácil manutenção.
*   **Performance:** Vue.js oferece excelente performance. O uso do Vite como ferramenta de build (`npm run dev`, `npm run build`) acelera o desenvolvimento com Hot Module Replacement (HMR) rápido e otimiza os arquivos finais para produção.
*   **Documentação e Comunidade:** Possui documentação oficial clara e uma comunidade ativa, facilitando a resolução de dúvidas.

## 3. Estrutura do Projeto 📁

O projeto foi inicializado utilizando `npm create vite@latest` com o template para Vue. A estrutura de pastas adotada foi:

projeto-explora-mundi/
├── public/
│ └── images/ # Imagens estáticas dos destinos
├── src/
│ ├── assets/
│ │ └── main.css # CSS Global (incluindo variáveis de cor)
│ ├── components/ # Componentes Vue reutilizáveis (.vue)
│ │ ├── TheHeader.vue
│ │ ├── HeroSection.vue
│ │ ├── DestinationList.vue
│ │ ├── DestinationFilter.vue
│ │ ├── DestinationCard.vue
│ │ ├── DestinationDetailModal.vue
│ │ ├── ItinerarySection.vue
│ │ ├── ContactForm.vue
│ │ └── TheFooter.vue
│ ├── App.vue # Componente raiz da aplicação
│ └── main.js # Ponto de entrada (inicializa Vue, importa CSS global)
├── .gitignore
├── index.html # HTML base (Vite injeta o app aqui)
├── package.json # Dependências (Vue, Vite, FontAwesome) e scripts
├── vite.config.js # Configuração do Vite
└── DOCUMENTACAO.md # Este arquivo



## 4. Funcionalidades Implementadas ✨

A página "Explora Mundi" é dividida em quatro seções principais:

1.  **Seção Hero/Inspiração:** Introdução visualmente atraente com um call-to-action para explorar os destinos.
2.  **Seção Explorar Destinos:** Apresenta os destinos disponíveis em formato de cards. Inclui:
    *   **Funcionalidade 1: Filtro de Destinos:** Permite ao usuário filtrar os destinos exibidos por continente (Europa, Ásia, etc.) e tipo de viagem (Cidade, Praia, Montanha) usando menus `<select>`. A lista de cards se atualiza dinamicamente.
3.  **Seção Meu Roteiro:** Exibe os destinos que o usuário adicionou ao seu roteiro. Inclui:
    *   **Funcionalidade 2: Adicionar/Remover do Roteiro:** Botões nos cards de destino permitem adicionar o local à seção "Meu Roteiro". Dentro desta seção, cada item possui um botão para remoção individual. Um botão adicional permite limpar todo o roteiro. As atualizações são refletidas instantaneamente.
4.  **Seção Fale Conosco:** Contém um formulário para contato. Inclui:
    *   **Funcionalidade 3: Formulário com Validação e Envio Simulado:** O formulário possui campos para Nome, Email e Mensagem. A validação ocorre no lado do cliente (campos obrigatórios, formato de email válido, tamanho mínimo da mensagem) com feedback visual e mensagens de erro específicas. O envio é simulado com um atraso artificial, mostrando estados de "Enviando...", "Sucesso" ou "Erro", e o formulário é limpo após envio bem-sucedido.

Além disso, foi implementada uma **Funcionalidade Bônus:**
*   **Detalhes do Destino (Modal):** Ao clicar no botão "Detalhes" em um card de destino, uma janela modal (pop-up) é exibida com informações mais completas (descrição longa, principais atrações, melhor época para visitar).

## 5. Utilização do Vue.js no Projeto ⚙️

O Vue.js foi aplicado extensivamente para criar a interatividade e estrutura da página:

*   **Componentização:** A UI foi quebrada nos componentes listados na estrutura do projeto, cada um com seu template, script (`<script setup>`) e estilos (`<style scoped>`). `App.vue` orquestra a disposição destes componentes.
*   **Reatividade (`ref`, `reactive`, `computed`):**
    *   `ref` foi usado para estados primitivos ou arrays/objetos simples que podem ser substituídos (ex: `itinerary = ref([])`, `selectedDestination = ref(null)`).
    *   `reactive` foi usado para objetos onde as propriedades internas são modificadas (ex: `formData` no formulário, `errors` de validação, `activeFilters` nos filtros).
    *   `computed` (`filteredDestinations` em `DestinationList.vue`) foi crucial para criar a lista filtrada de forma declarativa e eficiente, recalculando-a apenas quando os filtros (`activeFilters`) mudam.
*   **Directivas:**
    *   `v-model` para two-way data binding nos campos de formulário (`ContactForm.vue`) e nos `<select>` dos filtros (`DestinationFilter.vue`).
    *   `v-for` para renderizar a lista de cards de destino (`DestinationList.vue`) e os itens do roteiro (`ItinerarySection.vue`), usando `:key` para otimização.
    *   `v-if` para renderização condicional do modal de detalhes (`DestinationDetailModal` em `App.vue`), mensagens de erro/sucesso no formulário e a mensagem de "roteiro vazio".
    *   `v-bind` (ou a forma curta `:`) para passar dados (props) para componentes filhos (ex: `:destination="dest"`), para definir atributos dinamicamente (ex: `:disabled="isSubmitting"`) e para classes condicionais (ex: `:class="{'has-error': errors.name}"`).
    *   `v-on` (ou a forma curta `@`) para ouvir eventos DOM (ex: `@click`, `@submit.prevent`) e eventos customizados emitidos por componentes filhos (ex: `@add-to-itinerary`, `@filter-change`, `@remove-from-itinerary`, `@close`).
*   **Props & Emits:** O padrão de comunicação principal entre componentes:
    *   **Props (`defineProps`)**: Componentes filhos recebem dados dos pais (ex: `ItinerarySection` recebe `destinations` de `App.vue`).
    *   **Emits (`defineEmits`, `$emit`)**: Componentes filhos notificam os pais sobre eventos ocorridos (ex: `DestinationCard` emite `addToItinerary` para `DestinationList`, que repassa para `App.vue`; `ItinerarySection` emite `removeFromItinerary` com o ID para `App.vue`).
*   **Gerenciamento de Estado:** Para este projeto, o estado compartilhado (roteiro, destino selecionado no modal) foi gerenciado diretamente em `App.vue` e passado para baixo via props e atualizado via eventos emitidos para cima. Para aplicações maiores, bibliotecas como Pinia seriam consideradas.

## 6. Design Responsivo 📱💻

A responsividade foi implementada utilizando CSS, focando em:

*   **Layout Flexbox e Grid:** `display: flex` para estrutura geral (`App.vue`, alinhamento em cards) e `display: grid` com `repeat(auto-fit, minmax(280px, 1fr))` para a lista de destinos (`DestinationList.vue`), permitindo que os cards se ajustem automaticamente à largura da tela.
*   **Unidades Relativas:** Uso de `em`, `rem`, `%`, `vh`, `vw` para fontes, paddings e larguras, permitindo melhor escalabilidade.
*   **Media Queries:** Regras `@media (max-width: ...)` foram usadas em `main.css` e nos estilos `scoped` dos componentes para ajustar layouts (ex: empilhar filtros, reduzir paddings, diminuir fontes) em breakpoints específicos (ex: 768px, 480px).
*   **Imagens Flexíveis:** Imagens nos cards (`DestinationCard.vue`) usam `width: 100%`, `height` fixa e `object-fit: cover` para preencher o espaço designado sem distorcer, adaptando-se ao tamanho do card.

## 7. Instruções para Execução e Teste 🛠️

Para executar o projeto "Explora Mundi" localmente:

1.  **Pré-requisitos:** Instale o [Node.js](https://nodejs.org/) (que inclui npm).
2.  **Clone/Download:** Obtenha os arquivos do projeto (se estiver no GitHub, use `git clone [URL_DO_REPOSITORIO]`).
3.  **Navegue até a Pasta:** Abra um terminal e use `cd` para entrar na pasta do projeto (`cd projeto-explora-mundi`).
4.  **Instale as Dependências:**
    ```bash
    npm install
    ```
    Isso baixará o Vue, Vite, Font Awesome e outras dependências listadas no `package.json`.
5.  **Execute em Modo de Desenvolvimento:**
    ```bash
    npm run dev
    ```
    Isso iniciará o servidor de desenvolvimento Vite. O terminal mostrará o endereço local (geralmente `http://localhost:5173`).
6.  **Abra no Navegador:** Acesse o endereço fornecido.
7.  **Teste as Funcionalidades:**
    *   Use os filtros na seção de destinos.
    *   Clique em "Detalhes" nos cards para abrir o modal.
    *   Clique em "Roteiro" nos cards para adicionar itens à seção "Meu Roteiro".
    *   Remova itens individualmente do roteiro clicando na lixeira.
    *   Use o botão "Limpar Roteiro".
    *   Preencha o formulário de contato com dados válidos e inválidos, observe a validação e o feedback de envio simulado.
    *   Redimensione a janela do navegador ou use as ferramentas de desenvolvedor (F12) para testar a responsividade.
8.  **Build para Produção (Opcional):**
    ```bash
    npm run build
    ```
    Cria uma versão otimizada na pasta `dist/`, pronta para deploy.

## 8. Desafios Encontrados e Soluções 🐛✅

*   **Desafio:** Gerenciar o estado compartilhado (lista do roteiro) entre múltiplos componentes (`DestinationList`, `ItinerarySection`).
    *   **Solução:** Centralizar o estado no componente pai comum (`App.vue`) usando `ref`. Utilizar o padrão "props down, events up": `App.vue` passa o estado para os filhos via props, e os filhos emitem eventos para `App.vue` quando uma mudança é necessária (adicionar, remover).
*   **Desafio:** Implementar o modal de detalhes de forma eficiente.
    *   **Solução:** Criar um componente `DestinationDetailModal.vue`. Usar uma variável reativa (`selectedDestination = ref(null)`) em `App.vue` para controlar a visibilidade do modal com `v-if`. Passar os dados do destino selecionado via props para o modal. O modal emite um evento `@close` para que `App.vue` possa resetar `selectedDestination` para `null`.
*   **Desafio:** Garantir validação clara e feedback útil no formulário de contato.
    *   **Solução:** Criar um objeto `errors` reativo. Implementar uma função `validateForm` que atualiza esse objeto. Usar `v-if` para mostrar mensagens de erro específicas e classes CSS condicionais (`:class`) para destacar campos inválidos. Utilizar estados `isSubmitting` e `submitStatus` para controlar o botão de envio e exibir mensagens gerais de feedback (carregando, sucesso, erro).
*   **Desafio:** Configurar corretamente os ícones do Font Awesome com Vite/Vue.
    *   **Solução:** Instalar via `npm`, importar o CSS principal (`all.min.css`) em `main.js` após o CSS global, e garantir o uso das classes corretas (ex: `fas fa-nome-do-icone`) no HTML. Verificar a aba "Network" do navegador por erros 404 nos arquivos de fonte (`.woff2`) foi crucial para depuração inicial.

## 9. Conclusão 🎉

O desenvolvimento do "Explora Mundi" permitiu aplicar conceitos essenciais de desenvolvimento web front-end moderno. O uso do Vue.js facilitou a criação de uma interface reativa, componentizada e interativa. As funcionalidades de filtragem dinâmica, manipulação de lista (roteiro), exibição de detalhes em modal e validação de formulário foram implementadas com sucesso, atendendo aos requisitos do projeto. A adoção de uma paleta de cores e o foco na responsividade contribuíram para uma experiência de usuário mais agradável. O projeto demonstra a capacidade de utilizar um framework JavaScript moderno para construir aplicações web funcionais e bem estruturadas.
