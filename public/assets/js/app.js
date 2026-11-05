const API_URL = '/api';

let currentPage = 1;
const perPage = 10;

// load the customers
async function loadCustomers(page = 1) {
    const container = document.getElementById('customers-list');
    const pagination = document.getElementById('pagination');

    container.textContent = 'Chargement...';
    pagination.replaceChildren();

    try {
        const response = await fetch(
            `${API_URL}/customers?page=${page}&limit=${perPage}`
        );

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Erreur lors du chargement');
        }

        currentPage = result.pagination.current_page;

        displayCustomers(result.data);
        displayPagination(result.pagination);

    } catch (error) {
        container.textContent = error.message;
    }
}

// Display customers within table
function displayCustomers(customers) {
    const container = document.getElementById('customers-list');

    container.replaceChildren();

    if (customers.length === 0) {
        container.textContent = 'Aucun client trouvé.';
        return;
    }

    const table = document.createElement('table');
    const thead = document.createElement('thead');
    const tbody = document.createElement('tbody');

    thead.innerHTML = `
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Email</th>
            <th>Action</th>
        </tr>
    `;

    customers.forEach(customer => {
        const tr = document.createElement('tr');

        const id = document.createElement('td');
        id.textContent = customer.id;

        const name = document.createElement('td');
        name.textContent = customer.name;

        const email = document.createElement('td');
        email.textContent = customer.email;

        const action = document.createElement('td');
        const button = document.createElement('button');

        button.textContent = 'Voir le profil';
        button.addEventListener('click', () => {
            loadCustomerProfile(customer.id);
        });

        action.appendChild(button);

        tr.append(id, name, email, action);
        tbody.appendChild(tr);
    });

    table.append(thead, tbody);
    container.appendChild(table);
}

// Display pagination buton
function displayPagination(pagination) {
    const container = document.getElementById('pagination');

    const previous = document.createElement('button');
    previous.textContent = 'Précédent';
    previous.disabled = pagination.current_page <= 1;

    previous.addEventListener('click', () => {
        loadCustomers(currentPage - 1);
    });

    const information = document.createElement('span');
    information.textContent =
        `Page ${pagination.current_page} / ${pagination.total_pages}`;

    const next = document.createElement('button');
    next.textContent = 'Suivant';
    next.disabled = pagination.current_page >= pagination.total_pages;

    next.addEventListener('click', () => {
        loadCustomers(currentPage + 1);
    });

    container.append(previous, information, next);
}

// load customer's profile
async function loadCustomerProfile(customerId) {
    const container = document.getElementById('customer-profile');

    container.textContent = 'Chargement du profil...';

    try {
        const response = await fetch(
            `${API_URL}/customers/${customerId}`
        );

        const result = await response.json();

        if (!response.ok || !result.success) {
            throw new Error(result.message || 'Client introuvable');
        }

        displayCustomerProfile(result.data);

    } catch (error) {
        container.textContent = error.message;
    }
}

// Display Customer's data
function displayCustomerProfile(data) {
    const container = document.getElementById('customer-profile');
    container.replaceChildren();

    const customer = data.customer;
    const statistics = data.statistics;

    const info = document.createElement('div');
    info.className = 'profile-info';

    const title = document.createElement('h3');
    title.textContent = customer.name;

    const email = document.createElement('p');
    email.textContent = `Email : ${customer.email}`;

    const purchases = document.createElement('p');
    purchases.textContent =
        `Nombre d'achats : ${statistics.total_purchases}`;

    const spend = document.createElement('p');
    spend.textContent =
        `Montant total : ${statistics.total_spend}`;

    info.append(title, email, purchases, spend);

    const eventsTitle = document.createElement('h3');
    eventsTitle.textContent = 'Derniers événements';

    const events = document.createElement('div');

    data.recent_events.forEach(event => {
        const item = document.createElement('div');
        item.className = 'event-item';
        item.textContent =
            `${event.event_type} - ${event.created_at}`;

        events.appendChild(item);
    });

    container.append(info, eventsTitle, events);
}

// Exécuter une segmentation
document.getElementById('segment-form').addEventListener(
    'submit',
    async function (event) {
        event.preventDefault();

        const resultsContainer =
            document.getElementById('segment-results');

        resultsContainer.textContent = 'Recherche en cours...';

        const conditions = [
            {
                event: document.getElementById('event').value,
                property: document.getElementById('property').value,
                operator: document.getElementById('operator').value,
                value: Number(document.getElementById('value').value)
            }
        ];

        try {
            const response = await fetch(`${API_URL}/segments/query`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ conditions })
            });

            const result = await response.json();

            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Erreur de segmentation');
            }

            displaySegmentResults(result.data);

        } catch (error) {
            resultsContainer.textContent = error.message;
        }
    }
);

// Afficher les résultats de segmentation
function displaySegmentResults(customers) {
    const container = document.getElementById('segment-results');

    container.replaceChildren();

    const title = document.createElement('h3');
    title.textContent = `Résultats : ${customers.length} client(s) trouvé(s)`;

    container.appendChild(title);

    customers.forEach(customer => {
        const item = document.createElement('p');
        item.textContent =
            `${customer.name} - ${customer.email}`;

        container.appendChild(item);
    });
}

// initial loading
loadCustomers();