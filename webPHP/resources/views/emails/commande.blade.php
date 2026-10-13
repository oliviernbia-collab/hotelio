<h2>Confirmation de commande</h2>

<p>Commande #{{ $commande->id }}</p>

<p>Total : {{ $commande->total }} FCFA</p>

<h4>Détails :</h4>

@foreach($commande->lignes as $ligne)
    <p>
        Produit #{{ $ligne->product_id }} -
        Qté : {{ $ligne->quantite }} -
        Prix : {{ $ligne->prix }}
    </p>
@endforeach

<p>Merci pour votre achat 🙏</p>