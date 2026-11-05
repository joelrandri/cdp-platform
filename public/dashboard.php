<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CDP Platform - Dashboard</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <header class="header">
        <h1>CDP Platform</h1>
        <p>Customer Data Platform</p>
    </header>

    <main class="container">

        <!-- Liste des clients -->
        <section class="card">
            <h2>Liste des clients</h2>

            <div id="customers-list">
                Chargement des clients...
            </div>

            <div id="pagination" class="pagination"></div>
        </section>

        <!-- Profil client -->
        <section class="card">
            <h2>Profil du client</h2>

            <div id="customer-profile">
                Sélectionne un client pour consulter son profil.
            </div>
        </section>

        <!-- Segmentation -->
        
        <section class="card">
            <h2>Segmentation des clients</h2>

            <form id="segment-form">
                <div class="form-group">
                    <label for="event">Événement</label>
                    <select id="event" required>
                        <option value="purchase">Purchase</option>
                        <option value="page_view">Page View</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="property">Propriété</label>
                    <select id="property" required>
                        <option value="price">Price</option>
                        <option value="quantity">Quantity</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="operator">Opérateur</label>
                    <select id="operator" required>
                        <option value=">">Supérieur à</option>
                        <option value="<">Inférieur à</option>
                        <option value="=">Égal à</option>
                        <option value=">=">Supérieur ou égal à</option>
                        <option value="<=">Inférieur ou égal à</option>
                        <option value="!=">Différent de</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="value">Valeur</label>
                    <input type="number" id="value" step="0.01" required>
                </div>

                <button type="submit">Rechercher les clients</button>
            </form>

            <div id="segment-results"></div>
        </section>

    </main>

    <script src="assets/js/app.js"></script>
</body>
</html>