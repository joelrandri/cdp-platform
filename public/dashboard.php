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
        
        <section class="segmentation-section">
            <h2>Customer Segmentation</h2>

            <div class="segmentation-form">

                <div class="form-group">
                    <label for="segment-logic">Condition logic</label>

                    <select id="segment-logic">
                        <option value="AND">AND - Toutes les conditions</option>
                        <option value="OR">OR - Au moins une condition</option>
                    </select>
                </div>

                <div id="segment-conditions">

                    <div class="condition-row">

                        <input
                            type="text"
                            class="condition-event"
                            value="purchase"
                            placeholder="Event"
                        >

                        <input
                            type="text"
                            class="condition-property"
                            value="amount"
                            placeholder="Property"
                        >

                        <select class="condition-type">
                            <option value="number">Number</option>
                            <option value="string">String</option>
                        </select>

                        <select class="condition-operator">
                            <option value=">">Greater than</option>
                            <option value="=">Equal</option>
                            <option value="!=">Not equal</option>
                            <option value="<">Less than</option>
                            <option value=">=">Greater or equal</option>
                            <option value="<=">Less or equal</option>
                        </select>

                        <input
                            type="text"
                            class="condition-value"
                            value="100"
                            placeholder="Value"
                        >

                        <button type="button" class="remove-condition">
                            Remove
                        </button>

                    </div>

                </div>

                <div class="segmentation-actions">
                    <button type="button" id="add-condition">
                        + Add condition
                    </button>

                    <button type="button" id="run-segmentation">
                        Run segmentation
                    </button>
                </div>

                <p id="segment-message"></p>

            </div>

            <div class="segmentation-results">
                <h3>Matching customers</h3>

                <p id="segment-count">No search performed</p>

                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                    </thead>

                    <tbody id="segment-results">
                        <tr>
                            <td colspan="3">No results yet</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

    </main>

    <script src="assets/js/app.js"></script>
    <script src="assets/js/segmentation.js" defer></script>
</body>
</html>