<template>
    <footer class="main-footer">
       <p>© {{ currentYear }} Explora Mundi por Carla Eduarda A. Izidório. </p>
       <p>Trabalho de Desenvolvimento Web.</p>
     </footer>
   </template>
   
   <script setup>
   import { ref } from 'vue';
   const currentYear = ref(new Date().getFullYear());
   </script>
   
   <style scoped>
    .main-footer {
     background-color: var(--primary-dark); /* Tom mais escuro do roxo azulado */
     color: #e0e0e0; /* Texto claro */
     padding: 20px 30px;
     text-align: center;
     margin-top: auto; /* Empurra para baixo */
     font-size: 0.9em;
   }
   p {
       margin: 5px 0;
   }
   </style>