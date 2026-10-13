<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('Facture') }} N° {{ $facture->numero ?? $facture->id }} — Hôtelio</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1a1a2e;
            background: #fff;
            font-size: 13px;
            line-height: 1.5;
        }

        .ci-flag {
            height: 7px;
            background: linear-gradient(to right, #F77F00 33.33%, #ffffff 33.33% 66.66%, #009A44 66.66%);
        }

        .container { padding: 36px 48px; max-width: 820px; margin: 0 auto; }

        /* EN-TÊTE */
        .header {
            display: flex; justify-content: space-between; align-items: flex-start;
            padding-bottom: 24px;
            border-bottom: 2px solid #F77F00;
            margin-bottom: 28px;
        }
        .brand { display: flex; align-items: center; gap: 14px; }
        .brand-logo {
            width: 52px; height: 52px; background: #F77F00; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; font-weight: 900; color: #fff;
        }
        .brand-img { width: 52px; height: 52px; border-radius: 14px; object-fit: contain; }
        .brand-name { font-size: 22px; font-weight: 800; color: #0C1F3D; }
        .brand-sub  { font-size: 10px; color: #009A44; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; margin-top: 2px; }

        .invoice-meta { text-align: right; }
        .invoice-title { font-size: 26px; font-weight: 800; color: #F77F00; text-transform: uppercase; letter-spacing: .06em; }
        .invoice-ref { font-size: 13px; color: #555; margin-top: 4px; }
        .invoice-ref strong { color: #0C1F3D; font-size: 15px; }

        .status-badge {
            display: inline-block; margin-top: 8px;
            padding: 4px 14px; border-radius: 20px;
            font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .08em;
        }
        .badge-paid    { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .badge-pending { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }

        /* INFO SECTION */
        .info-grid { display: flex; gap: 16px; margin-bottom: 24px; }
        .info-box {
            flex: 1; padding: 14px 18px; border-radius: 10px;
        }
        .info-box.dark { background: #0C1F3D; }
        .info-box.light { background: #f8fafc; border: 1px solid #e2e8f0; }
        .info-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .12em; margin-bottom: 8px; }
        .info-box.dark .info-label { color: #94a3b8; }
        .info-box.light .info-label { color: #64748b; }
        .info-name { font-size: 14px; font-weight: 700; margin-bottom: 4px; }
        .info-box.dark .info-name { color: #e2e8f0; }
        .info-box.light .info-name { color: #1e293b; }
        .info-detail { font-size: 11px; line-height: 1.7; }
        .info-box.dark .info-detail { color: #94a3b8; }
        .info-box.light .info-detail { color: #475569; }

        /* DATES */
        .dates { display: flex; gap: 12px; margin-bottom: 24px; }
        .date-box {
            flex: 1; padding: 10px 14px;
            background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px;
        }
        .date-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #94a3b8; margin-bottom: 3px; }
        .date-value { font-size: 13px; font-weight: 700; color: #0C1F3D; }

        /* TABLE */
        .table-section-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #64748b; margin-bottom: 8px; }
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
        .totals-box { width: 280px; }
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

        /* FOOTER */
        .footer {
            display: flex; justify-content: space-between; align-items: center;
            padding-top: 18px; border-top: 1px solid #e2e8f0; margin-top: 8px;
        }
        .footer-brand { font-size: 13px; font-weight: 700; color: #0C1F3D; }
        .footer-sub { font-size: 10px; color: #94a3b8; margin-top: 2px; }
        .footer-orange { color: #F77F00; }
        .footer-legal { font-size: 9px; color: #94a3b8; text-align: right; max-width: 240px; line-height: 1.7; }

        .no-print { margin-bottom: 20px; text-align: right; }
        .btn-print {
            padding: 10px 18px; background: #0C1F3D; color: #fff;
            border: none; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; margin-right: 8px;
        }

        @media print {
            .no-print { display: none !important; }
            .ci-flag, thead tr, .info-box.dark {
                -webkit-print-color-adjust: exact; print-color-adjust: exact;
            }
        }
    </style>
</head>
<body>

<div class="ci-flag"></div>

<div class="container">

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ {{ __('Imprimer la facture') }}</button>
    </div>

    {{-- EN-TÊTE --}}
    <div class="header">
        <div class="brand">
            @if(file_exists(public_path('images/logo.png')))
                <img src="{{ asset('images/logo.png') }}" class="brand-img" alt="Hôtelio">
            @else
                <div class="brand-logo">H</div>
            @endif
            <div>
                <div class="brand-name">Hôtelio</div>
                <div class="brand-sub">{{ __("Plateforme hôtelière · Côte d'Ivoire") }}</div>
            </div>
        </div>
        <div class="invoice-meta">
            <div class="invoice-title">{{ __('Facture') }}</div>
            <div class="invoice-ref">N° <strong>{{ $facture->numero ?? $facture->id }}</strong></div>
            <div class="invoice-ref">{{ __('Date :') }} {{ $facture->date_facture ?? now()->format('d/m/Y') }}</div>
            @if(isset($facture->statut))
                <span class="status-badge {{ $facture->statut === 'payée' ? 'badge-paid' : 'badge-pending' }}">
                    {{ $facture->statut === 'payée' ? '✓ '.__('Payée') : '⏳ ' . ucfirst($facture->statut) }}
                </span>
            @endif
        </div>
    </div>

    {{-- PARTIES --}}
    <div class="info-grid">
        <div class="info-box dark">
            <div class="info-label">{{ __('Émetteur') }}</div>
            <div class="info-name">Hôtelio</div>
            <div class="info-detail">{{ __('Plateforme de réservation hôtelière') }}<br>Abidjan, Côte d'Ivoire 🇨🇮<br>contact@hotelio.ci</div>
        </div>
        <div class="info-box light">
            <div class="info-label">{{ __('Client') }}</div>
            <div class="info-name">{{ $facture->client }}</div>
            <div class="info-detail">{{ __('Hôtel :') }} {{ $facture->hotel }}</div>
        </div>
    </div>

    {{-- DATES --}}
    <div class="dates">
        <div class="date-box">
            <div class="date-label">{{ __('Date') }}</div>
            <div class="date-value">{{ $facture->date_facture ?? now()->format('d/m/Y') }}</div>
        </div>
        <div class="date-box">
            <div class="date-label">{{ __('Statut') }}</div>
            <div class="date-value" style="color:{{ ($facture->statut ?? '') === 'payée' ? '#009A44' : '#F77F00' }};">
                {{ ucfirst($facture->statut ?? __('En attente')) }}
            </div>
        </div>
        @if(isset($facture->total_ttc))
        <div class="date-box">
            <div class="date-label">{{ __('Montant TTC') }}</div>
            <div class="date-value" style="color:#F77F00;">{{ number_format($facture->total_ttc, 0, ',', ' ') }} FCFA</div>
        </div>
        @endif
    </div>

    {{-- TABLEAU --}}
    <div class="table-section-label">{{ __('Détail des prestations') }}</div>
    <table>
        <thead>
            <tr>
                <th>{{ __('Désignation') }}</th>
                <th>{{ __('Qté') }}</th>
                <th>{{ __('Prix unitaire') }}</th>
                <th>{{ __('Total') }}</th>
            </tr>
        </thead>
        <tbody>
            @if(isset($facture->items) && count($facture->items))
                @foreach($facture->items as $item)
                <tr>
                    <td>{{ $item->designation }}</td>
                    <td>× {{ $item->quantite }}</td>
                    <td>{{ number_format($item->prix, 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($item->total, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
            @else
                <tr>
                    <td>{{ __('Prestation hôtelière') }}</td>
                    <td>× 1</td>
                    <td>{{ number_format($facture->montant, 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($facture->montant, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- TOTAUX --}}
    <div class="totals">
        <div class="totals-box">
            @if(isset($facture->total_ht))
            <div class="total-row">
                <span>{{ __('Sous-total HT') }}</span>
                <span>{{ number_format($facture->total_ht, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="total-row">
                <span>{{ __('TVA (18%)') }}</span>
                <span>{{ number_format($facture->tva, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="total-row" style="color:#0C1F3D;font-weight:600;">
                <span>{{ __('Taxe Hôtelière — TH (2%)') }}</span>
                <span>{{ number_format($facture->taxe_hoteliere, 0, ',', ' ') }} FCFA</span>
            </div>
            <div class="total-grand">
                <span>{{ __('Total TTC') }}</span>
                <span>{{ number_format($facture->total_ttc, 0, ',', ' ') }} FCFA</span>
            </div>
            @else
            <div class="total-grand">
                <span>{{ __('Total') }}</span>
                <span>{{ number_format($facture->montant ?? 0, 0, ',', ' ') }} FCFA</span>
            </div>
            @endif
        </div>
    </div>

    {{-- MODE DE PAIEMENT & NOTES --}}
    @if($facture->mode_paiement || $facture->notes)
    <div style="margin-bottom:20px;padding:12px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:11px;">
        @if($facture->mode_paiement)
        <div style="margin-bottom:4px;"><strong style="color:#0C1F3D;">{{ __('Mode de paiement :') }}</strong> {{ $facture->mode_paiement }}</div>
        @endif
        @if($facture->notes)
        <div><strong style="color:#0C1F3D;">{{ __('Notes :') }}</strong> {{ $facture->notes }}</div>
        @endif
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
            {{ __('Merci pour votre confiance — Conservez-la pour vos archives.') }}
        </div>
    </div>

</div>

</body>
</html>
