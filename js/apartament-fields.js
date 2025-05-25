document.getElementById('typeResi')?.addEventListener('change', function() {
    const type = this.value;
    const dynamicFields = document.getElementById('dynamic-fields');
    const featuresContainer = document.getElementById('features-container');
    
    // Limpar campos anteriores
    dynamicFields.innerHTML = '';
    featuresContainer.innerHTML = '';
    
    if (type === 'Apartamento') {
        dynamicFields.innerHTML = `
            <div class="mb-3">
                <label class="form-label">Varanda</label>
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="varanda" id="varanda" value="1">
                    <label class="form-check-label" for="varanda">Possui varanda</label>
                </div>
            </div>
        `;
        
        featuresContainer.innerHTML = `
            <div class="col-md-4">
                <label class="form-label required">Salas de Estar</label>
                <select name="livingRoomCount" id="livingRoomCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label required">Banheiros</label>
                <select name="bathroomCount" id="bathroomCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label required">Cozinhas</label>
                <select name="kitchenCount" id="kitchenCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                </select>
            </div>
        `;
    } 
    else if (type === 'Vivenda' || type === 'Moradia') {
        featuresContainer.innerHTML = `
            <div class="col-md-3">
                <label class="form-label required">Salas de Estar</label>
                <select name="livingRoomCount" id="livingRoomCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label required">Banheiros</label>
                <select name="bathroomCount" id="bathroomCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label required">Cozinhas</label>
                <select name="kitchenCount" id="kitchenCount" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label required">Andares</label>
                <select name="andares" id="andares" class="form-select" required>
                    <option value="">Selecione</option>
                    <option value="0">Nenhum</option>
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3+</option>
                </select>
            </div>
        `;
    }
});