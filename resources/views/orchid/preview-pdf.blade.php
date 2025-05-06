{{-- resources/views/orchid/preview-pdf.blade.php --}}

<!DOCTYPE html>
<html>
<head>
    <title>Aperçu du document PDF</title>
</head>
<body>
    <h2>Aperçu du document PDF</h2>

    <iframe src="{{ route('preview-pdf.pdf', ['id' => request()->id ?? request()->get('venteId')]) }}" width="100%" height="800px"></iframe>
</body>
</html>
