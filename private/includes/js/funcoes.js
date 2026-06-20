           /*Lógica para o modal de eliminação de fornecedor*/
           
           const deleteModal = document.getElementById('deleteModal');
            const modalDialog = deleteModal.querySelector('.modal-dialog');
            let fornecedorToDelete = null;
            let isDrawing = false;
            let currentX = 0;
            let currentY = 0;
            let initialX = 0;
            let initialY = 0;

            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const fornecedorId = button.getAttribute('data-fornecedor');
                const fornecedorName = button.getAttribute('data-nome');

                fornecedorToDelete = fornecedorId;
                document.getElementById('fornecedorName').textContent = fornecedorName;

                modalDialog.style.transform = 'translate(-50%, -50%)';
                currentX = 0;
                currentY = 0;
            });

            const modalHeader = deleteModal.querySelector('.modal-header');
            modalHeader.addEventListener('mousedown', function(e) {
                isDrawing = true;
                initialX = e.clientX - currentX;
                initialY = e.clientY - currentY;
                modalDialog.classList.add('dragging');
                modalHeader.style.cursor = 'grabbing';
            });

            document.addEventListener('mousemove', function(e) {
                if (isDrawing && deleteModal.classList.contains('show')) {
                    currentX = e.clientX - initialX;
                    currentY = e.clientY - initialY;

                    currentX = Math.min(Math.max(currentX, -window.innerWidth / 2 + modalDialog.offsetWidth / 4), window.innerWidth / 2 - modalDialog.offsetWidth / 4);
                    currentY = Math.min(Math.max(currentY, -window.innerHeight / 2 + 40), window.innerHeight / 2 - 40);

                    modalDialog.style.transform = `translate(calc(-50% + ${currentX}px), calc(-50% + ${currentY}px))`;
                }
            });

            document.addEventListener('mouseup', function() {
                isDrawing = false;
                modalDialog.classList.remove('dragging');
                modalHeader.style.cursor = 'grab';
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
                if (fornecedorToDelete) {
                    const row = document.querySelector(`button[data-fornecedor="${fornecedorToDelete}"]`).closest('tr');
                    if (row) {
                        row.remove();
                    }
                    bootstrap.Modal.getInstance(deleteModal).hide();
                    alert('Fornecedor eliminado com sucesso!');
                }
            });


/*Lógica para o modal de eliminação de localização*/
              // Controlar modal de eliminação
        const deleteModal = document.getElementById('deleteModal');
        const modalDialog = deleteModal.querySelector('.modal-dialog');
        let localizacaoToDelete = null;
        let isDrawing = false;
        let currentX = 0;
        let currentY = 0;
        let initialX = 0;
        let initialY = 0;

        // Evento para mostrar modal
        deleteModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const localizacaoId = button.getAttribute('data-localizacao');
            const localizacaoName = button.getAttribute('data-nome');
            
            localizacaoToDelete = localizacaoId;
            document.getElementById('localizacaoName').textContent = localizacaoName;
            
            // Resetar posição do modal
            modalDialog.style.transform = 'translate(-50%, -50%)';
            currentX = 0;
            currentY = 0;
        });

        // Função para arrastar o modal
        const modalHeader = deleteModal.querySelector('.modal-header');
        
        modalHeader.addEventListener('mousedown', function(e) {
            isDrawing = true;
            initialX = e.clientX - currentX;
            initialY = e.clientY - currentY;
            modalDialog.classList.add('dragging');
            modalHeader.style.cursor = 'grabbing';
        });

        document.addEventListener('mousemove', function(e) {
            if (isDrawing && deleteModal.classList.contains('show')) {
                currentX = e.clientX - initialX;
                currentY = e.clientY - initialY;
                
                // Limitar movimento dentro da tela
                const maxX = window.innerWidth - modalDialog.offsetWidth / 2;
                const maxY = window.innerHeight - modalDialog.offsetHeight / 2;
                
                currentX = Math.min(Math.max(currentX, -window.innerWidth / 2 + modalDialog.offsetWidth / 4), window.innerWidth / 2 - modalDialog.offsetWidth / 4);
                currentY = Math.min(Math.max(currentY, -window.innerHeight / 2 + 40), window.innerHeight / 2 - 40);
                
                modalDialog.style.transform = `translate(calc(-50% + ${currentX}px), calc(-50% + ${currentY}px))`;
            }
        });

        document.addEventListener('mouseup', function() {
            isDrawing = false;
            modalDialog.classList.remove('dragging');
            modalHeader.style.cursor = 'grab';
        });

        // Confirmar eliminação
        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (localizacaoToDelete) {
                // Aqui você faria uma requisição AJAX/Fetch para deletar no servidor
                console.log('Eliminando localização:', localizacaoToDelete);
                
                // Remover a linha da tabela
                const row = document.querySelector(`a[data-localizacao="${localizacaoToDelete}"]`).closest('tr');
                if (row) {
                    row.remove();
                }
                
                // Fechar o modal
                bootstrap.Modal.getInstance(deleteModal).hide();
                
                // Mensagem de sucesso
                alert('Localização eliminada com sucesso!');
            }
        });      





   /*Lógica para o modal de edição de fornecedor*/
           document.getElementById('formEditarFornecedor').addEventListener('submit', function(e) {
            e.preventDefault();
            const fornecedor = {
                empresa: document.getElementById('empresa').value,
                nif: document.getElementById('nif').value,
                tipo: document.getElementById('tipo').value,
                contacto: document.getElementById('contacto').value,
                email: document.getElementById('email').value,
                endereco: document.getElementById('endereco').value,
                observacoes: document.getElementById('observacoes').value
            };
            console.log('Fornecedor atualizado:', fornecedor);
            alert('Fornecedor atualizado com sucesso!');
            window.location.href = 'fornecedores_lista.html';
        });
        





/*Lógica para o modal de eliminação de equipamento*/
        // Extrair parâmetros da URL
        const urlParams = new URLSearchParams(window.location.search);
        const equipmentId = urlParams.get('id');
        const equipmentName = urlParams.get('nome');

        // Preencher nome do equipamento
        if (equipmentName) {
            document.getElementById('equipmentName').textContent = decodeURIComponent(equipmentName);
        }

        // Controlar clique no botão de eliminação
        document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
            if (equipmentId) {
                // Aqui você faria uma chamada AJAX/Fetch para deletar no servidor
                console.log('Eliminando equipamento:', equipmentId);

                // Simular eliminação
                alert('Equipamento eliminado com sucesso!');

                // Redirecionar para a lista de equipamentos
                window.location.href = 'equipamentos_lista.html';
            }
        });




/*Lógica para o modal de edição de manutenção*/
             document.getElementById('editarManutencaoForm').addEventListener('submit', function (event) {
            event.preventDefault();
            alert('Manutenção atualizada com sucesso!');
            window.location.href = 'manutencao_lista.html';
        });



        /*Lógica para o modal de registo de nova manutenção*/
             document.getElementById('novaManutencaoForm').addEventListener('submit', function (event) {
            event.preventDefault();
            alert('Nova manutenção registada com sucesso!');
            window.location.href = 'manutencao_lista.html';
        });








           // Lógica para o modal de eliminação de garantia
            const deleteModal = document.getElementById('deleteModal');
            let garantiaToDelete = null;

            deleteModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const garantiaId = button.getAttribute('data-garantia');
                const garantiaName = button.getAttribute('data-nome');

                garantiaToDelete = garantiaId;
                document.getElementById('garantiaName').textContent = garantiaName;
            });

            document.getElementById('confirmDeleteBtn').addEventListener('click', function () {
                if (garantiaToDelete) {
                    const row = document.querySelector(`button[data-garantia="${garantiaToDelete}"]`).closest('tr');
                    if (row) {
                        row.remove();
                    }
                    bootstrap.Modal.getInstance(deleteModal).hide();
                    alert('Garantia eliminada com sucesso!');
                }
            });










