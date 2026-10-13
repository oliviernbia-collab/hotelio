{{-- @extends('layouts.app')

@section('content')

<style>
.container-facture {
    padding: 20px;
}

/* HEADER */
.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.header h2 {
    font-weight: bold;
}

/* STATS */
.stats {
    display: flex;
    gap: 15px;
    margin-bottom: 20px;
}

.card-stat {
    flex: 1;
    padding: 15px;
    border-radius: 10px;
    color: white;
    font-weight: bold;
}

.total { background: orange; }
.payees { background: #2e7d32; }
.impayees { background: #c62828; }

/* TABLE */
.table-pro {
    width: 100%;
    border-collapse: collapse;
    background: #fff;
    border-radius: 10px;
    overflow: hidden;
}

.table-pro th {
    background: rgb(84, 84, 126);
    color: white;
    padding: 12px;
    text-align: left;
}

.table-pro td {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

/* BADGE */
.badge {
    padding: 5px 10px;
    border-radius: 20px;
    color: white;
    font-size: 12px;
}

.paye { background: #2e7d32; }
.nonpaye { background: #c62828; }

/* BUTTONS */
.btn {
    padding: 6px 10px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
    margin-right: 5px;
}

.btn-add { background: #d32f2f; color: white; }
.btn-edit { background: #1976d2; color: white; }
.btn-delete { background: #000000; color: white; }
.btn-pdf { background: #f57c00; color: white; }

form {
    display: inline;
}
</style>

<div class="container-facture">

    <!-- HEADER -->
    <div class="header">
        <h2>📄 Gestion des factures</h2>
        <a href="{{ route('facturations.create') }}" class="btn btn-add">+ Ajouter</a>
    </div>

    <!-- STATS -->
    <div class="stats">
        <div class="card-stat total">
            Total <br>
            {{ number_format($stats['total'], 0, ',', ' ') }} FCFA
        </div>

        <div class="card-stat payees">
            Payées <br>
            {{ number_format($stats['payees'], 0, ',', ' ') }} FCFA
        </div>

        <div class="card-stat impayees">
            Impayées <br>
            {{ number_format($stats['impayees'], 0, ',', ' ') }} FCFA
        </div>
    </div>

    <!-- TABLE -->
    <table class="table-pro">
        <thead>
            <tr>
                <th>Client</th>
                <th>Hôtel</th>
                <th>Montant</th>
                <th>Date</th>
                <th>Statut</th>
                <th>Actions</th>
            </tr>
        </thead>

        <tbody>
            @foreach($factures as $facture)
            <tr>
                <td>{{ $facture->client }}</td>
                <td>{{ $facture->hotel }}</td>
                <td>{{ number_format($facture->montant, 0, ',', ' ') }} FCFA</td>
                <td>{{ $facture->date_facture }}</td>

                <td>
                    <span class="badge {{ $facture->statut == 'payé' ? 'paye' : 'nonpaye' }}">
                        {{ $facture->statut }}
                    </span>
                </td>

                <td>
                    <a href="{{ route('facturations.edit', $facture) }}" class="btn btn-edit">✏️</a>

                    <a href="{{ route('facturations.pdf', $facture) }}" class="btn btn-pdf">📄</a>

                    <form action="{{ route('facturations.destroy', $facture) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-delete">🗑️</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

</div>

@endsection --}}

@extends('layouts.app')

@section('content')

<style>
    .fac-page{
  --bg:#0f172a;
  --card:#111827;
  --card-soft:#1f2937;
  --border:#1e293b;

  --text:#e5e7eb;
  --muted:#94a3b8;
  --muted2:#64748b;

  --teal:#14b8a6;
  --teal-dim:#0f766e20;

  --green:#22c55e;
  --green-dim:#22c55e20;

  --red:#ef4444;
  --blue:#3b82f6;
  --gold:#eab308;
  --gold-light:#facc15;
}

/* BUTTONS */
.btn{
  border:none;
  padding:10px 16px;
  border-radius:8px;
  cursor:pointer;
  font-weight:600;
  transition:.3s;
  display:inline-flex;
  align-items:center;
  gap:6px;
}

.btn:hover{transform:translateY(-2px);opacity:.9;}

.btn-teal{background:var(--teal);color:#fff;}
.btn-gold{background:var(--gold);color:#000;}
.btn-primary{background:var(--blue);color:#fff;}
.btn-danger{background:var(--red);color:#fff;}
.btn-secondary{background:#334155;color:#fff;}
.btn-print{background:#9333ea;color:#fff;}

/* CARDS */
.card, .table-card{
  background:rgba(17,24,39,.85);
  backdrop-filter:blur(10px);
  border:1px solid var(--border);
  border-radius:14px;
  box-shadow:0 10px 30px rgba(0,0,0,.3);
}

.card{
  padding:18px;
}

/* HEADER */
.page-header-row{
  display:flex;
  justify-content:space-between;
  align-items:center;
  margin-bottom:20px;
}

.page-title{
  font-size:1.4rem;
  font-weight:700;
}

.page-sub{
  color:var(--muted);
  font-size:.85rem;
}

/* STATS */
.stats-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
  gap:15px;
  margin-bottom:20px;
}

.stat-card{
  padding:16px;
  border-radius:12px;
  background:linear-gradient(135deg,#020617,#111827);
  border:1px solid var(--border);
  transition:.3s;
}

.stat-card:hover{
  transform:translateY(-5px);
}

.stat-label{
  font-size:.75rem;
  color:var(--muted);
}

.stat-value{
  font-size:1.4rem;
  font-weight:bold;
}

.stat-desc{
  font-size:.75rem;
  color:var(--muted2);
}

.trend-up{
  color:var(--green);
  font-size:.75rem;
}

/* FILTER */
.filter-bar{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}

.filter-bar input,
.filter-bar select{
  padding:10px;
  border-radius:8px;
  border:1px solid var(--border);
  background:#1e293b;
  color:#e2e8f0;
  color-scheme: dark;
}
.filter-bar select option {
  background:#1e293b;
  color:#e2e8f0;
}

/* TABLE */
table{
  width:100%;
  border-collapse:collapse;
}

thead{
  background:#020617;
}

th, td{
  padding:12px;
  font-size:.85rem;
}

th{
  color:var(--muted);
  text-transform:uppercase;
  font-size:.7rem;
}

tbody tr{
  border-top:1px solid var(--border);
  transition:.2s;
}

tbody tr:hover{
  background:rgba(255,255,255,.03);
}

/* STATUS */
.status-pill{
  padding:5px 10px;
  border-radius:20px;
  font-size:.7rem;
  display:inline-flex;
  align-items:center;
  gap:5px;
}

.status-paid{background:#22c55e20;color:var(--green);}
.status-pending{background:#facc1520;color:var(--gold);}
.status-overdue{background:#ef444420;color:var(--red);}
.status-draft{background:#64748b20;color:var(--muted);}
.status-partial{background:#fb923c20;color:#fb923c;}

/* ACTIONS */
.action-link{
  background:none;
  border:none;
  cursor:pointer;
  font-size:1rem;
  margin-right:6px;
  transition:.2s;
}

.action-link:hover{
  transform:scale(1.2);
}

.action-link.red{color:var(--red);}
.action-link.gold{color:var(--gold);}

/* MODAL */
.modal-overlay{
  position:fixed;
  inset:0;
  background:rgba(0,0,0,.6);
  display:none;
  justify-content:center;
  align-items:center;
  z-index:1000;
}

.modal-overlay.open{
  display:flex;
}

.modal{
  background:#020617;
  border-radius:14px;
  width:100%;
  max-height:90vh;
  overflow:auto;
  box-shadow:0 20px 60px rgba(0,0,0,.6);
}

.modal-header{
  padding:15px;
  border-bottom:1px solid var(--border);
  display:flex;
  justify-content:space-between;
}

.modal-body{
  padding:15px;
}

.modal-footer{
  padding:15px;
  border-top:1px solid var(--border);
  display:flex;
  justify-content:flex-end;
  gap:10px;
}

/* FORM */
.form-grid{
  display:grid;
  grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
  gap:12px;
}

.form-group label{
  font-size:.7rem;
  color:var(--muted);
}

.form-group input,
.form-group select,
.form-group textarea{
  width:100%;
  padding:10px;
  border-radius:8px;
  border:1px solid var(--border);
  background:#020617;
  color:#fff;
}

/* LINES */
.line-item-row{
  display:grid;
  grid-template-columns:2fr 1fr 1fr 1fr auto;
  gap:8px;
  margin-bottom:8px;
}

.line-item-row input{
  padding:8px;
  border-radius:6px;
  border:1px solid var(--border);
  background:#020617;
  color:#fff;
}

/* TOAST */
#toast{
  position:fixed;
  bottom:20px;
  right:20px;
  background:#020617;
  border:1px solid var(--border);
  padding:10px 14px;
  border-radius:8px;
  opacity:0;
  transition:.4s;
}

/* RESPONSIVE */
@media(max-width:768px){
  .page-header-row{
    flex-direction:column;
    align-items:flex-start;
    gap:10px;
  }

  .line-item-row{
    grid-template-columns:1fr;
  }

  th,td{
    font-size:.75rem;
  }
}
</style>

<!-- MAIN -->
<div class="fac-page">
    <div style="margin-top:13px;">
      <button class="btn btn-teal" onclick="showPage('facturation')">→ {{ __('Gérer la facturation') }}</button>
    </div>

<!-- ═══ FACTURATION ═══ -->
<div class="page-section" id="page-facturation">
  <div class="page-header-row">
    <div>
      <div class="page-title">{{ __('Facturation') }}</div>
      <div class="page-sub">{{ __('Créez, suivez et imprimez vos factures.') }}</div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
      <button class="btn btn-gold" onclick="openModal('modalNew')">+ {{ __('Nouvelle facture') }}</button>
      <button class="btn btn-teal" onclick="exportCSV()">⬇ {{ __('Exporter CSV') }}</button>
    </div>
  </div>

  <div class="stats-grid">
    <div class="stat-card teal">
      <div class="stat-label">{{ __('Factures ce mois') }}</div>
      <div class="stat-value">{{ $stats['count_mois'] }}</div>
      <div class="stat-desc">{{ __('Dont :count réglée(s)', ['count' => $stats['count_payees_mois']]) }}</div>
      @if($stats['delta_count'] > 0)
        <div class="stat-trend trend-up">▲ +{{ $stats['delta_count'] }} {{ __('vs mois dernier') }}</div>
      @elseif($stats['delta_count'] < 0)
        <div class="stat-trend" style="color:var(--red)">▼ {{ $stats['delta_count'] }} {{ __('vs mois dernier') }}</div>
      @else
        <div class="stat-desc">{{ __('Stable vs mois dernier') }}</div>
      @endif
    </div>
    <div class="stat-card green">
      <div class="stat-label">{{ __('Montant encaissé') }}</div>
      <div class="stat-sub">FCFA</div>
      <div class="stat-value" style="font-size:1.25rem">{{ number_format($stats['encaisse'], 0, ',', ' ') }}</div>
    </div>
    <div class="stat-card red">
      <div class="stat-label">{{ __('En attente') }}</div>
      <div class="stat-sub">FCFA</div>
      <div class="stat-value" style="font-size:1.25rem">{{ number_format($stats['en_attente'], 0, ',', ' ') }}</div>
      <div class="stat-desc">{{ $stats['impayees_count'] }} {{ __('facture(s) impayée(s)') }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-label">{{ __('Taux recouvrement') }}</div>
      <div class="stat-value">{{ $stats['taux_recouvrement'] }}%</div>
    </div>
  </div>

  <!-- FILTER BAR -->
  <div class="card" style="margin-bottom:18px;padding:13px 16px;">
    <div class="filter-bar">
      <input type="text" id="srch" placeholder="🔍 {{ __('Rechercher N°, client, prestation…') }}" oninput="filterTable()">
      <select id="fStatus" onchange="filterTable()">
        <option value="">{{ __('Tous les statuts') }}</option>
        <option value="paid">{{ __('Payée') }}</option>
        <option value="pending">{{ __('En attente') }}</option>
        <option value="overdue">{{ __('En retard') }}</option>
        <option value="partial">{{ __('Partiel') }}</option>
        <option value="draft">{{ __('Brouillon') }}</option>
      </select>
      <select id="fHotel" onchange="filterTable()">
        <option value="">{{ __('Tous les clients') }}</option>
        @foreach($clients as $c)
          <option>{{ $c }}</option>
        @endforeach
      </select>
    </div>
  </div>

  <!-- TABLE -->
  <div class="table-card">
    <div class="table-header">
      <div class="table-header-title"><span class="card-dot teal"></span>{{ __('Factures') }} <span id="cnt" style="background:var(--teal-dim);color:var(--teal);font-size:.63rem;padding:2px 8px;border-radius:10px;margin-left:4px;"></span></div>
    </div>
    <div style="overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>{{ __('N° Facture') }}</th>
            <th>{{ __('Client / Hôtel') }}</th>
            <th>{{ __('Prestation') }}</th>
            <th>{{ __('Émission') }}</th>
            <th>{{ __('Échéance') }}</th>
            <th>{{ __('Montant (FCFA)') }}</th>
            <th>{{ __('Payé (FCFA)') }}</th>
            <th>{{ __('Statut') }}</th>
            <th>{{ __('Actions') }}</th>
          </tr>
        </thead>
        <tbody id="tbody"></tbody>
      </table>
      <div id="noRes" style="display:none;padding:28px;text-align:center;color:var(--muted);font-size:.82rem;">{{ __('Aucun résultat.') }}</div>
    </div>
  </div>

  <!-- BOTTOM -->
  @php
  $modeConfig = [
    'Espèces'          => ['bg'=>'#22c55e20','color'=>'var(--green)','abbr'=>'CASH','bar'=>'var(--green)'],
    'Carte bancaire'   => ['bg'=>'#3b82f620','color'=>'var(--blue)','abbr'=>'CARD','bar'=>'var(--blue)'],
    'Mobile Money'     => ['bg'=>'#eab30820','color'=>'var(--gold-light)','abbr'=>'MOM','bar'=>'var(--gold-light)'],
    'Virement bancaire'=> ['bg'=>'#14b8a620','color'=>'var(--teal)','abbr'=>'VIR','bar'=>'var(--teal)'],
  ];
  @endphp
  <div class="two-col">
    <div class="card">
      <div class="card-title"><span class="card-dot teal"></span>{{ __('Modes de paiement reçus') }}</div>
      @forelse($paymentModes as $pm)
        @php $cfg = $modeConfig[$pm['mode']] ?? ['bg'=>'rgba(148,163,184,.1)','color'=>'var(--muted)','abbr'=>strtoupper(substr($pm['mode'],0,4)),'bar'=>'var(--muted)']; @endphp
        <div class="pm-item">
          <div class="pm-name">
            <div class="pm-icon" style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};">{{ $cfg['abbr'] }}</div>
            {{ $pm['mode'] }}
          </div>
          <div>
            <div class="pm-amount">{{ number_format($pm['total'], 0, ',', ' ') }}<span class="pm-pct"> FCFA · {{ $pm['pct'] }}%</span></div>
            <div class="progress-bar-wrap"><div class="progress-bar" style="width:{{ $pm['pct'] }}%;background:{{ $cfg['bar'] }};"></div></div>
          </div>
        </div>
      @empty
        <p style="color:var(--muted);font-size:.82rem;text-align:center;padding:1.5rem 0;">{{ __('Aucun paiement enregistré.') }}</p>
      @endforelse
    </div>
    <div class="card">
      <div class="card-title"><span class="card-dot teal"></span>{{ __('Activité récente') }}</div>
      @forelse($recentActivity as $act)
        @php
          $dotColor = match($act['statut']) {
            'paid'    => 'var(--green)',
            'pending' => 'var(--gold-light)',
            'overdue' => 'var(--red)',
            'partial' => '#fb923c',
            default   => 'var(--teal)',
          };
          $label = match($act['statut']) {
            'paid'    => __('Paiement reçu'),
            'pending' => __('Facture émise'),
            'overdue' => __('Relance requise'),
            'partial' => __('Paiement partiel'),
            default   => __('Facture créée'),
          };
        @endphp
        <div class="activity-item">
          <div class="activity-dot" style="background:{{ $dotColor }};"></div>
          <div>
            <div class="activity-text">{{ $label }} — {{ $act['client'] }} ({{ $act['numero'] }})</div>
            <div class="activity-time">{{ $act['updated_at']->diffForHumans() }} · {{ number_format($act['montant'], 0, ',', ' ') }} FCFA</div>
          </div>
        </div>
      @empty
        <p style="color:var(--muted);font-size:.82rem;text-align:center;padding:1.5rem 0;">{{ __('Aucune activité récente.') }}</p>
      @endforelse
    </div>
  </div>
</div>


<!-- ═══ MODAL : NOUVELLE FACTURE ═══ -->
<div class="modal-overlay" id="modalNew">
  <div class="modal" style="max-width:820px;">
    <div class="modal-header no-print">
      <div class="modal-title" id="modalNewTitle">✦ {{ __('Créer une nouvelle facture') }}</div>
      <button class="modal-close" onclick="closeModal('modalNew')">✕</button>
    </div>
    <div class="modal-body">
      <div class="form-grid">
        <div class="form-group">
          <label>{{ __('Hôtel *') }}</label>
          @if($hotels->isEmpty())
          <p style="font-size:.8rem;color:var(--muted);">{{ __("Ajoutez d'abord un hôtel pour pouvoir facturer.") }}</p>
          @else
          <select id="fc-hotel" required>
            @foreach($hotels as $h)
              <option value="{{ $h->id }}">{{ $h->name }}</option>
            @endforeach
          </select>
          @endif
        </div>
        <div class="form-group">
          <label>{{ __('Client') }} *</label>
          <input list="fc-clients-list" id="fc-client" placeholder="{{ __('Nom du client…') }}">
          <datalist id="fc-clients-list">
            @foreach($clients as $c)
              <option value="{{ $c }}">
            @endforeach
          </datalist>
        </div>
        <div class="form-group">
          <label>{{ __('Mode de paiement') }}</label>
          <select id="fc-pay">
            <option>{{ __('Espèces') }}</option>
            <option>{{ __('Carte bancaire') }}</option>
            <option>{{ __('Mobile Money') }}</option>
            <option>{{ __('Virement bancaire') }}</option>
          </select>
        </div>
        <div class="form-group">
          <label>{{ __('Statut') }}</label>
          <select id="fc-status">
            <option value="draft">{{ __('Brouillon') }}</option>
            <option value="pending" selected>{{ __('En attente') }}</option>
            <option value="paid">{{ __('Payée') }}</option>
            <option value="partial">{{ __('Paiement partiel') }}</option>
          </select>
        </div>
        <div class="form-group">
          <label>{{ __("Date d'émission *") }}</label>
          <input type="date" id="fc-issue">
        </div>
        <div class="form-group">
          <label>{{ __("Date d'échéance *") }}</label>
          <input type="date" id="fc-due">
        </div>
        <div class="form-group">
          <label>{{ __('Acompte reçu (FCFA)') }}</label>
          <input type="number" id="fc-paid" placeholder="0" min="0">
        </div>
        <div class="form-group span3">
          <label>{{ __('Notes / Conditions') }}</label>
          <textarea id="fc-notes" placeholder="{{ __('Conditions de paiement, remarques…') }}"></textarea>
        </div>
      </div>

      <div style="margin-top:20px;">
        <div style="font-size:.7rem;text-transform:uppercase;letter-spacing:1px;color:var(--muted);margin-bottom:10px;display:flex;align-items:center;gap:6px;"><span class="card-dot teal"></span>{{ __('Lignes de prestation') }}</div>
        <div class="line-item-row header-row" style="padding-bottom:4px;">
          <span>{{ __('Description') }}</span><span>{{ __('Qté') }}</span><span>{{ __('Prix unit. (FCFA)') }}</span><span>{{ __('Total HT') }}</span><span></span>
        </div>
        <div id="lineItems"></div>
        <button class="add-line-btn" onclick="addLine()">+ {{ __('Ajouter une ligne') }}</button>
        <div class="invoice-totals">
          <div class="total-row"><span>{{ __('Sous-total HT') }}</span><span id="t-sub">0 FCFA</span></div>
          <div class="total-row"><span>{{ __('TVA (18%)') }}</span><span id="t-tva">0 FCFA</span></div>
          <div class="total-row"><span style="color:var(--muted)">{{ __('Acompte reçu') }}</span><span id="t-acompte" style="color:var(--green)">- 0 FCFA</span></div>
          <div class="total-row grand"><span>{{ __('SOLDE DÛ TTC') }}</span><span id="t-grand">0 FCFA</span></div>
        </div>
      </div>
    </div>
    <div class="modal-footer no-print">
      <button class="btn btn-secondary" onclick="closeModal('modalNew')">{{ __('Annuler') }}</button>
      <button class="btn btn-gold" id="btnSaveDraft" onclick="saveInvoice(true)">{{ __('Brouillon') }}</button>
      <button class="btn btn-primary" id="btnSaveInvoice" onclick="saveInvoice(false)">{{ __('Créer et enregistrer') }}</button>
    </div>
  </div>
</div>

<!-- ═══ MODAL : VOIR / IMPRIMER FACTURE ═══ -->
<div class="modal-overlay" id="modalView">
  <div class="modal" style="max-width:920px;">
    <div class="modal-header no-print">
      <div class="modal-title">{{ __('Aperçu de la facture') }}</div>
      <div style="display:flex;gap:8px;align-items:center;">
        <button class="btn btn-teal no-print" onclick="downloadPDF()">⬇ {{ __('PDF') }}</button>
        <button class="btn btn-print no-print" onclick="doPrint()">🖨️ {{ __('Imprimer') }}</button>
        <button class="modal-close no-print" onclick="closeModal('modalView')">✕</button>
      </div>
    </div>
    <div class="modal-body" id="viewBody" style="padding:16px;"></div>
  </div>
</div>

<!-- ═══ MODAL : SUPPRIMER ═══ -->
<div class="modal-overlay" id="modalDel">
  <div class="modal" style="max-width:400px;">
    <div class="modal-header no-print">
      <div class="modal-title" style="color:var(--red);">⚠ {{ __('Supprimer la facture') }}</div>
      <button class="modal-close" onclick="closeModal('modalDel')">✕</button>
    </div>
    <div class="modal-body">
      <p style="font-size:.83rem;color:var(--muted2);line-height:1.7;">{{ __('Voulez-vous supprimer la facture') }} <strong id="delId" style="color:var(--text);font-family:monospace;"></strong> {{ __('? Cette action est irréversible.') }}</p>
    </div>
    <div class="modal-footer no-print">
      <button class="btn btn-secondary" onclick="closeModal('modalDel')">{{ __('Annuler') }}</button>
      <button class="btn btn-danger" onclick="confirmDel()">{{ __('Supprimer définitivement') }}</button>
    </div>
  </div>
</div>

<div class="modal-overlay" id="modalPay">
  <div class="modal" style="max-width:400px;">
    <div class="modal-header no-print">
      <div class="modal-title" style="color:var(--green);">💰 {{ __('Enregistrer un paiement') }}</div>
      <button class="modal-close" onclick="closeModal('modalPay')">✕</button>
    </div>
    <div class="modal-body">
      <p style="font-size:.83rem;color:var(--muted2);margin-bottom:12px;">{{ __('Facture') }} <strong id="payId" style="color:var(--text);font-family:monospace;"></strong> — {{ __('solde dû :') }} <strong id="paySolde" style="color:var(--text);"></strong></p>
      <div class="form-group">
        <label>{{ __('Montant payé (FCFA) *') }}</label>
        <input type="number" id="pay-amount" min="0" placeholder="0">
      </div>
      <div class="form-group">
        <label>{{ __('Mode de paiement') }}</label>
        <select id="pay-mode">
          <option>{{ __('Espèces') }}</option>
          <option>{{ __('Carte bancaire') }}</option>
          <option>{{ __('Mobile Money') }}</option>
          <option>{{ __('Virement bancaire') }}</option>
        </select>
      </div>
    </div>
    <div class="modal-footer no-print">
      <button class="btn btn-secondary" onclick="closeModal('modalPay')">{{ __('Annuler') }}</button>
      <button class="btn btn-primary" onclick="submitPayment()">{{ __('Enregistrer le paiement') }}</button>
    </div>
  </div>
</div>

<!-- TOAST -->
<div id="toast"></div>

@php
$moisAbregesJs = [__('jan.'), __('fév.'), __('mar.'), __('avr.'), __('mai'), __('jun.'), __('jul.'), __('aoû.'), __('sep.'), __('oct.'), __('nov.'), __('déc.')];
@endphp
<script>
/* ── DATA (chargé depuis la base de données) ── */
let invoices = @json($facturesJs);
let delTarget=null;
let formLines=[{d:'',q:1,p:0}];
let editingDbId=null;
let payTarget=null;

/* ── I18N (JS runtime strings) ── */
const I18N = {
  voir: @json(__('Voir')),
  imprimer: @json(__('Imprimer')),
  relancer: @json(__('Relancer')),
  supprimer: @json(__('Supprimer')),
  modifier: @json(__('Modifier')),
  marquerPayee: @json(__('Enregistrer un paiement')),
  modifierFacture: @json(__('Modifier la facture')),
  creerFacture: @json(__('Créer une nouvelle facture')),
  enregistrerModifs: @json(__('Enregistrer les modifications')),
  creerEtEnregistrer: @json(__('Créer et enregistrer')),
  factureModifiee: @json(__('mise à jour')),
  montantPayeRequis: @json(__('Montant payé requis')),
  paiementEnregistre: @json(__('Paiement enregistré')),
  descriptionPrestation: @json(__('Description de la prestation…')),
  selectionnezClient: @json(__('Sélectionnez ou saisissez un client')),
  selectionnezHotel: @json(__('Sélectionnez un hôtel')),
  ajoutezLigne: @json(__('Ajoutez au moins une ligne de prestation')),
  prestationHoteliere: @json(__('Prestation hôtelière')),
  sauvegardeeBrouillon: @json(__('sauvegardée en brouillon')),
  creee: @json(__('créée')),
  erreurInconnue: @json(__('Erreur inconnue')),
  erreurReseauReessayez: @json(__('Erreur réseau — réessayez')),
  factureReglee: @json(__('FACTURE RÉGLÉE – Paiement intégral reçu')),
  enAttentePaiement: @json(__('EN ATTENTE DE PAIEMENT')),
  factureEnRetard: @json(__('FACTURE EN RETARD – Veuillez régulariser immédiatement')),
  paiementPartielSolde: @json(__('PAIEMENT PARTIEL – Solde restant dû')),
  brouillonNonEmise: @json(__('BROUILLON – Non émise officiellement')),
  wmPayee: @json(__('PAYÉE')),
  wmEnRetard: @json(__('EN RETARD')),
  wmBrouillon: @json(__('BROUILLON')),
  emission: @json(__('Émission :')),
  echeance: @json(__('Échéance :')),
  reglement: @json(__('Règlement :')),
  facture: @json(__('Facture')),
  emetteurPrestataire: @json(__('Émetteur (Prestataire)')),
  clientDestinataire: @json(__('Client / Destinataire')),
  refClient: @json(__('Réf. client :')),
  prestationLabel: @json(__('Prestation :')),
  description: @json(__('Description')),
  qte: @json(__('Qté')),
  prixUnit: @json(__('Prix unit. (FCFA)')),
  totalHtFcfa: @json(__('Total HT (FCFA)')),
  sousTotalHt: @json(__('Sous-total HT')),
  tva: @json(__('TVA (18%)')),
  totalTtc: @json(__('Total TTC')),
  acompteRecu: @json(__('Acompte reçu')),
  soldeDu: @json(__('SOLDE DÛ')),
  conditionsPaiement: @json(__('Conditions de paiement')),
  conditionsPaiementDefaut: @json(__("Paiement à 7 jours nets. Pénalités de retard : 1,5% par mois. Tout règlement au nom de Hôtelio.")),
  signatureCachet: @json(__('Signature & Cachet')),
  coordonneesBancaires: @json(__('Coordonnées bancaires')),
  merciConfiance: @json(__('Merci pour votre confiance')),
  merciConfianceTexte: @json(__("Hôtelio vous accompagne dans l'excellence de la gestion hôtelière en Côte d'Ivoire.")),
  genereLe: @json(__('Généré le')),
  factureHotelioTitle: @json(__('Facture – Hôtelio')),
  impressionPdf: @json(__('Impression PDF via navigateur…')),
  erreurSuppression: @json(__('Erreur lors de la suppression')),
  erreurReseau: @json(__('Erreur réseau')),
  relanceEnvoyee: @json(__('Relance envoyée pour :id')),
  exportCsvTelecharge: @json(__('Export CSV téléchargé')),
  factureSupprimee: @json(__('Facture supprimée')),
  colNumero: @json(__('N° Facture')),
  colClient: @json(__('Client')),
  colPrestation: @json(__('Prestation')),
  colEmission: @json(__('Émission')),
  colEcheance: @json(__('Échéance')),
  colMontant: @json(__('Montant FCFA')),
  colPaye: @json(__('Payé FCFA')),
  colStatut: @json(__('Statut')),
  colPaiement: @json(__('Mode de paiement')),
};

/* ── NAV ── */
function showPage(id,el){
  document.querySelectorAll('.page-section').forEach(s=>s.classList.remove('active'));
  document.getElementById('page-'+id).classList.add('active');
  document.querySelectorAll('.nav-item').forEach(n=>n.classList.remove('active'));
  if(el) el.classList.add('active');
  if(id==='facturation') renderTable();
}

/* ── MODAL ── */
function openModal(id){document.getElementById(id).classList.add('open');document.body.style.overflow='hidden';}
function closeModal(id){document.getElementById(id).classList.remove('open');document.body.style.overflow='';}
document.querySelectorAll('.modal-overlay').forEach(o=>o.addEventListener('click',e=>{if(e.target===o)closeModal(o.id);}));

/* ── TOAST ── */
function toast(msg,col='var(--green)'){
  const t=document.getElementById('toast');
  t.innerHTML=`<span style="width:7px;height:7px;border-radius:50%;background:${col};flex-shrink:0;"></span>${msg}`;
  t.style.opacity='1';t.style.display='flex';t.style.alignItems='center';t.style.gap='8px';
  clearTimeout(t._timer);t._timer=setTimeout(()=>{t.style.opacity='0';},3500);
}

/* ── HELPERS ── */
const SM={paid:{l:@json(__('Payée')),c:'status-paid'},pending:{l:@json(__('En attente')),c:'status-pending'},overdue:{l:@json(__('En retard')),c:'status-overdue'},draft:{l:@json(__('Brouillon')),c:'status-draft'},partial:{l:@json(__('Partiel')),c:'status-partial'}};
function pill(s){const m=SM[s]||{l:s,c:'status-draft'};return`<span class="status-pill ${m.c}"><span class="status-dot"></span>${m.l}</span>`;}
function fmt(n){return Number(n).toLocaleString('fr-FR');}
function fmtD(d){if(!d)return'–';const[y,m,day]=d.split('-');const ms=@json($moisAbregesJs);return`${day} ${ms[+m-1]} ${y}`;}

/* ── TABLE ── */
function renderTable(data){
  const list=data||invoices;
  document.getElementById('cnt').textContent=list.length;
  const tb=document.getElementById('tbody');
  document.getElementById('noRes').style.display=list.length?'none':'block';
  tb.innerHTML=list.map(inv=>`
    <tr>
      <td><span class="invoice-id">${inv.id}</span></td>
      <td style="font-weight:500;white-space:nowrap;">${inv.client}</td>
      <td style="color:var(--muted2);max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${inv.prestation}</td>
      <td style="white-space:nowrap;">${fmtD(inv.date)}</td>
      <td style="white-space:nowrap;${inv.status==='overdue'?'color:var(--red);font-weight:600;':''}">${fmtD(inv.due)}</td>
      <td class="amount-cell">${fmt(inv.amount)}</td>
      <td class="amount-cell" style="color:${inv.paid>=inv.amount?'var(--green)':inv.paid>0?'var(--orange)':'var(--muted)'};">${fmt(inv.paid)}</td>
      <td>${pill(inv.status)}</td>
      <td style="white-space:nowrap;">
        <button class="action-link" title="${I18N.voir}" onclick="viewInv('${inv.id}')">👁</button>
        <button class="action-link gold" title="${I18N.imprimer}" onclick="printById('${inv.id}')">🖨️</button>
        <button class="action-link" title="${I18N.modifier}" onclick="openEditInvoice('${inv.id}')">✏️</button>
        ${inv.status!=='paid'?`<button class="action-link" style="color:var(--green);" title="${I18N.marquerPayee}" onclick="openPayModal('${inv.id}')">💰</button>`:''}
        ${inv.status==='pending'||inv.status==='overdue'?`<button class="action-link" title="${I18N.relancer}" onclick="remind('${inv.id}')">📧</button>`:''}
        <button class="action-link red" title="${I18N.supprimer}" onclick="askDel('${inv.id}')">🗑️</button>
      </td>
    </tr>`).join('');
}

function filterTable(){
  const q=document.getElementById('srch').value.toLowerCase();
  const st=document.getElementById('fStatus').value;
  const ht=document.getElementById('fHotel').value;
  renderTable(invoices.filter(i=>{
    return(!q||i.id.toLowerCase().includes(q)||i.client.toLowerCase().includes(q)||i.prestation.toLowerCase().includes(q))
      &&(!st||i.status===st)&&(!ht||i.client===ht);
  }));
}

/* ── FORM LINES ── */
function renderLines(){
  document.getElementById('lineItems').innerHTML=formLines.map((l,i)=>`
    <div class="line-item-row">
      <input type="text" placeholder="${I18N.descriptionPrestation}" value="${l.d}" oninput="formLines[${i}].d=this.value">
      <input type="number" value="${l.q}" min="1" oninput="formLines[${i}].q=+this.value;calcT()">
      <input type="number" value="${l.p}" min="0" placeholder="0" oninput="formLines[${i}].p=+this.value;calcT()">
      <span class="line-total">${fmt(l.q*l.p)}</span>
      <button class="del-line" onclick="rmLine(${i})">×</button>
    </div>`).join('');
  calcT();
}
function addLine(){formLines.push({d:'',q:1,p:0});renderLines();}
function rmLine(i){if(formLines.length>1){formLines.splice(i,1);renderLines();}}
function calcT(){
  const sub=formLines.reduce((s,l)=>s+l.q*l.p,0);
  const tva=Math.round(sub*.18);
  const ac=+(document.getElementById('fc-paid').value)||0;
  document.getElementById('t-sub').textContent=fmt(sub)+' FCFA';
  document.getElementById('t-tva').textContent=fmt(tva)+' FCFA';
  document.getElementById('t-acompte').textContent='- '+fmt(ac)+' FCFA';
  document.getElementById('t-grand').textContent=fmt(Math.max(0,sub+tva-ac))+' FCFA';
}
document.getElementById('fc-paid').addEventListener('input',calcT);

function initForm(){
  editingDbId=null;
  document.getElementById('modalNewTitle').textContent='✦ '+I18N.creerFacture;
  document.getElementById('btnSaveInvoice').textContent=I18N.creerEtEnregistrer;
  document.getElementById('btnSaveDraft').style.display='';
  formLines=[{d:'',q:1,p:0}];
  if(document.getElementById('fc-hotel')) document.getElementById('fc-hotel').selectedIndex=0;
  document.getElementById('fc-client').value='';
  document.getElementById('fc-pay').value='Espèces';
  document.getElementById('fc-status').value='pending';
  document.getElementById('fc-paid').value='';
  document.getElementById('fc-notes').value='';
  const now=new Date().toISOString().slice(0,10);
  const due=new Date(Date.now()+7*864e5).toISOString().slice(0,10);
  document.getElementById('fc-issue').value=now;
  document.getElementById('fc-due').value=due;
  renderLines();
}

function openEditInvoice(id){
  const inv=invoices.find(i=>i.id===id);
  if(!inv)return;
  editingDbId=inv.dbId;
  document.getElementById('modalNewTitle').textContent='✏️ '+I18N.modifierFacture;
  document.getElementById('btnSaveInvoice').textContent=I18N.enregistrerModifs;
  document.getElementById('btnSaveDraft').style.display='none';
  if(document.getElementById('fc-hotel')) document.getElementById('fc-hotel').value=inv.hotelId||'';
  document.getElementById('fc-client').value=inv.client;
  document.getElementById('fc-pay').value=inv.pay||'Espèces';
  document.getElementById('fc-status').value=inv.status==='draft'?'pending':inv.status;
  document.getElementById('fc-paid').value=inv.paid||'';
  document.getElementById('fc-notes').value=inv.notes||'';
  document.getElementById('fc-issue').value=inv.date||'';
  document.getElementById('fc-due').value=inv.due||'';
  formLines=(inv.lines&&inv.lines.length)?inv.lines.map(l=>({d:l.d,q:l.q,p:l.p})):[{d:inv.prestation||'',q:1,p:inv.amount||0}];
  renderLines();
  openModal('modalNew');
}

async function saveInvoice(draft=false){
  const hotelEl=document.getElementById('fc-hotel');
  const hotelId=hotelEl?hotelEl.value:'';
  if(!hotelId){toast(I18N.selectionnezHotel,'var(--red)');return;}
  const cl=document.getElementById('fc-client').value.trim();
  if(!cl){toast(I18N.selectionnezClient,'var(--red)');return;}
  if(!formLines.some(l=>l.d.trim())){toast(I18N.ajoutezLigne,'var(--red)');return;}

  const sub=formLines.reduce((s,l)=>s+l.q*l.p,0);
  const tva=Math.round(sub*.18);
  const total=sub+tva;
  const paid=+(document.getElementById('fc-paid').value)||0;
  const status=draft?'draft':document.getElementById('fc-status').value;

  const payload={
    hotel_id: hotelId,
    client: cl,
    prestation: formLines[0]?.d||I18N.prestationHoteliere,
    date_facture:  document.getElementById('fc-issue').value,
    date_echeance: document.getElementById('fc-due').value,
    montant:       total,
    montant_paye:  paid,
    statut:        status,
    mode_paiement: document.getElementById('fc-pay').value,
    notes:         document.getElementById('fc-notes').value,
    lignes:        formLines.map(l=>({d:l.d,q:l.q,p:l.p})),
    _token: document.querySelector('meta[name=csrf-token]').content,
  };

  const isEdit = !!editingDbId;
  const url = isEdit ? `/facturations/${editingDbId}` : '{{ route("facturations.store") }}';

  try {
    const resp = await fetch(url, {
      method: isEdit ? 'PUT' : 'POST',
      headers: {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':payload._token},
      body: JSON.stringify(payload),
    });
    const json = await resp.json();
    if(resp.ok && json.success){
      const f = json.facture;
      const mapped = {id:f.numero,dbId:f.id,hotelId:f.hotel_id,client:f.client,prestation:f.prestation,
        date:f.date_facture||'',due:f.date_echeance||'',
        amount:f.montant,paid:f.montant_paye,status:f.statut,
        pay:f.mode_paiement,notes:f.notes||'',lines:f.lignes||[]};
      if(isEdit){
        const idx=invoices.findIndex(i=>i.dbId===editingDbId);
        if(idx>-1) invoices[idx]=mapped; else invoices.unshift(mapped);
      } else {
        invoices.unshift(mapped);
      }
      closeModal('modalNew');
      renderTable();
      toast(`${I18N.facture} ${f.numero} ${isEdit?I18N.factureModifiee:(draft?I18N.sauvegardeeBrouillon:I18N.creee)} ✓`);
    } else {
      const msg = json.message || (json.errors ? Object.values(json.errors).flat().join(' · ') : I18N.erreurInconnue);
      toast(msg,'var(--red)');
    }
  } catch(e){
    toast(I18N.erreurReseauReessayez,'var(--red)');
  }
}

/* ── VIEW / PRINT ── */
function viewInv(id){
  const inv=invoices.find(i=>i.id===id);
  if(!inv)return;
  document.getElementById('viewBody').innerHTML=buildHTML(inv);
  openModal('modalView');
}

function buildHTML(inv){
  const sub=inv.lines.reduce((s,l)=>s+l.q*l.p,0);
  const tva=Math.round(sub*.18);
  const ttc=sub+tva;
  const solde=Math.max(0,ttc-inv.paid);
  const bm={paid:'banner-paid',pending:'banner-pending',overdue:'banner-overdue',partial:'banner-partial',draft:'banner-draft'};
  const bl={paid:'✓ '+I18N.factureReglee,pending:'⏳ '+I18N.enAttentePaiement,overdue:'⚠ '+I18N.factureEnRetard,partial:'◑ '+I18N.paiementPartielSolde,draft:'✎ '+I18N.brouillonNonEmise};
  const wm={paid:I18N.wmPayee,overdue:I18N.wmEnRetard,draft:I18N.wmBrouillon};
  return`<div class="invoice-preview" id="INV">
  ${wm[inv.status]?`<div class="inv-watermark">${wm[inv.status]}</div>`:''}
  <div class="inv-header">
    <div>
      <div class="inv-logo-name">Hôtelio</div>
      <div class="inv-logo-sub">Gestion Hôtelière Premium</div>
      <div class="inv-logo-addr">Plateau, Avenue Botreau Roussel<br>Abidjan, Côte d'Ivoire<br>Tél : +225 27 20 30 40 50<br>contact@hotelio-ci.com<br>RCCM : CI-ABJ-2020-B-12345 · NIF : 0012345678A</div>
    </div>
    <div class="inv-title-block">
      <div class="inv-title-word">${I18N.facture}</div>
      <div class="inv-number">${inv.id}</div>
      <div class="inv-dates">
        <div class="inv-date-row"><span class="inv-date-label">${I18N.emission}</span><span>${fmtD(inv.date)}</span></div>
        <div class="inv-date-row"><span class="inv-date-label">${I18N.echeance}</span><span>${fmtD(inv.due)}</span></div>
        <div class="inv-date-row"><span class="inv-date-label">${I18N.reglement}</span><span>${inv.pay}</span></div>
      </div>
    </div>
  </div>
  <div class="inv-status-banner ${bm[inv.status]||'banner-draft'}">${bl[inv.status]||''}</div>
  <div class="inv-body">
    <div class="inv-parties">
      <div>
        <div class="inv-party-title">${I18N.emetteurPrestataire}</div>
        <div class="inv-party-name">HÔTELIO – KAMENAN</div>
        <div class="inv-party-info">Plateau, Avenue Botreau Roussel · Abidjan, CI<br>Tél : +225 27 20 30 40 50<br>kamenan@gmail.com<br>NIF : 0012345678A</div>
      </div>
      <div>
        <div class="inv-party-title">${I18N.clientDestinataire}</div>
        <div class="inv-party-name">${inv.client}</div>
        <div class="inv-party-info">Abidjan, Côte d'Ivoire<br>${I18N.refClient} CLI-${inv.id.slice(-4)}<br>${I18N.prestationLabel} ${inv.prestation}</div>
      </div>
    </div>
    <table class="inv-table">
      <thead>
        <tr>
          <th style="width:36px;">#</th>
          <th>${I18N.description}</th>
          <th style="width:55px;">${I18N.qte}</th>
          <th style="width:130px;">${I18N.prixUnit}</th>
          <th style="width:140px;">${I18N.totalHtFcfa}</th>
        </tr>
      </thead>
      <tbody>
        ${inv.lines.map((l,i)=>`<tr><td>${i+1}</td><td>${l.d}</td><td style="text-align:center;">${l.q}</td><td style="text-align:right;">${fmt(l.p)}</td><td style="text-align:right;font-weight:600;">${fmt(l.q*l.p)}</td></tr>`).join('')}
      </tbody>
    </table>
    <div class="inv-totals-area">
      <div class="inv-totals">
        <div class="inv-total-row"><span>${I18N.sousTotalHt}</span><span>${fmt(sub)} FCFA</span></div>
        <div class="inv-total-row"><span>${I18N.tva}</span><span>${fmt(tva)} FCFA</span></div>
        <div class="inv-total-row" style="font-weight:600;"><span>${I18N.totalTtc}</span><span>${fmt(ttc)} FCFA</span></div>
        <div class="inv-total-row" style="color:${inv.paid>0?'#16a34a':'#94a3b8'};"><span>${I18N.acompteRecu}</span><span>- ${fmt(inv.paid)} FCFA</span></div>
        <div class="inv-total-row grand"><span>${I18N.soldeDu}</span><span>${fmt(solde)} FCFA</span></div>
      </div>
    </div>
  </div>
  <div class="inv-footer">
    <div class="inv-footer-grid">
      <div>
        <div class="inv-footer-title">${I18N.conditionsPaiement}</div>
        <div class="inv-footer-text">${inv.notes||I18N.conditionsPaiementDefaut}</div>
        <div class="inv-signature-line">${I18N.signatureCachet}</div>
      </div>
      <div>
        <div class="inv-footer-title">${I18N.coordonneesBancaires}</div>
        <div class="inv-footer-text">Banque : SGBCI – Abidjan Plateau<br>IBAN : CI77 0001 0100 0012 3456 7890<br>BIC/SWIFT : SGBCCIAB<br>Titulaire : HOTELIO SARL</div>
        <div style="margin-top:12px;">
          <div class="inv-footer-title">${I18N.merciConfiance}</div>
          <div class="inv-footer-text">${I18N.merciConfianceTexte}</div>
        </div>
      </div>
    </div>
    <div style="margin-top:16px;padding-top:10px;border-top:1px solid #f1f5f9;display:flex;justify-content:space-between;">
      <span style="font-size:.66rem;color:#94a3b8;">${I18N.genereLe} ${new Date().toLocaleDateString('fr-FR')} · Hôtelio © 2025</span>
      <span style="font-size:.66rem;color:#94a3b8;">Page 1/1</span>
    </div>
  </div>
</div>`;
}

function doPrint(){
  const content=document.getElementById('INV').outerHTML;
  const w=window.open('','_blank','width=1000,height=750');
  w.document.write(`<!DOCTYPE html><html lang="fr"><head><meta charset="UTF-8"><title>${I18N.factureHotelioTitle}</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
  *{box-sizing:border-box;margin:0;padding:0;}body{font-family:'DM Sans',sans-serif;background:#fff;}
  .invoice-preview{background:#fff;color:#1a1a2e;font-family:'DM Sans',sans-serif;position:relative;}
  .inv-header{background:linear-gradient(135deg,#0f2a28,#0d3d3a,#0a2e2b);padding:30px 34px;display:flex;justify-content:space-between;align-items:flex-start;}
  .inv-logo-name{font-family:'Playfair Display',serif;font-size:1.5rem;color:#e8c97a;}
  .inv-logo-sub{font-size:.68rem;color:#4ade80;letter-spacing:2px;text-transform:uppercase;margin-top:2px;}
  .inv-logo-addr{font-size:.72rem;color:rgba(255,255,255,.55);margin-top:10px;line-height:1.65;}
  .inv-title-block{text-align:right;}
  .inv-title-word{font-size:1.9rem;font-weight:700;color:#fff;letter-spacing:2px;text-transform:uppercase;}
  .inv-number{font-size:.78rem;color:#2dd4bf;margin-top:4px;}
  .inv-dates{margin-top:8px;}
  .inv-date-row{font-size:.72rem;color:rgba(255,255,255,.65);display:flex;gap:8px;justify-content:flex-end;margin-top:3px;}
  .inv-date-label{color:rgba(255,255,255,.4);}
  .inv-body{padding:26px 34px;}
  .inv-parties{display:grid;grid-template-columns:1fr 1fr;gap:22px;margin-bottom:26px;}
  .inv-party-title{font-size:.6rem;text-transform:uppercase;letter-spacing:1.5px;color:#6b7280;margin-bottom:7px;}
  .inv-party-name{font-size:.92rem;font-weight:700;color:#1a1a2e;margin-bottom:2px;}
  .inv-party-info{font-size:.73rem;color:#6b7280;line-height:1.7;}
  .inv-table{width:100%;border-collapse:collapse;margin-bottom:22px;}
  .inv-table th{background:#f1f5f9;font-size:.6rem;text-transform:uppercase;letter-spacing:1px;color:#64748b;padding:9px 13px;text-align:left;border-bottom:2px solid #e2e8f0;}
  .inv-table th:nth-child(3),.inv-table th:nth-child(4),.inv-table th:nth-child(5),.inv-table td:nth-child(3),.inv-table td:nth-child(4),.inv-table td:nth-child(5){text-align:right;}
  .inv-table td{padding:10px 13px;border-bottom:1px solid #f1f5f9;font-size:.78rem;color:#334155;}
  .inv-totals-area{display:flex;justify-content:flex-end;}
  .inv-totals{width:260px;background:#f8fafc;border-radius:8px;padding:15px;}
  .inv-total-row{display:flex;justify-content:space-between;font-size:.77rem;color:#64748b;padding:4px 0;}
  .inv-total-row.grand{font-size:.9rem;font-weight:700;color:#0d9488;border-top:2px solid #e2e8f0;margin-top:8px;padding-top:10px;}
  .inv-footer{padding:18px 34px 26px;border-top:1px solid #f1f5f9;}
  .inv-footer-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;}
  .inv-footer-title{font-size:.6rem;text-transform:uppercase;letter-spacing:1.2px;color:#94a3b8;margin-bottom:6px;}
  .inv-footer-text{font-size:.73rem;color:#64748b;line-height:1.7;}
  .inv-status-banner{margin:0 34px 18px;padding:9px 16px;border-radius:8px;font-size:.77rem;font-weight:600;text-align:center;}
  .banner-paid{background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0;}
  .banner-pending{background:#fef9c3;color:#ca8a04;border:1px solid #fef08a;}
  .banner-overdue{background:#fee2e2;color:#dc2626;border:1px solid #fecaca;}
  .banner-partial{background:#fff7ed;color:#c2410c;border:1px solid #fed7aa;}
  .banner-draft{background:#f8fafc;color:#64748b;border:1px solid #e2e8f0;}
  .inv-watermark{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-35deg);font-size:4.5rem;font-weight:900;opacity:.035;letter-spacing:4px;text-transform:uppercase;pointer-events:none;color:#0d9488;}
  .inv-signature-line{border-top:1px solid #cbd5e1;width:160px;margin-top:30px;padding-top:6px;font-size:.68rem;color:#94a3b8;text-align:center;}
  @media print{@page{margin:0;size:A4 portrait;}body{margin:0;}}
  </style></head><body>${content}<script>window.onload=()=>{window.print();setTimeout(()=>window.close(),500);}<\/script></body></html>`);
  w.document.close();
}

function downloadPDF(){toast(I18N.impressionPdf,'var(--blue)');setTimeout(doPrint,300);}
function printById(id){const inv=invoices.find(i=>i.id===id);if(!inv)return;document.getElementById('viewBody').innerHTML=buildHTML(inv);doPrint();}

/* ── DELETE ── */
function askDel(id){delTarget=id;document.getElementById('delId').textContent=id;openModal('modalDel');}
async function confirmDel(){
  if(!delTarget)return;
  const inv=invoices.find(i=>i.id===delTarget);
  if(!inv){delTarget=null;closeModal('modalDel');return;}
  const token=document.querySelector('meta[name=csrf-token]').content;
  try{
    const resp=await fetch(`/facturations/${inv.dbId}`,{
      method:'DELETE',
      headers:{'Accept':'application/json','X-CSRF-TOKEN':token},
    });
    const json=await resp.json();
    if(resp.ok&&json.success){
      invoices=invoices.filter(i=>i.id!==delTarget);
      closeModal('modalDel');renderTable();
      toast(I18N.factureSupprimee,'var(--red)');
    }else{toast(I18N.erreurSuppression,'var(--red)');}
  }catch(e){toast(I18N.erreurReseau,'var(--red)');}
  delTarget=null;
}

/* ── PAY ── */
function openPayModal(id){
  const inv=invoices.find(i=>i.id===id);
  if(!inv)return;
  payTarget=id;
  document.getElementById('payId').textContent=id;
  document.getElementById('paySolde').textContent=fmt(Math.max(0,inv.amount-inv.paid))+' FCFA';
  document.getElementById('pay-amount').value=inv.paid||'';
  document.getElementById('pay-mode').value=inv.pay||'Espèces';
  openModal('modalPay');
}
async function submitPayment(){
  if(!payTarget)return;
  const inv=invoices.find(i=>i.id===payTarget);
  if(!inv){payTarget=null;closeModal('modalPay');return;}
  const montant=document.getElementById('pay-amount').value;
  if(montant===''||isNaN(montant)||+montant<0){toast(I18N.montantPayeRequis,'var(--red)');return;}
  const token=document.querySelector('meta[name=csrf-token]').content;
  try{
    const resp=await fetch(`/facturations/${inv.dbId}/pay`,{
      method:'PATCH',
      headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':token},
      body:JSON.stringify({montant_paye:+montant,mode_paiement:document.getElementById('pay-mode').value}),
    });
    const json=await resp.json();
    if(resp.ok&&json.success){
      const idx=invoices.findIndex(i=>i.id===payTarget);
      if(idx>-1){invoices[idx].paid=json.facture.montant_paye;invoices[idx].status=json.facture.statut;invoices[idx].pay=json.facture.mode_paiement;}
      closeModal('modalPay');renderTable();
      toast(I18N.paiementEnregistre+' ✓');
    }else{
      const msg=json.message||I18N.erreurInconnue;
      toast(msg,'var(--red)');
    }
  }catch(e){toast(I18N.erreurReseau,'var(--red)');}
  payTarget=null;
}

/* ── REMIND ── */
function remind(id){toast(`${I18N.relanceEnvoyee.replace(':id', id)} 📧`,'var(--blue)');}

/* ── EXPORT ── */
function exportCSV(){
  const h=[I18N.colNumero,I18N.colClient,I18N.colPrestation,I18N.colEmission,I18N.colEcheance,I18N.colMontant,I18N.colPaye,I18N.colStatut,I18N.colPaiement];
  const rows=invoices.map(i=>[i.id,i.client,i.prestation,i.date,i.due,i.amount,i.paid,i.status,i.pay]);
  const csv=[h,...rows].map(r=>r.map(v=>`"${v}"`).join(',')).join('\n');
  const a=document.createElement('a');
  a.href='data:text/csv;charset=utf-8,\uFEFF'+encodeURIComponent(csv);
  a.download='factures_hotelio_ci.csv';a.click();
  toast(I18N.exportCsvTelecharge+' ✓','var(--teal)');
}

/* ── INIT ── */
renderTable();
document.querySelectorAll('[onclick*="modalNew"]').forEach(b=>b.addEventListener('click',()=>{initForm();}));

</script>

<!-- ═══ PAGINATION ═══ -->
<div style="padding:0 24px 16px;max-width:1200px;margin:0 auto;">
  {{ $factures->links() }}
</div>

</div>
@endsection
