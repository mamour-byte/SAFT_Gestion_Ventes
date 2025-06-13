{{-- resources/views/orchid/preview-bordereau.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <title>Aperçu Bordereau de Livraison</title>
</head>
<body>
    <h2>Bordereau de Livraison</h2>

    <iframe src="{{ route('preview-bordereau.pdf', ['id' => request()->id ?? request()->get('venteId')]) }}" width="90%" height="800px"></iframe>
</body>
</html>
