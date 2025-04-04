// Espera o documento HTML estar completamente carregado e pronto
$(document).ready(function() {

    // --- 1. Adicionar Tarefa ---
    $('#adicionar-tarefa').on('click', function() {
        // Pega o valor digitado no input
        let textoTarefa = $('#nova-tarefa').val(); 

        // Verifica se o campo não está vazio (trim remove espaços em branco)
        if (textoTarefa.trim() !== '') {
            
            // Cria o HTML para o novo item da lista (<li>)
            // Usando template literals (crases ``) para facilitar a concatenação
            let novoItem = `
                <li>
                    <span>${textoTarefa}</span> 
                    <button class="remover-tarefa">Remover</button>
                </li>
            `;

            // Adiciona o novo item (<li>) ao final da lista (<ul>)
            $('#lista-tarefas').append(novoItem);

            // Limpa o campo de input após adicionar
            $('#nova-tarefa').val('');

            // Opcional: Coloca o foco de volta no input para facilitar adicionar mais tarefas
            $('#nova-tarefa').focus(); 
        } else {
            alert("Por favor, digite uma tarefa!"); // Feedback se o campo estiver vazio
        }
    });

    // Permite adicionar tarefa pressionando Enter no input
    $('#nova-tarefa').on('keypress', function(event) {
        // Verifica se a tecla pressionada foi Enter (código 13)
        if (event.which === 13) { 
            // Simula um clique no botão Adicionar
            $('#adicionar-tarefa').click(); 
        }
    });

    // --- 2. Marcar Tarefa como Concluída (ou desmarcar) ---
    // Usa 'on' com delegação de eventos, pois os 'li' são adicionados dinamicamente.
    // O evento é ligado na 'ul' (#lista-tarefas), mas só dispara quando o clique
    // acontece em um 'span' DENTRO de um 'li' que está DENTRO da 'ul'.
    $('#lista-tarefas').on('click', 'li span', function() {
        // $(this) aqui se refere ao <span> que foi clicado.
        // .parent('li') pega o elemento <li> pai do <span>.
        // .toggleClass('concluida') adiciona a classe 'concluida' se ela não existir,
        // ou remove se ela já existir. É perfeito para marcar/desmarcar.
        $(this).parent('li').toggleClass('concluida'); 
    });

    // --- 3. Remover Tarefa ---
    // Também usa delegação de eventos, pois os botões '.remover-tarefa' 
    // são adicionados dinamicamente junto com os 'li'.
    $('#lista-tarefas').on('click', '.remover-tarefa', function() {
        // $(this) aqui se refere ao botão '.remover-tarefa' que foi clicado.
        // .parent('li') pega o elemento <li> pai do botão.
        // .fadeOut() faz o item desaparecer suavemente antes de ser removido.
        // O segundo argumento do fadeOut é uma função callback que executa
        // APÓS a animação terminar.
        $(this).parent('li').fadeOut(300, function() {
            // $(this) DENTRO do callback do fadeOut se refere ao <li> que desapareceu.
            $(this).remove(); // Remove o elemento <li> do HTML (DOM).
        });
    });

}); // Fim do $(document).ready()