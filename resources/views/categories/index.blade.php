<!DOCTYPE html>
<html>
<head>
    <title>Smart CBT - Kategori</title>
</head>
<body>

    <h1>Daftar Kategori</h1>

    <table border="1" cellpadding="10">
        <tr>
            <th>ID</th>
            <th>Nama Kategori</th>
        </tr>

        @foreach($categories as $category)
        <tr>
            <td>{{ $category->id }}</td>
            <td>{{ $category->name }}</td>
        </tr>
        @endforeach

    </table>

</body>
</html>