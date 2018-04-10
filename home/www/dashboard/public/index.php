<?php
$domain = $_ENV['OPS_DOMAIN'];
$sitesDir = $_ENV['OPS_SITES_DIR'];

$mariadb = new PDO('mysql:host=mariadb', 'root', null);
$postgres = new PDO('pgsql:host=postgres;user=postgres');

$mariadbDatabases = $mariadb->query('SHOW DATABASES');
$postgresDatabases = $postgres->query('SELECT datname AS name FROM pg_database WHERE datistemplate = false');
?>
<!doctype html>
<html lang="">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="x-ua-compatible" content="ie=edge">
        <title>Ops Dashboard</title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
        <link rel="stylesheet" href="/dashboard.css">
    </head>
    <body>
        <div class="navbar">
            <div class="container">
                <h1>Ops Dashboard</h1>
                <a href="https://gitlab.imarc.net/imarc/ops/blob/master/README.md">Documentation</a>
            </div>
        </div>

        <main class="container">
            <div class="row">
                <div class="col-md">
                    <h2>Projects</h2>

                    <ul>
                    <?php
                    $sites = [];
                    foreach(glob('/var/www/html/*', GLOB_ONLYDIR) as $dir) {
                        $site = str_replace('/var/www/html/', '', $dir);
                        if (!preg_match('/^[a-z][a-z0-9-]*$/', $site)) {
                            continue;
                        }

                        //chdir($dir);
                        //exec("git status -bs 2> /dev/null", $output);

                        //var_dump($output);

                        //var_dump($output);

                        echo "<li><a class=\"site\" href=\"https://{$site}.{$domain}\">{$site} {$output[0]}</a></li>";
                    }
                    ?>
                    </ul>

                    <p class="note"><em>Only valid site directories within <strong><?= $sitesDir ?></strong> will show. Site directories must only contain letters, numbers, and dashes.</em></p>
                </div>

                <div class="col-md databases">
                    <h2>Databases</h2>

                    <header>
                        <h3>MariaDB</h3>
                        <small>
                            /
                            <a href="https://adminer.ops.<?= $domain ?>/?server=mariadb&username=root&database=">create db</a>
                        </small>
                    </header>

                    <ul>
                        <?php
                        $count = 0;
                        foreach ($mariadbDatabases as $db) {
                            if (in_array($db[0], ['mysql', 'information_schema', 'performance_schema'])) {
                                continue;
                            }
                            ?>

                            <li>
                                <?php
                                $link = "https://adminer.ops.${domain}/?server=mariadb&username=root&db=" . $db[0];
                                echo sprintf('<a href="%s">%s</a>', $link, $db[0]);

                                $sqlLink = "https://adminer.ops.${domain}/?server=mariadb&username=root&sql=&db=" . $db[0];
                                echo sprintf('<small> / <a href="%s">query</a></li></small>', $sqlLink);
                                ?>
                            </li>

                            <?php
                            $count++;
                        }

                        if ($count === 0) {
                            echo '<li><em>None</em></li>';
                        }
                        ?>
                    </ul>

                    <header>
                        <h3>Postgres</h3>
                        <small>
                            /
                            <a href="https://adminer.ops.<?= $domain ?>/?pgsql=postgres&username=postgres&database=">create db</a>
                        </small>
                    </header>

                    <ul>
                        <?php
                        $count = 0;
                        foreach ($postgresDatabases as $db) {
                            if (in_array($db['name'], ['postgres'])) {
                                continue;
                            }
                            ?>

                            <li>
                                <?php
                                $link = "https://adminer.ops.${domain}/?pgsql=postgres&username=postgres&ns=public&db=" . $db['name'];;
                                echo sprintf('<a href="%s">%s</a>', $link, $db['name']);

                                $sqlLink = "https://adminer.ops.${domain}/?pgsql=postgres&username=postgres&ns=public&sql=&db=" . $db['name'];;
                                echo sprintf('<small> / <a href="%s">query</a></li></small>', $sqlLink);
                                ?>
                            </li>

                            <?php
                            $count++;

                        }

                        if ($count === 0) {
                            echo '<li><em>None</em></li>';
                        }
                        ?>

                    </ul>
                </div>
                <div class="col-md">

                    <h2>Tools</h2>

                    <ul>
                        <li>
                            <a href="https://adminer.ops.<?= $domain ?>">Adminer</a>
                            <ul>
                                <li><a href="https://adminer.ops.<?= $domain ?>/?server=mariadb&amp;username=root">MariaDB</a></li>
                                <li><a href="https://adminer.ops.<?= $domain ?>/?pgsql=postgres&amp;username=postgres">PostgreSQL</a></li>
                            </ul>
                        </li>
                        <li>
                            <a href="https://redis-commander.ops.<?= $domain ?>">Redis Commander</a>
                        </li>
                        <li>
                            <a href="https://minio.ops.<?= $domain ?>">Minio</a>
                        </li>
                        <li>
                            <a href="https://mailhog.ops.<?= $domain ?>">Mailhog</a>
                        </li>
                        <li>
                            <a href="https://ops.<?= $domain ?>:8080/dashboard/#/health">Traefik</a>
                        </li>
                        <li>
                            <a href="https://portainer.ops.<?= $domain ?>">Portainer</a>
                        </li>

                        <li><a href="/phpinfo.php">PHP Info</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    </body>
</html>
