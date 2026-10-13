<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>{{ __('Facture') }} {{ $reservation->code }} — Hôtelio</title>
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: DejaVu Sans, Arial, sans-serif; color: #1a1a1a; font-size: 13px; background: #fff; }

  /* Bande Côte d'Ivoire */
  .ci-flag { height: 6px; background: linear-gradient(to right, #F77F00 33%, #FFFFFF 33% 66%, #009A44 66%); }

  .container { padding: 32px 40px; }

  /* En-tête */
  .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; padding-bottom: 24px; border-bottom: 2px solid #F77F00; }
  .brand { display: flex; align-items: center; gap: 12px; }
  .brand-icon { width: 48px; height: 48px; background: #F77F00; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 24px; color: white; font-weight: 900; }
  .brand-name { font-size: 26px; font-weight: 700; color: #0C1F3D; letter-spacing: -0.5px; }
  .brand-sub { font-size: 11px; color: #009A44; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; }

  .invoice-meta { text-align: right; }
  .invoice-title { font-size: 22px; font-weight: 700; color: #F77F00; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 6px; }
  .invoice-ref { font-size: 13px; color: #555; }
  .invoice-ref strong { color: #0C1F3D; font-size: 15px; }
  .invoice-status { display: inline-block; margin-top: 8px; padding: 4px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; }
  .status-paid { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
  .status-pending { background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; }

  /* Bloc parties */
  .parties { display: flex; gap: 24px; margin-bottom: 28px; }
  .party-box { flex: 1; padding: 16px 20px; border-radius: 10px; }
  .party-box.emitter { background: #0C1F3D; color: white; }
  .party-box.receiver { background: #f8fafc; border: 1px solid #e2e8f0; }
  .party-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em; margin-bottom: 10px; opacity: 0.7; }
  .party-name { font-size: 15px; font-weight: 700; margin-bottom: 4px; }
  .party-detail { font-size: 12px; opacity: 0.75; line-height: 1.7; }
  .party-box.emitter .party-detail { color: #cbd5e1; }
  .party-box.receiver .party-title { color: #64748b; }

  /* Dates */
  .dates-row { display: flex; gap: 16px; margin-bottom: 28px; }
  .date-box { flex: 1; padding: 12px 16px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; }
  .date-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 4px; }
  .date-value { font-size: 14px; font-weight: 600; color: #0C1F3D; }

  /* Tableau */
  .table-section { margin-bottom: 24px; }
  .table-title { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #64748b; margin-bottom: 10px; }
  table { width: 100%; border-collapse: collapse; }
  thead tr { background: #0C1F3D; color: white; }
  thead th { padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.06em; }
  thead th:last-child { text-align: right; }
  tbody tr { border-bottom: 1px solid #f1f5f9; }
  tbody tr:nth-child(even) { background: #f8fafc; }
  tbody td { padding: 12px 14px; font-size: 13px; color: #334155; }
  tbody td:last-child { text-align: right; font-weight: 600; }

  /* Totaux */
  .totals { display: flex; justify-content: flex-end; margin-bottom: 28px; }
  .totals-box { width: 280px; }
  .total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 13px; color: #555; border-bottom: 1px solid #f1f5f9; }
  .total-row.grand { font-size: 16px; font-weight: 700; color: #0C1F3D; border-bottom: none; padding-top: 12px; border-top: 2px solid #F77F00; margin-top: 4px; }
  .total-row.grand span:last-child { color: #F77F00; }

  /* Paiement */
  .payment-box { padding: 16px 20px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; margin-bottom: 24px; display: flex; align-items: center; gap: 16px; }
  .payment-icon { font-size: 28px; }
  .payment-label { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #065f46; margin-bottom: 3px; }
  .payment-method { font-size: 15px; font-weight: 700; color: #0C1F3D; }

  /* Services */
  .services-row { display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 24px; }
  .service-tag { padding: 4px 12px; background: rgba(247,127,0,.1); border: 1px solid rgba(247,127,0,.25); border-radius: 20px; font-size: 11px; font-weight: 600; color: #c05c00; }

  /* Footer */
  .footer { padding-top: 20px; border-top: 1px solid #e2e8f0; display: flex; justify-content: space-between; align-items: center; }
  .footer-brand { font-size: 14px; font-weight: 700; color: #0C1F3D; }
  .footer-sub { font-size: 11px; color: #94a3b8; margin-top: 2px; }
  .footer-legal { font-size: 10px; color: #94a3b8; text-align: right; max-width: 250px; line-height: 1.6; }

  .watermark { position: fixed; top: 45%; left: 50%; transform: translate(-50%,-50%) rotate(-30deg); font-size: 80px; font-weight: 900; color: rgba(0,154,68,.06); pointer-events: none; z-index: 0; text-transform: uppercase; letter-spacing: 10px; }
</style>
</head>
<body>

<div class="watermark">PAYÉ</div>

<div class="ci-flag"></div>
<div class="container">

  <!-- En-tête -->
  <div class="header">
    <div class="brand">
      @if(file_exists(public_path('images/logo.png')))
        <img src="{{ asset('images/logo.png') }}" alt="Hôtelio" style="width:52px;height:52px;border-radius:12px;object-fit:contain;">
      @else
        <div class="brand-icon">H</div>
      @endif
      <div>
        <div class="brand-name">Hôtelio</div>
        <div class="brand-sub">Plateforme de réservation hôtelière</div>
      </div>
    </div>
    <div class="invoice-meta">
      <div class="invoice-title">{{ __('Facture') }}</div>
      <div class="invoice-ref">{{ __('Référence :') }} <strong>{{ $reservation->code }}</strong></div>
      <div class="invoice-ref">{{ __('Émise le :') }} {{ now()->format('d/m/Y') }}</div>
      <div>
        <span class="invoice-status {{ $reservation->paid ? 'status-paid' : 'status-pending' }}">
          {{ $reservation->paid ? '✓ '.__('Payée') : __('En attente') }}
        </span>
      </div>
    </div>
  </div>

  <!-- Émetteur / Destinataire -->
  <div class="parties">
    <div class="party-box emitter">
      <div class="party-title">{{ __('Émetteur') }}</div>
      <div class="party-name">Hôtelio</div>
      <div class="party-detail">
        {{ __('Plateforme de réservation hôtelière') }}<br>
        Abidjan, Côte d'Ivoire 🇨🇮<br>
        contact@hotelio.ci<br>
        www.hotelio.ci
      </div>
    </div>
    <div class="party-box receiver">
      <div class="party-title">{{ __('Client') }}</div>
      <div class="party-name">
        {{ optional($reservation->client)->first_name ?? __('Client') }}
        {{ optional($reservation->client)->last_name ?? '' }}
      </div>
      <div class="party-detail" style="color:#475569">
        {{ optional($reservation->client)->email ?? '—' }}<br>
        {{ optional($reservation->client)->phone ?? '' }}<br>
        {{ optional($reservation->client)->city ?? '' }}
        @if(optional($reservation->client)->nationality)
          · {{ $reservation->client->nationality }}
        @endif
      </div>
    </div>
    <div class="party-box receiver">
      <div class="party-title">{{ __('Hôtel') }}</div>
      <div class="party-name">{{ $reservation->hotel->name }}</div>
      <div class="party-detail" style="color:#475569">
        {{ $reservation->hotel->quartier }}, {{ $reservation->hotel->city }}<br>
        {{ $reservation->hotel->phone ?? '—' }}<br>
        {{ str_repeat('★', $reservation->hotel->stars) }} — {{ $reservation->hotel->category }}
      </div>
    </div>
  </div>

  <!-- Dates clés -->
  <div class="dates-row">
    <div class="date-box">
      <div class="date-label">{{ __('Date de réservation') }}</div>
      <div class="date-value">{{ $reservation->created_at->format('d/m/Y') }}</div>
    </div>
    <div class="date-box">
      <div class="date-label">{{ __('Arrivée (Check-in)') }}</div>
      <div class="date-value">{{ $reservation->checkin->format('d/m/Y') }}</div>
    </div>
    <div class="date-box">
      <div class="date-label">{{ __('Départ (Check-out)') }}</div>
      <div class="date-value">{{ $reservation->checkout->format('d/m/Y') }}</div>
    </div>
    <div class="date-box">
      <div class="date-label">{{ __('Durée du séjour') }}</div>
      <div class="date-value">{{ $reservation->nights }} nuit{{ $reservation->nights > 1 ? 's' : '' }}</div>
    </div>
    <div class="date-box">
      <div class="date-label">{{ __('Statut') }}</div>
      <div class="date-value" style="color:{{ $reservation->status === 'confirmée' ? '#009A44' : '#F77F00' }}">
        {{ ucfirst($reservation->status) }}
      </div>
    </div>
  </div>

  <!-- Tableau des prestations -->
  <div class="table-section">
    <div class="table-title">{{ __('Détail des prestations') }}</div>
    <table>
      <thead>
        <tr>
          <th>{{ __('Description') }}</th>
          <th>{{ __('Type') }}</th>
          <th>{{ __('Nuits') }}</th>
          <th>{{ __('Prix / nuit') }}</th>
          <th>{{ __('Total') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <strong>{{ __('Chambre') }} {{ $reservation->room }}</strong><br>
            <span style="font-size:11px;color:#94a3b8">{{ $reservation->hotel->name }} · {{ $reservation->hotel->city }}</span>
          </td>
          <td>{{ __('Hébergement') }}</td>
          <td style="text-align:center">{{ $reservation->nights }}</td>
          <td>{{ number_format($reservation->price, 0, ',', ' ') }} FCFA</td>
          <td>{{ number_format($reservation->total, 0, ',', ' ') }} FCFA</td>
        </tr>
      </tbody>
    </table>
  </div>

  <!-- Totaux -->
  <div class="totals">
    <div class="totals-box">
      <div class="total-row">
        <span>{{ __('Sous-total HT') }}</span>
        <span>{{ number_format((int)($reservation->total / 1.18), 0, ',', ' ') }} FCFA</span>
      </div>
      <div class="total-row">
        <span>{{ __('TVA (18%)') }}</span>
        <span>{{ number_format($reservation->total - (int)($reservation->total / 1.18), 0, ',', ' ') }} FCFA</span>
      </div>
      <div class="total-row grand">
        <span>{{ __('Total TTC') }}</span>
        <span>{{ number_format($reservation->total, 0, ',', ' ') }} FCFA</span>
      </div>
    </div>
  </div>

  <!-- Paiement -->
  <div class="payment-box">
    <div class="payment-icon">✅</div>
    <div>
      <div class="payment-label">{{ __('Paiement') }} {{ $reservation->paid ? __('reçu') : __('en attente') }}</div>
      <div class="payment-method">{{ $reservation->payment_method ?: __('Non spécifié') }}</div>
    </div>
    <div style="margin-left:auto;text-align:right">
      <div style="font-size:11px;color:#065f46;font-weight:600">{{ __('Montant réglé') }}</div>
      <div style="font-size:20px;font-weight:700;color:#0C1F3D">
        {{ $reservation->paid ? number_format($reservation->total, 0, ',', ' ') . ' FCFA' : '—' }}
      </div>
    </div>
  </div>

  <!-- Services de l'hôtel -->
  @if($reservation->hotel->services && count($reservation->hotel->services))
  <div class="table-title" style="margin-bottom:8px">{{ __('Services inclus') }}</div>
  <div class="services-row">
    @foreach($reservation->hotel->services as $svc)
      <span class="service-tag">✓ {{ $svc }}</span>
    @endforeach
  </div>
  @endif

  <!-- Footer -->
  <div class="footer">
    <div>
      <div class="footer-brand">Hôtelio</div>
      <div class="footer-sub">Côte d'Ivoire · contact@hotelio.ci</div>
      <div class="footer-sub" style="margin-top:4px;color:#F77F00">www.hotelio.ci</div>
    </div>
    <div class="footer-legal">
      {{ __('Cette facture est générée automatiquement par la plateforme Hôtelio.') }}<br>
      {{ __('Elle constitue un justificatif de paiement valide.') }}<br>
      {{ __('Conservez-la pour vos archives.') }}
    </div>
  </div>

</div>
</body>
</html>
