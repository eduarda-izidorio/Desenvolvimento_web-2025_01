<template>
    <form @submit.prevent="handleSubmit" class="contact-form" novalidate>
      <div :class="['form-group', { 'has-error': errors.name }]">
        <label for="name">Nome:</label>
        <input
          type="text"
          id="name"
          v-model.trim="formData.name"
          required
          aria-describedby="name-error"
        >
        <span v-if="errors.name" id="name-error" class="error-message">{{ errors.name }}</span>
      </div>
  
      <div :class="['form-group', { 'has-error': errors.email }]">
        <label for="email">Email:</label>
        <input
          type="email"
          id="email"
          v-model.trim="formData.email"
          required
          aria-describedby="email-error"
        >
         <span v-if="errors.email" id="email-error" class="error-message">{{ errors.email }}</span>
      </div>
  
      <div :class="['form-group', { 'has-error': errors.message }]">
        <label for="message">Sua Mensagem:</label>
        <textarea
          id="message"
          v-model.trim="formData.message"
          rows="5"
          required
          aria-describedby="message-error"
        ></textarea>
         <span v-if="errors.message" id="message-error" class="error-message">{{ errors.message }}</span>
      </div>
  
      <button type="submit" :disabled="isSubmitting" class="submit-button">
        <span v-if="isSubmitting">
            <i class="fas fa-spinner fa-spin"></i> Enviando...
        </span>
        <span v-else>
            <i class="fas fa-paper-plane"></i> Enviar Mensagem
        </span>
      </button>
  
      <!-- Feedback de Status -->
      <p v-if="submitStatus === 'success'" class="feedback-message success">
          <i class="fas fa-check-circle"></i> Mensagem enviada com sucesso! Entraremos em contato em breve (Simulação).
      </p>
      <p v-if="submitStatus === 'validation_error'" class="feedback-message error">
          <i class="fas fa-exclamation-triangle"></i> Por favor, corrija os erros no formulário.
      </p>
      <p v-if="submitStatus === 'error'" class="feedback-message error">
          <i class="fas fa-times-circle"></i> Falha ao enviar a mensagem. Tente novamente mais tarde (Simulação).
      </p>
    </form>
  </template>
  
  <script setup>
  import { ref, reactive } from 'vue';
  
  // Estado reativo para os dados do formulário
  const formData = reactive({
    name: '',
    email: '',
    message: ''
  });
  
  // Estado reativo para armazenar erros de validação
  const errors = reactive({
      name: null,
      email: null,
      message: null
  });
  
  // Estado para controlar o processo de envio
  const isSubmitting = ref(false);
  // Estado para exibir mensagens de feedback pós-envio
  const submitStatus = ref(null); // null | 'success' | 'error' | 'validation_error'
  
  // Função de validação
  function validateForm() {
      // Limpa erros anteriores
      errors.name = null;
      errors.email = null;
      errors.message = null;
      let isValid = true;
  
      // Validação do Nome
      if (!formData.name) {
          errors.name = 'O nome é obrigatório.';
          isValid = false;
      }
  
      // Validação do Email
      if (!formData.email) {
          errors.email = 'O email é obrigatório.';
          isValid = false;
      } else {
          // Regex simples para validação de email
          const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
          if (!emailRegex.test(formData.email)) {
              errors.email = 'Por favor, insira um endereço de email válido.';
              isValid = false;
          }
      }
  
       // Validação da Mensagem
       if (!formData.message) {
          errors.message = 'A mensagem é obrigatória.';
          isValid = false;
      } else if (formData.message.length < 10) {
          errors.message = 'A mensagem deve ter pelo menos 10 caracteres.';
          isValid = false;
      }
  
      return isValid;
  }
  
  // Função chamada ao submeter o formulário
  async function handleSubmit() {
    // Reseta o status de envio anterior
    submitStatus.value = null;
  
    // Executa a validação
    if (!validateForm()) {
        submitStatus.value = 'validation_error';
        console.log('Erros de validação:', errors);
        return; // Interrompe o envio se a validação falhar
    }
  
    // Inicia o estado de envio
    isSubmitting.value = true;
    console.log('Enviando dados (simulação):', { ...formData }); // Log dos dados
  
    // --- SIMULAÇÃO DE ENVIO PARA SERVIDOR ---
    try {
      // Simula um atraso de rede (1.5 segundos)
      await new Promise(resolve => setTimeout(resolve, 1500));
  
      // Simula sucesso ou erro aleatoriamente (80% chance de sucesso)
      const success = Math.random() < 0.8;
  
      if (success) {
        submitStatus.value = 'success';
        console.log('Envio simulado com sucesso!');
        // Limpa o formulário após o sucesso
        formData.name = '';
        formData.email = '';
        formData.message = '';
      } else {
        throw new Error('Simulated server error'); // Simula um erro do servidor
      }
    } catch (error) {
      submitStatus.value = 'error';
      console.error('Erro simulado no envio:', error);
    } finally {
      // Finaliza o estado de envio, independentemente de sucesso ou erro
      isSubmitting.value = false;
    }
    // --- FIM DA SIMULAÇÃO ---
  }
  </script>
  
  <style scoped>
  .contact-form {
    max-width: 600px; /* Limita largura do formulário */
    margin: 0 auto; /* Centraliza */
    padding: 25px;
    background-color: #f9f9f9; /* Fundo levemente diferente */
    border-radius: 8px;
    border: 1px solid var(--border-color);
  }
  
  .form-group {
    margin-bottom: 20px;
  }
  
  /* Adiciona estilo visual quando há erro */
  .form-group.has-error input,
  .form-group.has-error textarea {
      border-color: #e74c3c; /* Vermelho para erro */
  }
  .form-group.has-error label {
      color: #e74c3c;
  }
  
  
  label {
    display: block;
    margin-bottom: 8px;
    font-weight: bold;
    color: var(--primary-dark);
  }
  
  input[type="text"],
  input[type="email"],
  textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid var(--border-color);
    border-radius: 5px;
    box-sizing: border-box;
    font-size: 1em;
    transition: border-color 0.3s ease;
  }
  
  input:focus, textarea:focus {
      border-color: var(--primary-color);
      outline: none;
      box-shadow: 0 0 0 2px rgba(106, 90, 205, 0.2); /* Sombra suave roxa no foco */
  }
  
  
  textarea {
      resize: vertical; /* Permite redimensionar verticalmente */
      min-height: 100px;
  }
  
  .submit-button {
    display: inline-flex; /* Para alinhar ícone e texto */
    align-items: center;
    gap: 8px; /* Espaço entre ícone e texto */
    width: 100%; /* Ocupa largura total */
    padding: 12px 20px;
    font-size: 1.1em;
    /* Usa estilo padrão de botão definido em main.css */
  }
  .submit-button:disabled {
      background-color: #bdc3c7; /* Cor diferente para desabilitado */
  }
  
  /* Ícone de loading */
  .fa-spinner {
      animation: fa-spin 1s infinite linear;
  }
  @keyframes fa-spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }
  
  
  .error-message {
    color: #e74c3c; /* Vermelho */
    font-size: 0.9em;
    margin-top: 5px;
    display: block; /* Garante que ocupe sua linha */
  }
  
  .feedback-message {
      text-align: center;
      margin-top: 20px;
      padding: 12px;
      border-radius: 5px;
      font-weight: bold;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
  }
  .feedback-message.success {
      background-color: #d4edda; /* Verde claro */
      color: #155724; /* Verde escuro */
      border: 1px solid #c3e6cb;
  }
  .feedback-message.error {
      background-color: #f8d7da; /* Vermelho claro */
      color: #721c24; /* Vermelho escuro */
      border: 1px solid #f5c6cb;
  }
  
  /* Responsividade */
  @media (max-width: 480px) {
      .contact-form {
          padding: 20px;
      }
      input[type="text"],
      input[type="email"],
      textarea {
          padding: 10px;
      }
      .submit-button {
          font-size: 1em;
      }
  }
  </style>