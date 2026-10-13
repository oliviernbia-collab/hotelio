<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Facture') }} {{ __('commande') }} #{{ $commande->id }} — Hôtelio</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1a1a2e;
            background: #fff;
            font-size: 13px;
            line-height: 1.5;
        }

        /* Bande drapeau CI */
        .ci-flag {
            height: 7px;
            background: linear-gradient(to right, #F77F00 33.33%, #ffffff 33.33% 66.66%, #009A44 66.66%);
        }

        .container { padding: 36px 48px; max-width: 820px; margin: 0 auto; }

        /* EN-TÊTE */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            padding-bottom: 24px;
            border-bottom: 2px solid #F77F00;
            margin-bottom: 28px;
        }

        .brand { display: flex; align-items: center; gap: 14px; }

        .brand-logo {
            width: 52px; height: 52px;
            background: #F77F00;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; font-weight: 900; color: #fff;
            letter-spacing: -1px;
        }

        .brand-img {
            width: 52px; height: 52px;
            border-radius: 14px;
            object-fit: contain;
        }

        .brand-info .name  { font-size: 22px; font-weight: 800; color: #0C1F3D; }
        .brand-info .sub   { font-size: 10px; color: #009A44; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; margin-top: 2px; }

        .invoice-meta { text-align: right; }
        .invoice-title { font-size: 26px; font-weight: 800; color: #F77F00; text-transform: uppercase; letter-spacing: .06em; }
        .invoice-num   { font-size: 14px; color: #555; margin-top: 4px; }
        .invoice-num strong { color: #0C1F3D; font-size: 16px; }

        .status-badge {
            display: inline-block;
            margin-top: 8px;
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 10px; font-weight: 700;
            text-transform: uppercase; letter-spacing: .08em;
        }
        .badge-paid    { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .badge-pending { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }
        .badge-cancel  { background: #fee2e2; color: #991b1b; border: 1px solid #fca5a5; }

        /* PARTIES */
        .parties { display: flex; gap: 16px; margin-bottom: 24px; }
        .party {
            flex: 1; padding: 14px 18px; border-radius: 10px;
        }
        .party.emitter { background: #0C1F3D; }
        .party.receiver { background: #f8fafc; border: 1px solid #e2e8f0; }
        .party-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; margin-bottom: 8px; opacity: .6; }
        .party.emitter .party-label { color: #94a3b8; }
        .party.receiver .party-label { color: #64748b; }
        .party-name { font-size: 14px; font-weight: 700; margin-bottom: 4px; }
        .party.emitter .party-name, .party.emitter p { color: #e2e8f0; }
        .party.receiver .party-name, .party.receiver p { color: #1e293b; }
        .party p { font-size: 11px; opacity: .8; line-height: 1.7; }

        /* DATES */
        .dates { display: flex; gap: 12px; margin-bottom: 24px; }
        .date-box {
            flex: 1; padding: 10px 14px;
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
        }
        .date-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; margin-bottom: 3px; }
        .date-value { font-size: 13px; font-weight: 700; color: #0C1F3D; }

        /* TABLE */
        .table-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #64748b; margin-bottom: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        thead tr { background: #0C1F3D; }
        thead th { padding: 10px 14px; text-align: left; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .06em; color: #e2e8f0; }
        thead th:last-child { text-align: right; }
        tbody tr { border-bottom: 1px solid #f1f5f9; }
        tbody tr:nth-child(even) { background: #f8fafc; }
        tbody td { padding: 12px 14px; font-size: 12px; color: #334155; }
        tbody td:last-child { text-align: right; font-weight: 700; color: #0C1F3D; }

        /* TOTAUX */
        .totals { display: flex; justify-content: flex-end; margin-bottom: 24px; }
        .totals-box { width: 260px; }
        .total-row {
            display: flex; justify-content: space-between;
            padding: 6px 0; font-size: 12px; color: #475569;
            border-bottom: 1px solid #f1f5f9;
        }
        .total-grand {
            display: flex; justify-content: space-between;
            padding: 10px 0 4px; font-size: 16px; font-weight: 800;
            color: #0C1F3D; border-top: 2px solid #F77F00; margin-top: 4px;
        }
        .total-grand span:last-child { color: #F77F00; }

        /* PAIEMENT */
        .payment-box {
            display: flex; align-items: center; gap: 16px;
            padding: 14px 18px;
            background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;
            margin-bottom: 24px;
        }
        .payment-icon { font-size: 24px; }
        .payment-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: #065f46; margin-bottom: 3px; }
        .payment-method { font-size: 14px; font-weight: 700; color: #0C1F3D; }

        /* FOOTER */
        .footer {
            display: flex; justify-content: space-between; align-items: center;
            padding-top: 18px;
            border-top: 1px solid #e2e8f0;
            margin-top: 8px;
        }
        .footer-brand { font-size: 13px; font-weight: 700; color: #0C1F3D; }
        .footer-sub { font-size: 10px; color: #94a3b8; margin-top: 2px; }
        .footer-orange { color: #F77F00; }
        .footer-legal { font-size: 9px; color: #94a3b8; text-align: right; max-width: 240px; line-height: 1.7; }

        /* IMPRESSION */
        .no-print { margin-bottom: 20px; text-align: right; }
        .btn-print {
            padding: 10px 18px; background: #0C1F3D; color: #fff;
            border: none; border-radius: 8px; font-size: 13px; font-weight: 600;
            cursor: pointer; margin-right: 8px;
        }
        .btn-back {
            padding: 10px 18px; background: #f8fafc; color: #0C1F3D;
            border: 1px solid #e2e8f0; border-radius: 8px; font-size: 13px; font-weight: 600;
            text-decoration: none; display: inline-block;
        }

        @media print {
            .no-print { display: none !important; }
            .ci-flag { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            thead tr { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .party.emitter { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>

<div class="ci-flag"></div>

<div class="container">

    {{-- BOUTONS (masqués à l'impression) --}}
    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ {{ __('Imprimer') }}</button>
        <a href="{{ route('boutique.clientboutique') }}" class="btn-back">← {{ __('Retour à la boutique') }}</a>
    </div>

    {{-- EN-TÊTE --}}
    <div class="header">
        <div class="brand">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" class="brand-img" alt="Hôtelio">
            @else
                <div class="brand-logo">H</div>
            @endif
            <div class="brand-info">
                <div class="name">Hôtelio</div>
                <div class="sub">{{ __("Plateforme hôtelière · Côte d'Ivoire") }}</div>
            </div>
        </div>
        <div class="invoice-meta">
            <div class="invoice-title">{{ __('Facture') }}</div>
            <div class="invoice-num">N° <strong>#{{ $commande->id }}</strong></div>
            <div class="invoice-num">{{ __('Émise le') }} {{ now()->format('d/m/Y') }}</div>
            @if($commande->statut === 'payée')
                <span class="status-badge badge-paid">✓ {{ __('Payée') }}</span>
            @elseif($commande->statut === 'en attente')
                <span class="status-badge badge-pending">⏳ {{ __('En attente') }}</span>
            @else
                <span class="status-badge badge-cancel">✗ {{ __('Annulée') }}</span>
            @endif
        </div>
    </div>

    {{-- PARTIES --}}
    <div class="parties">
        <div class="party emitter">
            <div class="party-label">{{ __('Émetteur') }}</div>
            <div class="party-name">Hôtelio</div>
            <p>{{ __('Plateforme de réservation hôtelière') }}<br>Abidjan, Côte d'Ivoire 🇨🇮<br>contact@hotelio.ci</p>
        </div>
        <div class="party receiver">
            <div class="party-label">{{ __('Client') }}</div>
            <div class="party-name">{{ optional($commande->user)->name ?? __('Client') }}</div>
            <p>{{ optional($commande->user)->email ?? '—' }}</p>
        </div>
    </div>

    {{-- DATES --}}
    <div class="dates">
        <div class="date-box">
            <div class="date-label">{{ __('Date de commande') }}</div>
            <div class="date-value">{{ $commande->created_at?->format('d/m/Y') ?? '—' }}</div>
        </div>
        <div class="date-box">
            <div class="date-label">{{ __('Heure') }}</div>
            <div class="date-value">{{ $commande->created_at?->format('H:i') ?? '' }}</div>
        </div>
        <div class="date-box">
            <div class="date-label">{{ __('Méthode de paiement') }}</div>
            <div class="date-value">{{ $commande->mode_paiement ? ucfirst(str_replace('_', ' ', $commande->mode_paiement)) : __('Non renseigné') }}</div>
        </div>
        <div class="date-box">
            <div class="date-label">{{ __('Statut') }}</div>
            <div class="date-value" style="color:{{ in_array($commande->statut,['payé','payée']) ? '#009A44' : '#F77F00' }};">
                {{ ucfirst($commande->statut) }}
            </div>
        </div>
    </div>

    {{-- COMPTE DÉBITÉ --}}
    @if($commande->payment_reference)
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:10px 16px;margin-bottom:24px;display:flex;align-items:center;gap:12px;">
        <div style="font-size:18px;">💳</div>
        <div>
            <div style="font-size:9px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94a3b8;margin-bottom:3px;">{{ __('Compte / numéro débité') }}</div>
            <div style="font-size:14px;font-weight:800;color:#0C1F3D;font-family:monospace;">{{ $commande->payment_reference }}</div>
        </div>
    </div>
    @endif

    {{-- TABLEAU PRODUITS --}}
    <div class="table-label">{{ __('Détail des produits') }}</div>
    <table>
        <thead>
            <tr>
                <th>{{ __('Produit') }}</th>
                <th>{{ __('Quantité') }}</th>
                <th>{{ __('Prix unitaire') }}</th>
                <th>{{ __('Montant') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($commande->lignes as $ligne)
            <tr>
                <td>{{ optional($ligne->product)->name ?? __('Produit #:id', ['id' => $ligne->product_id]) }}</td>
                <td>× {{ $ligne->quantite }}</td>
                <td>{{ number_format($ligne->prix, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($ligne->prix * $ligne->quantite, 0, ',', ' ') }} FCFA</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTAUX --}}
    <div class="totals">
        <div class="totals-box">
            <div class="total-grand">
                <span>{{ __('Total TTC') }}</span>
                <span>{{ number_format($commande->total, 0, ',', ' ') }} FCFA</span>
            </div>
        </div>
    </div>

    {{-- PAIEMENT --}}
    @php
    $isPaid = in_array($commande->statut, ['payé','payée']);
    @endphp
    @if($isPaid)
    <div class="payment-box">
        <div class="payment-icon">✅</div>
        <div style="flex:1;">
            <div class="payment-label">{{ __('Paiement reçu via') }}</div>
            <div class="payment-method">{{ $commande->mode_paiement ? ucfirst(str_replace('_', ' ', $commande->mode_paiement)) : __('Payé') }}</div>
            @if($commande->payment_reference)
            <div style="margin-top:5px;font-size:12px;color:#334155;font-family:monospace;font-weight:700;">
                {{ $commande->payment_reference }}
            </div>
            @endif
            @if($commande->transaction_id)
            <div style="margin-top:3px;font-size:10px;color:#94a3b8;font-family:monospace;">
                {{ __('Réf :') }} {{ $commande->transaction_id }}
            </div>
            @endif
        </div>
        <div style="margin-left:auto;text-align:right;">
            <div style="font-size:9px;color:#065f46;font-weight:700;text-transform:uppercase;letter-spacing:.08em;">{{ __('Montant réglé') }}</div>
            <div style="font-size:18px;font-weight:800;color:#0C1F3D;">{{ number_format($commande->total, 0, ',', ' ') }} FCFA</div>
        </div>
    </div>
    @endif

    {{-- FOOTER --}}
    <div class="footer">
        <div>
            <div class="footer-brand">Hôtelio</div>
            <div class="footer-sub">Côte d'Ivoire · contact@hotelio.ci</div>
            <div class="footer-sub footer-orange">www.hotelio.ci</div>
        </div>
        <div class="footer-legal">
            {{ __('Cette facture est générée automatiquement par Hôtelio.') }}<br>
            {{ __('Elle constitue un justificatif de paiement valide.') }}<br>
            {{ __('Conservez-la pour vos archives.') }}
        </div>
    </div>

</div>
</body>
</html>
