<!DOCTYPE html>
<html>
<head>
    <title>Banggomart Products</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background-color: #f5f5f5; }
    </style>
</head>
<body>
<h2>Banggomart Product List</h2>
<table>
    <thead>
    <tr>
        <th>SL</th>
        <th>ID</th>
        <th>Name</th>
        <th>Price</th>
    </tr>
    </thead>
    <tbody>
    @foreach($products as $product)
        <tr>
            <td>{{ $loop->iteration ?? '' }}</td>
            <td>{{ $product['id'] ?? '' }}</td>
            <td>{{ $product['name'] ?? '' }}</td>
            <td>{{ $product['wholesale_price'] ?? '' }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
