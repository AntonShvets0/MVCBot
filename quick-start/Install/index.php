<!DOCTYPE html>
<html lang="ru">
<head>
    <title>MVC PHP BOT | Установка</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
<?php
    $path = isset($_GET['path']) ? $_GET['path'] : 'index';

    if (!file_exists('page/' . $path . '.html')) {
        exit('Неизвестный путь');
    }

    if (isset($_GET['error'])) {
        echo <<<HTML
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <div class="p-2 mt-2 bg-danger text-light text-center">{$_GET['error']}</div>                                  
                    </div>
                </div>
            </div>
HTML;

    }

    echo file_get_contents('page/' . $path . '.html');
?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM"
        crossorigin="anonymous"></script>
</body>
</html>