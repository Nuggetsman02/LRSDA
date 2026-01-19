<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


require(__DIR__ . '/../_config/config.php');
require(__DIR__ . '/../vendor/autoload.php');

use LRSDA\Server\LRSConnector\LRSConnectionCheck;

$lrsChecker = new LRSConnectionCheck();


echo "Pinging LRS... ";
echo $lrsChecker->pingLRS() ? 'LRS is reachable.' : 'Failed to reach LRS.';
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LRS DataAnalyst</title>
    <link rel="stylesheet" href="./style.css">
</head>

<body>
    <header class="navbar">
        <div class="navbar-left">
            <h1>LRS DataAnalyst</h1>
        </div>
        <div class="navbar-right">
            <button class="login-btn">Se connecter</button>
        </div>
    </header>

    <main class="container">
        <aside class="sidebar">
            <div class="date-filter">
                <h3>Filtre par date</h3>
                <label for="start-date">Du :</label>
                <input type="date" id="start-date" value="2000-01-01">
                <label for="end-date">Au :</label>
                <input type="date" id="end-date">
            </div>

            <div class="generic-filters">
                <h3>Filtres</h3>
                <div class="filter-actions">
                    <button id="select-all">Tout sélectionner</button>
                    <button id="deselect-all">Tout désélectionner</button>
                </div>
                <ul id="filter-list">
                    <li><label><input type="checkbox" class="filter-checkbox" data-column="Utilisateur"> Utilisateur</label></li>
                    <li><label><input type="checkbox" class="filter-checkbox" data-column="Action"> Action</label></li>
                    <li><label><input type="checkbox" class="filter-checkbox" data-column="Objet"> Objet</label></li>
                    <li><label><input type="checkbox" class="filter-checkbox" data-column="Score"> Score</label></li>
                    <li><label><input type="checkbox" class="filter-checkbox" data-column="Durée"> Durée</label></li>
                </ul>
            </div>
        </aside>

        <section class="content">
            <div class="table-container">
                <table id="data-table">
                    <thead>
                        <tr id="table-header">
                            <!-- Columns will be added here -->
                        </tr>
                    </thead>
                    <tbody id="table-body">
                        <!-- Rows will be added here -->
                    </tbody>
                </table>
            </div>
            <div class="actions-bottom">
                <button id="download-csv" disabled>Télécharger .csv</button>
            </div>
        </section>
    </main>

    <script src="./script.js"></script>
</body>

</html>