<template>
    <section class="hero">
      <div class="hero-content">
        <h2>Descubra o Mundo Conosco</h2>
        <p>Encontre inspiração, filtre destinos e comece a planejar sua viagem dos sonhos.</p>
        <a href="#explore" class="cta-button">Explorar Destinos <i class="fas fa-arrow-down"></i></a>
      </div>
    </section>
  </template>
  
  <style scoped>
  .hero {
    background: url('/path/to/your/travel-background.jpg') no-repeat center center/cover; /* Use uma imagem de fundo legal! */
    /* Fallback com gradiente se a imagem falhar ou enquanto carrega */
    background-color: var(--primary-light);
    color: var(--text-light);
    padding: 60px 20px;
    text-align: center;
    border-radius: 8px;
    margin-bottom: 40px;
    position: relative; /* Para o overlay */
  }
  
  /* Overlay escuro para melhor contraste do texto sobre a imagem */
  .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: rgba(0, 0, 0, 0.4); /* Ajuste a opacidade */
      border-radius: 8px;
  }
  
  .hero-content {
      position: relative; /* Para ficar acima do overlay */
      z-index: 1;
  }
  
  
  h2 {
    font-size: 2.5em;
    margin-bottom: 15px;
    color: var(--text-light);
    text-shadow: 1px 1px 3px rgba(0,0,0,0.5);
     border-bottom: none; /* Remover borda do h2 geral */
  }
  
  p {
    font-size: 1.2em;
    margin-bottom: 30px;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
     text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
  }
  
  .cta-button {
    display: inline-block;
    padding: 12px 25px;
    background-color: var(--accent-color); /* Usando cor de destaque */
    color: var(--primary-dark); /* Texto escuro no botão claro */
    border-radius: 5px;
    text-decoration: none;
    font-weight: bold;
    transition: background-color 0.3s ease, transform 0.2s ease;
  }
  
  .cta-button:hover {
    background-color: #87CEEB; /* Um pouco mais escuro no hover */
     transform: scale(1.05);
  }
  .fa-arrow-down {
      margin-left: 8px;
  }
  </style>