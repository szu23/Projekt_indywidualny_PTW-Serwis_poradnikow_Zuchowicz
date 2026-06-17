<?php
require_once "sesja.php";

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$id_uzytkownika = (int)$_SESSION["id"];

$stmt = $polaczenie->prepare("SELECT id_posta, tytul, tresc, kategoria, data_utworzenia, poziom_trudnosci, czas_wykonania FROM posty WHERE id_uzytkownika = ? ORDER BY data_utworzenia DESC, id_posta DESC");
$stmt->bind_param("i", $id_uzytkownika);
$stmt->execute();
$result = $stmt->get_result();

$stmt2 = $polaczenie->prepare("SELECT komentarze.id_komentarza, komentarze.id_posta, komentarze.tresc_komentarza, posty.tytul AS tytul_posta FROM komentarze JOIN posty ON komentarze.id_posta = posty.id_posta WHERE komentarze.id_uzytkownika = ? ORDER BY komentarze.id_komentarza DESC");
$stmt2->bind_param("i", $id_uzytkownika);
$stmt2->execute();
$result2 = $stmt2->get_result();

$stmt3 = $polaczenie->prepare("SELECT ukonczone_poradniki.data_ukonczenia, posty.id_posta, posty.tytul, posty.kategoria FROM ukonczone_poradniki JOIN posty ON ukonczone_poradniki.id_posta = posty.id_posta WHERE ukonczone_poradniki.id_uzytkownika = ? ORDER BY ukonczone_poradniki.data_ukonczenia DESC");
$stmt3->bind_param("i", $id_uzytkownika);
$stmt3->execute();
$result3 = $stmt3->get_result();
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel użytkownika</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>
    <div class="settings-container">
        <h1>Cześć, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Witaj w panelu użytkownika.</h1>
        <p>
            <a href="reset-haslo.php" class="btn btn-warning">Zresetuj hasło</a>
            <a href="welcome.php" class="btn btn-primary">Wróć na stronę</a>
        </p>

        <h3>Twoje poradniki:</h3>
        <div class="table-wrapper">
            <table class="posts-table">
                <tr>
                    <th>Tytuł</th>
                    <th>Opis</th>
                    <th>Kategoria</th>
                    <th>Trudność</th>
                    <th>Czas</th>
                    <th>Data</th>
                    <th>Akcje</th>
                </tr>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['tytul']); ?></td>
                            <td><?php echo htmlspecialchars($row['tresc']); ?></td>
                            <td><?php echo htmlspecialchars($row['kategoria']); ?></td>
                            <td><?php echo htmlspecialchars($row['poziom_trudnosci']); ?></td>
                            <td><?php echo htmlspecialchars($row['czas_wykonania']); ?></td>
                            <td><?php echo htmlspecialchars($row['data_utworzenia']); ?></td>
                            <td>
                                <a href="edytowaniepost.php?id=<?php echo (int)$row['id_posta']; ?>" class="btn btn-warning btn-sm">Edytuj</a>
                                <a href="usuwaniepost.php?id=<?php echo (int)$row['id_posta']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Na pewno chcesz usunąć ten poradnik?')">Usuń</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7"><div class="alert alert-info">Nie masz jeszcze żadnych poradników.</div></td></tr>
                <?php endif; ?>
            </table>
        </div>

        <h3>Twoje ukończone poradniki:</h3>
        <div class="table-wrapper">
            <table class="posts-table">
                <tr>
                    <th>Tytuł</th>
                    <th>Kategoria</th>
                    <th>Data ukończenia</th>
                    <th>Akcje</th>
                </tr>
                <?php if ($result3->num_rows > 0): ?>
                    <?php while ($ukonczony = $result3->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($ukonczony['tytul']); ?></td>
                            <td><?php echo htmlspecialchars($ukonczony['kategoria']); ?></td>
                            <td><?php echo htmlspecialchars($ukonczony['data_ukonczenia']); ?></td>
                            <td><a href="post.php?id=<?php echo (int)$ukonczony['id_posta']; ?>" class="btn btn-primary btn-sm">Otwórz</a></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4"><div class="alert alert-info">Nie masz jeszcze ukończonych poradników.</div></td></tr>
                <?php endif; ?>
            </table>
        </div>

        <h3>Twoje komentarze:</h3>
        <div class="table-wrapper">
            <table class="posts-table">
                <tr>
                    <th>Poradnik</th>
                    <th>Treść komentarza</th>
                    <th>Akcje</th>
                </tr>
                <?php if ($result2->num_rows > 0): ?>
                    <?php while ($komentarz = $result2->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <a href="post.php?id=<?php echo (int)$komentarz['id_posta']; ?>">
                                    <?php echo htmlspecialchars($komentarz['tytul_posta']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($komentarz['tresc_komentarza']); ?></td>
                            <td>
                                <a href="edytowaniekomentarza.php?id=<?php echo (int)$komentarz['id_komentarza']; ?>" class="btn btn-warning btn-sm">Edytuj</a>
                                <a href="usuwaniekomentarza.php?id=<?php echo (int)$komentarz['id_komentarza']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Na pewno chcesz usunąć ten komentarz?');">Usuń</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3"><div class="alert alert-info">Nie masz jeszcze żadnych komentarzy.</div></td></tr>
                <?php endif; ?>
            </table>
        </div>
    </div>
</body>
</html>
<?php
$stmt->close();
$stmt2->close();
$stmt3->close();
$polaczenie->close();
?>
