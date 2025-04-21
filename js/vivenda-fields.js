document.addEventListener('DOMContentLoaded', function() {
    const typeResiSelect = document.getElementById('typeResi');
    const form = document.getElementById('cad-residencia-form');
    
    // HTML dos campos específicos da Vivenda
    const vivendaFieldsHTML = `
        <div id="vivendaFields">
            <label for="" class="col-form-label"><h3>Infraestrutura da Vivenda</h3></label>
            <div class="mb-3">
                <label for="houseSize" class="col-form-label">Área (m²):</label>
                <input class="form-control" type="number" id="houseSize" name="houseSize" min="10" step="0.1" placeholder="Ex: 120.5" required>
                <div class="invalid-feedback">Área mínima de 10m²</div>
            </div>
            <br>
            <label for="typology" class="col-form-label">Tipologia do Imóvel (Quantidade de Quarto(s))</label><p>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="typology" id="typologyT2" value="T2" required>
                <label class="form-check-label" for="typologyT2">T2</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="typology" id="typologyT3" value="T3">
                <label class="form-check-label" for="typologyT3">T3</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="typology" id="typologyT4" value="T4">
                <label class="form-check-label" for="typologyT4">T4</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="typology" id="typologyT5" value="T5+">
                <label class="form-check-label" for="typologyT5">T5+</label>
            </div>
            <div class="invalid-feedback">Selecione uma tipologia</div></p>

            <label for="livingRoomCount" class="col-form-label">Quantidade de Sala(s)</label>
            <div class="form-check">
                <input class="form-check-input" name="livingRoomCount" type="radio" value="1 - Sala" id="livingRoom1" required>
                <label class="form-check-label" for="livingRoom1">
                    1 - Sala(s)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" name="livingRoomCount" type="radio" value="2 - Sala(s)" id="livingRoom2">
                <label class="form-check-label" for="livingRoom2">
                    2 - Sala(s)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" name="livingRoomCount" type="radio" value="3 - Sala(s)" id="livingRoom3">
                <label class="form-check-label" for="livingRoom3">
                    3 - Sala(s)
                </label>
            </div>
            <div class="invalid-feedback">Selecione a quantidade de salas</div>
            <br>
            <label for="bathroomCount" class="col-form-label">Quantidade de Banheiros(s)</label><p>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="bathroomCount" type="radio" value="1 - Banheiro" id="bathroom1" required>
                <label class="form-check-label" for="bathroom1">
                    1 - Banheiro(s)
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="bathroomCount" type="radio" value="2 - Banheiro(s)" id="bathroom2">
                <label class="form-check-label" for="bathroom2">
                    2 - Banheiro(s)
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="bathroomCount" type="radio" value="3 - Banheiro(s)" id="bathroom3">
                <label class="form-check-label" for="bathroom3">
                    3 - Banheiro(s)
                </label>
            </div>
            <div class="invalid-feedback">Selecione a quantidade de banheiros</div></p>
            <br>
            <label for="kitchenCount" class="col-form-label">Quantidade de Cozinha(s)</label><p>
            <div class="form-check">
                <input class="form-check-input" name="kitchenCount" type="radio" value="1 - Cozinha" id="kitchen1" required>
                <label class="form-check-label" for="kitchen1">
                    1 - Cozinha(s)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" name="kitchenCount" type="radio" value="2 - Cozinha" id="kitchen2">
                <label class="form-check-label" for="kitchen2">
                    2 - Cozinha(s)
                </label>
            </div>
            <div class="invalid-feedback">Selecione a quantidade de cozinhas</div></p>
            <br>
            <label for="quintal" class="clo-form-label">Tem Quintal/Jardim?</label><p>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="quintal" type="radio" value="Sim" id="quintalSim" required>
                <label class="form-check-label" for="quintalSim">
                    Sim
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="quintal" type="radio" value="Não" id="quintalNao">
                <label class="form-check-label" for="quintalNao">
                    Não
                </label>
            </div>
            <div class="invalid-feedback">Selecione uma opção</div></p>
            <br>
            <label for="andares" class="clo-form-label">Números de Andares</label><p>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="andares" type="radio" value="Nenhum" id="andares0" required>
                <label class="form-check-label" for="andares0">
                    Nenhum
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="andares" type="radio" value="1 - Andar" id="andares1">
                <label class="form-check-label" for="andares1">
                    1 Andar
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="andares" type="radio" value="2 - Andar(es)" id="andares2">
                <label class="form-check-label" for="andares2">
                    2 Andares
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="andares" type="radio" value="3 - Andar(es)" id="andares3">
                <label class="form-check-label" for="andares3">
                    3 Andares
                </label>
            </div>
            <div class="invalid-feedback">Selecione o número de andares</div></p>
            <label for="garagem" class="clo-form-label">Tem Garagem?</label><p>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="garagem" type="radio" value="Sim" id="garagemSim" required>
                <label class="form-check-label" for="garagemSim">
                    Sim
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="garagem" type="radio" value="Não" id="garagemNao">
                <label class="form-check-label" for="garagemNao">
                    Não
                </label>
            </div>
            <div class="invalid-feedback">Selecione uma opção</div></p>
            <br>
            <label for="hasWater" class="clo-form-label">Tem Água?</label><p>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="hasWater" type="radio" value="Sim" id="hasWaterSim" required>
                <label class="form-check-label" for="hasWaterSim">
                    Sim
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="hasWater" type="radio" value="Não" id="hasWaterNao">
                <label class="form-check-label" for="hasWaterNao">
                    Não
                </label>
            </div>
            <div class="invalid-feedback">Selecione uma opção</div></p>
            <br>
            <label for="hasElectricity" class="clo-form-label">Tem Energia Elétrica?</label><p>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="hasElectricity" type="radio" value="Sim" id="hasElectricitySim" required>
                <label class="form-check-label" for="hasElectricitySim">
                    Sim
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="hasElectricity" type="radio" value="Não" id="hasElectricityNao">
                <label class="form-check-label" for="hasElectricityNao">
                    Não
                </label>
            </div>
            <div class="invalid-feedback">Selecione uma opção</div></p>
        </div>
    `;

    // Encontre o elemento após o qual inseriremos os campos
    const referenceElement = typeResiSelect;

    // Função para mostrar/ocultar campos
    function toggleVivendaFields() {
        const vivendaFieldsDiv = document.getElementById('vivendaFields');
        
        if (typeResiSelect.value === 'Vivenda') {
            if (!vivendaFieldsDiv) {
                referenceElement.insertAdjacentHTML('afterend', vivendaFieldsHTML);
                setupFieldValidations();
            }
        } else {
            if (vivendaFieldsDiv) {
                vivendaFieldsDiv.remove();
            }
        }
    }

    // Configurar validações dos campos
    function setupFieldValidations() {
        // Validação da Área (m²)
        const houseSizeInput = document.getElementById('houseSize');
        houseSizeInput.addEventListener('input', () => {
            if (parseFloat(houseSizeInput.value) < 10) {
                houseSizeInput.classList.add('is-invalid');
            } else {
                houseSizeInput.classList.remove('is-invalid');
            }
        });

        // Validação de formulário antes do submit
        form.addEventListener('submit', (e) => {
            let isValid = true;
            
            // Verificar todos os campos required
            const requiredFields = form.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value) {
                    field.classList.add('is-invalid');
                    isValid = false;
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            // Verificação específica para área
            if (houseSizeInput && parseFloat(houseSizeInput.value) < 10) {
                houseSizeInput.classList.add('is-invalid');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
                form.classList.add('was-validated');
            }
        }, false);
    }

    // Ouvinte de evento para mudanças no select
    typeResiSelect.addEventListener('change', toggleVivendaFields);
    
    // Verificar o valor inicial ao carregar a página
    toggleVivendaFields();
});