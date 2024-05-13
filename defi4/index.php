<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste dans un tableau</title>
</head>
<body>
    <table border="1">
        <thead>
            <tr>
                <th>#</th>
                <th>Nom</th>
            </tr>
        </thead>
        <tbody>
            <?php
                // Déclaration d'un tableau multidimensionnel
                $tab = array(
                array ('Mickaël Andrieu', 'mickael.andrieu@exemple.com',34),
                array ('Mathieu Nebra', 'mathieu.nebra@exemple.com', 34),
                array ('Laurène Castor','laurene.castor@exemple.com', 28)
                );

                /*
                $tab = array(
                    array(1, 2, 3),
                    array(4, 5, 6),
                    array(7, 8, 9)
                );
                */

                // Affichage du tableau
                echo "<table border='1'>";
                foreach ($tab as $row) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>$value</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            ?>
        </tbody>
    </table>
</body>
</html>
