document.addEventListener('DOMContentLoaded', function() {
    const typeResiSelect = document.getElementById('typeResi');
    const form = document.getElementById('cad-residencia-form');
    
    // HTML dos campos específicos do apartamento
    const apartmentFieldsHTML = `
        <div id="apartmentFields">
            <label for="typology" class="col-form-label"><h3>Infraestrutura Básica</h3></label>
            <div class="mb-3">
                <label for="houseSize" class="col-form-label">Área (m²):</label>
                <input class="form-control" type="number" id="houseSize" name="houseSize" min="10" step="0.1" placeholder="Ex: 65.5">
            </div>
            <br>
            <label for="typology" class="col-form-label">Tipologia do Imóvel (Quantidade de Quarto(s))</label><p>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="typology" name="typology" value="T1">
                <label class="form-check-label" for="inlineCheckbox1">T1</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="typology" name="typology" value="T2">
                <label class="form-check-label" for="inlineCheckbox2">T2</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="typology" name="typology" value="T3">
                <label class="form-check-label" for="inlineCheckbox3">T3</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="typology" name="typology" value="T4">
                <label class="form-check-label" for="inlineCheckbox3">T4</label>
            </div></p>

            <label for="livingRoomCount" class="col-form-label">Quantidade de Sala(s)</label>
            <div class="form-check">
                <input class="form-check-input" name="livingRoomCount" type="checkbox" value="1" id="livingRoomCount">
                <label class="form-check-label" for="checkDefault">
                    1 - Sala(s)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" name="livingRoomCount" type="checkbox" value="2" id="livingRoomCount">
                <label class="form-check-label" for="checkChecked">
                    2 - Sala(s)
                </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" name="livingRoomCount" type="checkbox" value="3" id="livingRoomCount">
                <label class="form-check-label" for="checkChecked">
                    3 - Sala(s)
                </label>
            </div>
            <br>
            <label for="bathroomCount" class="col-form-label">Quantidade de Banheiros(s)</label><p>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="bathroomCount" type="checkbox" value="1" id="bathroomCount">
                <label class="form-check-label" for="checkDefault">
                    1 - Banheiro(s)
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="bathroomCount" type="checkbox" value="2" id="bathroomCount">
                <label class="form-check-label" for="checkChecked">
                    2 - Banheiro(s)
                </label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" name="bathroomCount" type="checkbox" value="3" id="bathroomCount">
                <label class="form-check-label" for="checkChecked">
                    3 - Banheiro(s)
                </label>
            </div></p>
            <br>
            <label for="kitchenCount" class="col-form-label">Quantidade de Cozinha(s)</label><p>
            <div class="form-check">
                <input class="form-check-input" name="kitchenCount" type="checkbox" value="1" id="kitchenCount">
                <label class="form-check-label" for="checkDefault">
                    1 - Cozinha(s)
                </label>
            </div></p>
            <br>
            <label for="varanda" class="clo-form-label">Quantidade de Varanda/Sacada</label><p>
            <div class="form-check">
                <input class="form-check-input" name="varanda" type="checkbox" value="1" id="varanda">
                <label class="form-check-label" for="checkDefault">
                    1 - Varanda/Sacada
                </label>
            </div></p>
        </div>
    `;

    // Encontre o elemento após o qual inseriremos os campos
    const referenceElement = typeResiSelect;

    // Função para mostrar/ocultar campos
    function toggleApartmentFields() {
        const apartmentFieldsDiv = document.getElementById('apartmentFields');
        
        if (typeResiSelect.value === 'Apartamento') {
            if (!apartmentFieldsDiv) {
                referenceElement.insertAdjacentHTML('afterend', apartmentFieldsHTML);
            }
        } else {
            if (apartmentFieldsDiv) {
                apartmentFieldsDiv.remove();
            }
        }
    }

    // Ouvinte de evento para mudanças no select
    typeResiSelect.addEventListener('change', toggleApartmentFields);
    
    // Verificar o valor inicial ao carregar a página
    toggleApartmentFields();
});