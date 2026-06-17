<?php
require_once "sesja.php";

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

$tytul = "";
$tresc = "";
$kategoria = "";
$poziom_trudnosci = "";
$czas_wykonania = "";
$post_id = 0;
$kroki_posta = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $post_id = (int)($_POST["id"] ?? 0);
    $tytul = trim($_POST["tytul"] ?? "");
    $tresc = trim($_POST["tresc"] ?? "");
    $kategoria = trim($_POST["kategoriapostu"] ?? "");
    $poziom_trudnosci = trim($_POST["poziom_trudnosci"] ?? "");
    $czas_wykonania = trim($_POST["czas_wykonania"] ?? "");
    $kroki = $_POST["kroki"] ?? [];

    $kroki_czyste = [];
    foreach ($kroki as $krok) {
        $krok = trim($krok);
        if ($krok != "") {
            $kroki_czyste[] = $krok;
        }
    }

    if ($post_id <= 0 || $tytul == "" || $tresc == "" || $kategoria == "" || $poziom_trudnosci == "" || $czas_wykonania == "" || count($kroki_czyste) == 0) {
        echo "<script>alert('Uzupełnij wszystkie wymagane pola.'); window.location.href = 'settings.php';</script>";
        exit();
    }

    $stmt = $polaczenie->prepare("SELECT id_posta FROM posty WHERE id_posta = ? AND id_uzytkownika = ?");
    $stmt->bind_param("ii", $post_id, $_SESSION["id"]);
    $stmt->execute();
    $wynik_wlasciciel = $stmt->get_result();
    if ($wynik_wlasciciel->num_rows != 1) {
        echo "<script>alert('Nie masz uprawnień do edycji tego poradnika.'); window.location.href = 'settings.php';</script>";
        exit();
    }
    $stmt->close();

    $stmt = $polaczenie->prepare("UPDATE posty SET tytul = ?, tresc = ?, kategoria = ?, poziom_trudnosci = ?, czas_wykonania = ? WHERE id_posta = ? AND id_uzytkownika = ?");
    $stmt->bind_param("sssssii", $tytul, $tresc, $kategoria, $poziom_trudnosci, $czas_wykonania, $post_id, $_SESSION["id"]);
    $stmt->execute();
    $stmt->close();

    $stmt = $polaczenie->prepare("DELETE ukonczone_kroki FROM ukonczone_kroki INNER JOIN kroki_poradnika ON ukonczone_kroki.id_kroku = kroki_poradnika.id_kroku WHERE kroki_poradnika.id_posta = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $polaczenie->prepare("DELETE FROM ukonczone_poradniki WHERE id_posta = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $polaczenie->prepare("DELETE FROM kroki_poradnika WHERE id_posta = ?");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $stmt->close();

    $stmt_krok = $polaczenie->prepare("INSERT INTO kroki_poradnika (id_posta, numer_kroku, tresc_kroku) VALUES (?, ?, ?)");
    foreach ($kroki_czyste as $index => $krok) {
        $numer_kroku = $index + 1;
        $stmt_krok->bind_param("iis", $post_id, $numer_kroku, $krok);
        $stmt_krok->execute();
    }
    $stmt_krok->close();

    echo "<script>alert('Poradnik został zaktualizowany'); window.location.href = 'settings.php';</script>";
    exit();
} else {
    if (!isset($_GET["id"])) {
        header("Location: settings.php");
        exit();
    }

    $post_id = (int)$_GET["id"];

    if ($post_id <= 0) {
        header("Location: settings.php");
        exit();
    }

    $stmt = $polaczenie->prepare("SELECT tytul, tresc, kategoria, poziom_trudnosci, czas_wykonania FROM posty WHERE id_posta = ? AND id_uzytkownika = ?");
    $stmt->bind_param("ii", $post_id, $_SESSION["id"]);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $tytul = $row["tytul"];
        $tresc = $row["tresc"];
        $kategoria = $row["kategoria"];
        $poziom_trudnosci = $row["poziom_trudnosci"];
        $czas_wykonania = $row["czas_wykonania"];
    } else {
        echo "<script>alert('Poradnik nie istnieje albo nie masz uprawnień do jego edycji.'); window.location.href = 'settings.php';</script>";
        exit();
    }
    $stmt->close();

    $stmt = $polaczenie->prepare("SELECT tresc_kroku FROM kroki_poradnika WHERE id_posta = ? ORDER BY numer_kroku");
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $wynik_kroki = $stmt->get_result();
    while ($krok = $wynik_kroki->fetch_assoc()) {
        $kroki_posta[] = $krok["tresc_kroku"];
    }
    $stmt->close();
}

$kategorie2 = mysqli_query($polaczenie, "SELECT nazwa_kategorii FROM kategorie ORDER BY nazwa_kategorii");
$kategorie3 = mysqli_fetch_all($kategorie2, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edytuj poradnik</title>
    <link rel="stylesheet" href="styl.css">
</head>
<body>
    <div class="settings-container">
        <h2>Edytuj poradnik</h2>
        <form action="edytowaniepost.php" method="post">
            <input type="hidden" name="id" value="<?php echo (int)$post_id; ?>">

            <div class="form-group">
                <label for="tytul">Tytuł:</label>
                <input type="text" class="form-control" id="tytul" name="tytul" value="<?php echo htmlspecialchars($tytul); ?>" required>
            </div>

            <div class="form-group">
                <label for="tresc">Krótki opis:</label>
                <textarea class="form-control" id="tresc" name="tresc" rows="5" required><?php echo htmlspecialchars($tresc); ?></textarea>
            </div>

            <div class="form-group">
                <label for="kategoriapostu">Kategoria poradnika:</label>
                <select name="kategoriapostu" id="kategoriapostu" class="form-control" required>
                    <?php foreach ($kategorie3 as $kat): ?>
                        <option value="<?php echo htmlspecialchars($kat['nazwa_kategorii']); ?>" <?php if ($kat['nazwa_kategorii'] == $kategoria) echo "selected"; ?>>
                            <?php echo htmlspecialchars($kat['nazwa_kategorii']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="poziom_trudnosci">Poziom trudności:</label>
                <select name="poziom_trudnosci" id="poziom_trudnosci" class="form-control" required>
                    <?php foreach (["Łatwy", "Średni", "Trudny"] as $poziom): ?>
                        <option value="<?php echo $poziom; ?>" <?php if ($poziom == $poziom_trudnosci) echo "selected"; ?>><?php echo $poziom; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="czas_wykonania">Szacowany czas wykonania:</label>
                <input type="text" class="form-control" id="czas_wykonania" name="czas_wykonania" value="<?php echo htmlspecialchars($czas_wykonania); ?>" required>
            </div>

            <h3>Kroki poradnika</h3>
            <?php for ($i = 0; $i < 5; $i++): ?>
                <div class="form-group">
                    <label for="krok<?php echo $i + 1; ?>">Krok <?php echo $i + 1; ?>:</label>
                    <textarea class="form-control" name="kroki[]" id="krok<?php echo $i + 1; ?>" rows="3" <?php echo $i == 0 ? 'required' : ''; ?>><?php echo htmlspecialchars($kroki_posta[$i] ?? ""); ?></textarea>
                </div>
            <?php endfor; ?>

            <button type="submit" class="btn btn-primary">Zaktualizuj poradnik</button>
            <a href="settings.php" class="btn btn-secondary">Wróć do ustawień</a>
        </form>
    </div>
</body>
</html>
