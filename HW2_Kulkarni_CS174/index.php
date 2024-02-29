<?php

echo <<<_ENDHTML
<!DOCTYPE html>
<html>
<head>
    <title>Upload File</title>
</head>
<body>
    <h2>Upload File</h2>
    <form action="data_processor.php" method="post" enctype="multipart/form-data">
        Select a .txt file:
        <input type="file" name="numberFile" id="numberFile">
        <input type="submit" value="Upload File" name="submit">
    </form>
</body>
</html>
_ENDHTML;

