<?php
require_once "sesja.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: nowypost.php");
    exit();
}

$tytulpostu = trim($_POST["tytulpostu"] ?? "");
$trescpostu = trim($_POST["trescpostu"] ?? "");
$kategoriapostu = trim($_POST["kategoriapostu"] ?? "");
$poziom_trudnosci = trim($_POST["poziom_trudnosci"] ?? "");
$czas_wykonania = trim($_POST["czas_wykonania"] ?? "");
$kroki = $_POST["kroki"] ?? [];
$id_uzytkownika = (int)$_SESSION["id"];

$kroki_czyste = [];
foreach ($kroki as $krok) {
    $krok = trim($krok);
    if ($krok != "") {
        $kroki_czyste[] = $krok;
    }
}

if ($tytulpostu == "" || $trescpostu == "" || $kategoriapostu == "" || $poziom_trudnosci == "" || $czas_wykonania == "" || count($kroki_czyste) == 0 || !isset($_FILES["zdjeciepostu"])) {
    die("BŁĄD: Proszę wypełnić wszystkie wymagane pola. <a href='nowypost.php'>Wróć</a>");
}

if ($_FILES["zdjeciepostu"]["error"] != UPLOAD_ERR_OK) {
    die("BŁĄD: Nie udało się przesłać zdjęcia. <a href='nowypost.php'>Wróć</a>");
}

$dozwolone = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
$rozszerzenie = strtolower(pathinfo($_FILES["zdjeciepostu"]["name"], PATHINFO_EXTENSION));

if (!in_array($rozszerzenie, $dozwolone)) {
    die("BŁĄD: Dozwolone formaty zdjęć: jpg, jpeg, png, gif, webp. <a href='nowypost.php'>Wróć</a>");
}

if (!is_dir("uploads")) {
    mkdir("uploads", 0777, true);
}

$nazwa_pliku = "uploads/" . time() . "_" . uniqid() . "." . $rozszerzenie;

if (!move_uploaded_file($_FILES["zdjeciepostu"]["tmp_name"], $nazwa_pliku)) {
    die("BŁĄD: Nie udało się zapisać zdjęcia. <a href='nowypost.php'>Wróć</a>");
}

$stmt = $polaczenie->prepare("INSERT INTO posty (id_uzytkownika, tytul, tresc, kategoria, data_utworzenia, zdjecie, poziom_trudnosci, czas_wykonania) VALUES (?, ?, ?, ?, NOW(), ?, ?, ?)");
$stmt->bind_param("issssss", $id_uzytkownika, $tytulpostu, $trescpostu, $kategoriapostu, $nazwa_pliku, $poziom_trudnosci, $czas_wykonania);

if ($stmt->execute()) {
    $id_posta = $stmt->insert_id;
    $stmt->close();

    $stmt_krok = $polaczenie->prepare("INSERT INTO kroki_poradnika (id_posta, numer_kroku, tresc_kroku) VALUES (?, ?, ?)");

    foreach ($kroki_czyste as $index => $krok) {
        $numer_kroku = $index + 1;
        $stmt_krok->bind_param("iis", $id_posta, $numer_kroku, $krok);
        $stmt_krok->execute();
    }

    $stmt_krok->close();
    header("Location: post.php?id=" . $id_posta);
    exit();
} else {
    echo "BŁĄD: " . htmlspecialchars($polaczenie->error);
}
?>
