<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>{{ $mailMessage }}</p>
    
    <h2>Order Details:</h2>
    <ul>
        <li>Order ID: {{ $order->order_id }}</li>
        <li>Status: {{ $order->status }}</li>
        <li>Customer: {{ $order->user->name }}</li>
        <li>Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}</li>
    </ul>
    
    <p>This is a simple test email to verify email sending functionality.</p>
</body>
</html>