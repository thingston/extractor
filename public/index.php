<?php
require __DIR__ . '/vendor/autoload.php';

$url = $_GET['url'] ?? null;
$html = null;
$error = null;
$page = null;

if (null !== $url && false !== parse_url($url)) {
    $html = file_get_contents($url);
    if (false === $html) {
        $error = 'Unable to load the page from Internet.';
    } else {
        $html = trim($html);
        $page = \Thingston\Extractor\Page\PageExtractor::create($html, $url)->extract();
    }
}
?><!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">

        <title>Thingston Extractor</title>
    </head>
    <body>
        <div class="container">
            <h1>Thingston Extractor</h1>
            <form class="form-inline">
                <input type="text" name="url" value="<?= $url ?>" placeholder="E.g http://example.org" class="form-control">
                <button type="submit" class="btn btn-primary">Extract</button>
                <a href="/" class="btn btn-outline-primary">Reset</a>
            </form>
            <?php if ($html) : ?>
                <h2>Results of Extraction</h2>
                <p class="lead"><strong>URL:</strong> <a href="<?= $url ?>" target="_blank"><?= $url ?></a></p>
                <div class="card card-body">
                    <pre>
                        <?= htmlentities($html) ?>
                    </pre>
                </div>
            <?php dump($page) ?>
            <?php endif ?>
        </div>

        <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
    </body>
</html>