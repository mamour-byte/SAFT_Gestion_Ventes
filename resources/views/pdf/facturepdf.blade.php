{{-- resources/views/orchid/preview-pdf.blade.php --}}
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Facture #{{ $numero_facture }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        h1, h2, h3 { margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .text-right { text-align: right; }
        .company-info, .client-info { margin-bottom: 15px; }
        .totals td { border: none; }
        .totals { margin-top: 20px; width: auto; float: right; }
        .footer { margin-top: 50px; font-size: 11px; text-align: center; color: #777; }
    </style>
</head>
<body>

    <h1>Facture #{{ $numero_facture }}</h1>

    <div class="company-info">
        <strong>{{ config('app.name') }}</strong><br>
        123 Rue de la Société<br>
        Tél : 01 23 45 67 89<br>
        Email : contact@entreprise.com<br>
        SIRET : 123 456 789 00010 | TVA : FR123456789
    </div>

    <div class="client-info">
        <h3>Client</h3>
        {{ $client_nom }} {{ $client_prenom }}<br>
        {{ $client_adresse }}<br>
        Téléphone : {{ $client_telephone }}<br>
        Email : {{ $client_email }}<br>
        SIRET : {{ $client_siret }}
    </div>

    <p><strong>Date facture :</strong> {{ $date_facture }}</p>
    <p><strong>Date échéance :</strong> {{ $date_echeance }}</p>

    <h3>Détails de la facture</h3>
    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire (€)</th>
                <th>Total (€)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produits as $produit)
                <tr>
                    <td>{{ $produit['nom'] }}</td>
                    <td>{{ $produit['quantity'] }}</td>
                    <td>{{ number_format($produit['prix_unitaire'], 2, ',', ' ') }}</td>
                    <td>{{ number_format($produit['total_ligne'], 2, ',', ' ') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="totals">
        <tr>
            <td><strong>Sous-total :</strong></td>
            <td class="text-right">{{ number_format($subtotal, 2, ',', ' ') }} €</td>
        </tr>
        <tr>
            <td><strong>TVA ({{ $taxRate }}%) :</strong></td>
            <td class="text-right">{{ number_format($taxAmount, 2, ',', ' ') }} €</td>
        </tr>
        <tr>
            <td><strong>Total TTC :</strong></td>
            <td class="text-right"><strong>{{ number_format($totalAmount, 2, ',', ' ') }} €</strong></td>
        </tr>
    </table>

    <div class="footer">
        IBAN : FR76 XXXX XXXX XXXX XXXX XXXX XXX | BIC : XXXXXXXXXXX<br>
        Merci pour votre confiance !
    </div>

</body>
</html>
