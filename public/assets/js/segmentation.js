
document.addEventListener('DOMContentLoaded', () => {

    const conditionsContainer = document.getElementById(
        'segment-conditions'
    );

    const addButton = document.getElementById('add-condition');
    const runButton = document.getElementById('run-segmentation');

    const logicSelect = document.getElementById('segment-logic');

    const resultsContainer = document.getElementById('segment-results');
    const countElement = document.getElementById('segment-count');
    const messageElement = document.getElementById('segment-message');

    const numericOperators = [
        ['>', 'Greater than'],
        ['=', 'Equal'],
        ['!=', 'Not equal'],
        ['<', 'Less than'],
        ['>=', 'Greater or equal'],
        ['<=', 'Less or equal']
    ];

    const stringOperators = [
        ['=', 'Equal'],
        ['!=', 'Not equal'],
        ['contains', 'Contains'],
        ['starts_with', 'Starts with'],
        ['ends_with', 'Ends with']
    ];

    function createConditionRow() {

        const row = document.createElement('div');
        row.className = 'condition-row';

        row.innerHTML = `
            <input
                type="text"
                class="condition-event"
                placeholder="Event"
                value="purchase"
            >

            <input
                type="text"
                class="condition-property"
                placeholder="Property"
            >

            <select class="condition-type">
                <option value="number">Number</option>
                <option value="string">String</option>
            </select>

            <select class="condition-operator"></select>

            <input
                type="text"
                class="condition-value"
                placeholder="Value"
            >

            <button type="button" class="remove-condition">
                Remove
            </button>
        `;

        const typeSelect = row.querySelector('.condition-type');
        const operatorSelect = row.querySelector('.condition-operator');

        updateOperators(typeSelect, operatorSelect);

        typeSelect.addEventListener('change', () => {
            updateOperators(typeSelect, operatorSelect);
        });

        return row;
    }

    function updateOperators(typeSelect, operatorSelect) {

        const operators = typeSelect.value === 'number'
            ? numericOperators
            : stringOperators;

        operatorSelect.innerHTML = '';

        operators.forEach(([value, label]) => {

            const option = document.createElement('option');

            option.value = value;
            option.textContent = label;

            operatorSelect.appendChild(option);
        });
    }

    // Adapter les opérateurs de la première condition
    const firstRow = conditionsContainer.querySelector('.condition-row');

    updateOperators(
        firstRow.querySelector('.condition-type'),
        firstRow.querySelector('.condition-operator')
    );

    firstRow.querySelector('.condition-type').addEventListener(
        'change',
        () => {
            updateOperators(
                firstRow.querySelector('.condition-type'),
                firstRow.querySelector('.condition-operator')
            );
        }
    );

    // Ajouter une condition
    addButton.addEventListener('click', () => {

        if (conditionsContainer.children.length >= 10) {
            messageElement.textContent = 'Maximum 10 conditions allowed.';
            return;
        }

        conditionsContainer.appendChild(createConditionRow());

        messageElement.textContent = '';
    });

    // Supprimer une condition
    conditionsContainer.addEventListener('click', (event) => {

        if (!event.target.classList.contains('remove-condition')) {
            return;
        }

        if (conditionsContainer.children.length <= 1) {
            messageElement.textContent =
                'At least one condition is required.';
            return;
        }

        event.target.closest('.condition-row').remove();

        messageElement.textContent = '';
    });

    // Exécuter la segmentation
    runButton.addEventListener('click', async () => {

        messageElement.textContent = 'Searching...';

        resultsContainer.innerHTML = '';
        countElement.textContent = '';

        try {

            const conditions = [];

            const rows = conditionsContainer.querySelectorAll(
                '.condition-row'
            );

            rows.forEach((row) => {

                const type = row.querySelector(
                    '.condition-type'
                ).value;

                const rawValue = row.querySelector(
                    '.condition-value'
                ).value.trim();

                let value = rawValue;

                if (type === 'number') {

                    if (rawValue === '' || !Number.isFinite(Number(rawValue))) {
                        throw new Error('Please enter a valid number.');
                    }

                    value = Number(rawValue);
                }

                conditions.push({
                    event_type: row.querySelector(
                        '.condition-event'
                    ).value.trim(),

                    property: row.querySelector(
                        '.condition-property'
                    ).value.trim(),

                    operator: row.querySelector(
                        '.condition-operator'
                    ).value,

                    value: value
                });
            });

            const response = await fetch('/api/segments/query', {
                method: 'POST',

                headers: {
                    'Content-Type': 'application/json'
                },

                body: JSON.stringify({
                    logic: logicSelect.value,
                    conditions: conditions
                })
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(
                    result.message || 'Segmentation failed.'
                );
            }

            countElement.textContent =
                `${result.count} customer(s) found`;

            if (result.data.length === 0) {

                resultsContainer.innerHTML = `
                    <tr>
                        <td colspan="3">No matching customers found</td>
                    </tr>
                `;

            } else {

                result.data.forEach((customer) => {

                    const row = document.createElement('tr');

                    const idCell = document.createElement('td');
                    const nameCell = document.createElement('td');
                    const emailCell = document.createElement('td');

                    idCell.textContent = customer.id;
                    nameCell.textContent = customer.name;
                    emailCell.textContent = customer.email;

                    row.append(idCell, nameCell, emailCell);

                    resultsContainer.appendChild(row);
                });
            }

            messageElement.textContent = 'Segmentation completed.';

        } catch (error) {

            messageElement.textContent = error.message;
        }
    });
});
