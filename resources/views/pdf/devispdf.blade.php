<!DOCTYPE html>
<html>
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8">
    <title>Devis</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 6px; text-align: left; }
    </style>
</head>
<body>
    <h1>Devis {{ $facture->numero_facture }}</h1>
    <p><strong>Client :</strong> {{ $client->nom }}</p>
    <p><strong>Date de livraison :</strong> {{ $vente->date_livraison }}</p>

    <table>
        <thead>
            <tr>
                <th>Produit</th>
                <th>Quantité</th>
                <th>Prix unitaire</th>
                <th>Sous-total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($produits as $detail)
                <tr>
                    <td>{{ $detail->product->nom }}</td>
                    <td>{{ $detail->quantite_vendue }}</td>
                    <td>{{ number_format($detail->product->prix_unitaire) }} F CFA</td>
                    <td>{{ number_format($detail->quantite_vendue * $detail->product->prix_unitaire) }} F CFA</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <p><strong>Total TTC :</strong>
        {{ number_format($produits->sum(fn($d) => $d->quantite_vendue * $d->product->prix_unitaire * 1.18)) }} F CFA
    </p>
</body>
</html>
