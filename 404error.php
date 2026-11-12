<?php
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 not found</title>
    <link rel="stylesheet" href="style.css?v=3">
</head>
<body class="page themelight">

    <div class="notfound">sorry your url seems invalid :(</div>

<script>
var savedtheme = localStorage.getItem("theme");
if (savedtheme) {
    document.body.className = "page " + savedtheme;
}
</script>

</body>
</html>
