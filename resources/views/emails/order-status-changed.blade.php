<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><style>body{font-family:Arial,sans-serif;line-height:1.6;color:#333;max-width:600px;margin:0 auto;padding:20px}.header{background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;padding:24px;border-radius:12px 12px 0 0;text-align:center}.header h1{margin:0;font-size:22px}.header .status-badge{display:inline-block;padding:6px 16px;border-radius:20px;font-size:13px;font-weight:600;margin-top:8px}.body{padding:24px;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 12px 12px}.details{background:#f9fafb;border-radius:8px;padding:16px;margin:16px 0}.details p{margin:4px 0;font-size:14px}.label{color:#6b7280;font-size:13px}.table{width:100%;border-collapse:collapse;margin:16px 0}.table th,.table td{padding:8px 12px;text-align:left;border-bottom:1px solid #e5e7eb;font-size:14px}.table th{background:#f9fafb;color:#6b7280;font-weight:600}.total{text-align:right;font-size:18px;font-weight:700;color:#f97316;margin-top:8px}.footer{text-align:center;padding:20px;font-size:12px;color:#9ca3af}</style></head>
<body>
<div class="header">
<h1>Mise à jour de votre commande</h1>
<p style="margin:4px 0 0;opacity:.9">Optique Échouate</p>
<div class="status-badge" style="background:{{ $statusColor ?? '#f97316' }}20;color:{{ $statusColor ?? '#f97316' }};border:1px solid {{ $statusColor ?? '#f97316' }}40">{{ $order->status_label }}</div>
</div>
<div class="body">
<p>Bonjour <strong>{{ $order->name }}</strong>,</p>
<p>Le statut de votre commande <strong>#{{ $order->id }}</strong> a été mis à jour :</p>

<div class="details">
<p><span class="label">Nouveau statut</span><br><strong>{{ $order->status_label }}</strong></p>
<p><span class="label">Commande n°</span><br><strong>#{{ $order->id }}</strong></p>
<p><span class="label">Date de mise à jour</span><br><strong>{{ now()->format('d/m/Y H:i') }}</strong></p>
</div>

@if(is_array($order->items) && count($order->items) > 0)
<table class="table">
<thead><tr><th>Article</th><th>Qté</th><th>Prix</th></tr></thead>
<tbody>
@foreach($order->items as $item)
<tr>
<td>{{ $item['name'] ?? 'Article' }}</td>
<td>{{ $item['quantity'] ?? 1 }}</td>
<td>{{ number_format($item['price'] ?? 0, 2, ',', ' ') }} MAD</td>
</tr>
@endforeach
</tbody>
</table>
@endif

<div class="total">Total : {{ number_format($order->total_price, 2, ',', ' ') }} MAD</div>

<p style="margin-top:20px">Vous pouvez suivre l'évolution de votre commande depuis votre espace client.</p>
<p>Cordialement,<br><strong>L'équipe Optique Échouate</strong></p>
</div>
<div class="footer">&copy; {{ date('Y') }} Optique Échouate. Tous droits réservés.</div>
</body>
</html>