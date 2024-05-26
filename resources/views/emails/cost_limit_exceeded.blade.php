<!DOCTYPE html>
<html>
<head>
    <title>Cost Limit Exceeded</title>
</head>
<body>
<h1>Cost Limit Exceeded</h1>
<p>Dear {{ $cost->user->name }},</p>
<p>You have exceeded the {{ ucfirst($period) }} limit for {{ $cost->costType->name }}.</p>
<p>Exceeded Amount: {{ $exceededAmount }}</p>
<p>Date: {{ $cost->date }}</p>
<p>Description: {{ $cost->desc }}</p>
<p>Price: {{ $cost->price }}</p>
</body>
</html>
