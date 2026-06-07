<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><style>body{font-family:Arial,sans-serif;line-height:1.6;color:#333;max-width:600px;margin:0 auto;padding:20px}.header{background:linear-gradient(135deg,#f97316,#ea580c);color:#fff;padding:24px;border-radius:12px 12px 0 0;text-align:center}.header h1{margin:0;font-size:22px}.body{padding:24px;border:1px solid #e5e7eb;border-top:none;border-radius:0 0 12px 12px}.status-card{background:#f9fafb;border-radius:8px;padding:16px;margin:16px 0;text-align:center}.status-label{font-size:18px;font-weight:700;margin:4px 0}.details{background:#f9fafb;border-radius:8px;padding:16px;margin:16px 0}.details p{margin:4px 0;font-size:14px}.label{color:#6b7280;font-size:13px}.product{display:flex;align-items:center;gap:12px;padding:12px 0;border-bottom:1px solid #f3f4f6}.product img{width:56px;height:56px;object-fit:contain;border-radius:8px;background:#f9fafb;flex-shrink:0}.product-info{flex:1}.product-name{font-size:14px;font-weight:600;color:#111827;margin:0}.product-meta{font-size:12px;color:#6b7280;margin:2px 0 0}.total{text-align:right;font-size:18px;font-weight:700;color:#f97316;margin-top:12px;padding-top:12px;border-top:1px solid #e5e7eb}.tracking-link{display:inline-block;padding:12px 24px;background:#f97316;color:#fff;text-decoration:none;border-radius:8px;font-weight:600;margin:16px 0}.footer{text-align:center;padding:20px;font-size:12px;color:#9ca3af}</style></head>
<body>
<div class="header">
    <h1>Mise à jour de votre commande</h1>
    <p style="margin:4px 0 0;opacity:.9">Optique Échouate</p>
    <div class="status-label" style="color:{{ $statusColor ?? '#f97316' }}">{{ $order->status_label }}</div>
</div>
<div class="body">
<p>Bonjour <strong>{{ $order->name }}</strong>,</p>
<p>Le statut de votre commande <strong>#{{ $order->id }}</strong> a été mis à jour :</p>

<div class="status-card">
    <p style="margin:0;color:#6b7280;font-size:13px">Nouveau statut</p>
    <p class="status-label" style="color:{{ $statusColor ?? '#f97316' }}">{{ $order->status_label }}</p>
</div>

<div class="details">
<p><span class="label">Commande n°</span><br><strong>#{{ $order->id }}</strong></p>
<p><span class="label">Date de mise à jour</span><br><strong>{{ now()->format('d/m/Y H:i') }}</strong></p>
<p><span class="label">Nom</span><br><strong>{{ $order->name }}</strong></p>
<p><span class="label">Adresse</span><br><strong>{{ $order->address }}{{ $order->city ? ', ' . $order->city : '' }}</strong></p>
</div>

@php
    $items = $order->orderItems ?? collect();
@endphp
@if($items->isNotEmpty())
<h3 style="font-size:15px;margin:20px 0 8px">Articles commandés</h3>
@foreach($items as $item)
<div class="product">
    <img src="{{ $item->product?->image ?? 'https://placehold.co/56x56/e5e7eb/9ca3af?text=N/A' }}" alt="{{ $item->product?->name ?? 'Article' }}" onerror="this.style.display='none'">
    <div class="product-info">
        <p class="product-name">{{ $item->product?->name ?? 'Article #' . $item->product_id }}</p>
        <p class="product-meta">Quantité : {{ $item->quantity }} × {{ number_format($item->price, 2, ',', ' ') }} MAD</p>
    </div>
</div>
@endforeach
@endif

<div class="total">Total : {{ number_format($order->total_price, 2, ',', ' ') }} MAD</div>

<a href="{{ route('account.orders.show', $order) }}" class="tracking-link">Suivre ma commande</a>

<p style="margin-top:20px">Vous pouvez suivre l'évolution de votre commande depuis votre espace client.</p>
<p>Cordialement,<br><strong>L'équipe Optique Échouate</strong></p>
</div>
<div class="footer">&copy; {{ date('Y') }} Optique Échouate. Tous droits réservés.</div>
</body>
</html>
