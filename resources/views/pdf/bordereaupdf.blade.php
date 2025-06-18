<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Bon de Livraison n° {{ $numero_bon_livraison }} </title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #333;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 10px;
        }
        .company, .client {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .company {
            float: left;
        }
        .client {
            float: right;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        table th, table td {
            border: 1px solid #999;
            padding: 8px;
            text-align: center;
        }
        table th {
            background-color: #f0f0f0;
        }
        .totals {
            margin-top: 20px;
            width: 100%;
            border: 1px solid #999;
            border-collapse: collapse;
        }
        .totals td {
            padding: 8px;
            border: 1px solid #999;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            font-size: 11px;
            color: #7f8c8d;
            text-align: center;
        }
        .clearfix {
            clear: both;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <h1>Bordereau de Livraison</h1>
    <h2>N° {{ $numero_bon_livraison }}</h2>

    <div class="company">
        <strong>SAFT</strong><br>
        123 Rue Du savoir<br>
        75000 Paris<br>
        Tél: 01 23 45 67 89<br>
        Email: contact@entreprise.com
    </div>

    <div class="client">
        {{ $client_nom }} {{ $client_prenom }}<br>
        {{ $client_adresse }}<br>
        Tél: {{ $client_telephone }}<br>
        Email: {{ $client_email }}<br>
    </div>

    <div class="clearfix"></div>

    <div class="Infos">
        <strong>Bon de Livraison N° :</strong> {{ $numero_bon_livraison }}<br>
        <strong>Date de Livraison :</strong> {{ $date_livraison }}<br>
        <strong>Numero de Bon Commande :</strong> {{ $numero_commande }} <br>
    </div>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire (F CFA)</th>
                <th>Total ligne (F CFA)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($produits as $produit)
                <tr>
                    <td>{{ $produit['nom'] }}</td>
                    <td>{{ $produit['quantity'] }}</td>
                    <td>{{ number_format($produit['prix_unitaire'], 2, ',', ' ') }}</td>
                    <td>{{ number_format($produit['total_ligne'], 2, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <footer>
        <div class="footer">
            SAFT - SN Dakar - 12500 Yoff<br>
            Tél: 33 XXX XX XX - Email: services@saft.com - SIRET: XXX XXX XXX XX<br>
            Merci pour votre confiance.
        </div>
    </footer>

</body>
</html>