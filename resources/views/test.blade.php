<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
     @vite(['resources/js/app.js','resources/css/autocomplete.css'])
</head>
<body>
    <x-autocomplete
        name="fruit"
        id="fruit"
        placeholder="Search fruit..."
    />

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            window.loadAutoComplete('fruit',['apple','orange','grapes']);
        });
    </script>
</body>
</html>
