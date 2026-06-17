<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
$polaczenie = mysqli_connect('localhost', 'root', '', 'blog');

if (!$polaczenie) {
    echo json_encode(['error' => 'Błąd połączenia z bazą danych.']);
    exit();
}

if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'Musisz być zalogowany, aby oznaczać kroki jako wykonane.']);
    exit();
}

$id_uzytkownika = (int)$_SESSION['id'];
$postId = (int)($_POST['postId'] ?? 0);
$krokId = (int)($_POST['krokId'] ?? 0);

if ($postId <= 0 || $krokId <= 0) {
    echo json_encode(['error' => 'Nieprawidłowe dane kroku.']);
    exit();
}

$stmt = $polaczenie->prepare("SELECT id_kroku FROM kroki_poradnika WHERE id_kroku = ? AND id_posta = ?");
$stmt->bind_param("ii", $krokId, $postId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    echo json_encode(['error' => 'Ten krok nie istnieje w wybranym poradniku.']);
    exit();
}
$stmt->close();

$stmt = $polaczenie->prepare("SELECT id FROM ukonczone_kroki WHERE id_uzytkownika = ? AND id_kroku = ?");
$stmt->bind_param("ii", $id_uzytkownika, $krokId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt = $polaczenie->prepare("DELETE FROM ukonczone_kroki WHERE id_uzytkownika = ? AND id_kroku = ?");
    $stmt->bind_param("ii", $id_uzytkownika, $krokId);
    $stmt->execute();
    $zaznaczony = false;
} else {
    $stmt = $polaczenie->prepare("INSERT INTO ukonczone_kroki (id_uzytkownika, id_kroku) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_uzytkownika, $krokId);
    $stmt->execute();
    $zaznaczony = true;
}
$stmt->close();

$stmt = $polaczenie->prepare("SELECT COUNT(*) AS liczba_krokow FROM kroki_poradnika WHERE id_posta = ?");
$stmt->bind_param("i", $postId);
$stmt->execute();
$liczba_krokow = (int)$stmt->get_result()->fetch_assoc()['liczba_krokow'];
$stmt->close();

$stmt = $polaczenie->prepare("SELECT COUNT(*) AS ukonczone_kroki FROM ukonczone_kroki JOIN kroki_poradnika ON ukonczone_kroki.id_kroku = kroki_poradnika.id_kroku WHERE ukonczone_kroki.id_uzytkownika = ? AND kroki_poradnika.id_posta = ?");
$stmt->bind_param("ii", $id_uzytkownika, $postId);
$stmt->execute();
$ukonczone_kroki = (int)$stmt->get_result()->fetch_assoc()['ukonczone_kroki'];
$stmt->close();

if ($liczba_krokow > 0 && $ukonczone_kroki == $liczba_krokow) {
    $stmt = $polaczenie->prepare("INSERT IGNORE INTO ukonczone_poradniki (id_uzytkownika, id_posta) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_uzytkownika, $postId);
    $stmt->execute();
    $stmt->close();
    $ukonczony = true;
} else {
    $stmt = $polaczenie->prepare("DELETE FROM ukonczone_poradniki WHERE id_uzytkownika = ? AND id_posta = ?");
    $stmt->bind_param("ii", $id_uzytkownika, $postId);
    $stmt->execute();
    $stmt->close();
    $ukonczony = false;
}

echo json_encode([
    'zaznaczony' => $zaznaczony,
    'ukonczony' => $ukonczony,
    'ukonczone_kroki' => $ukonczone_kroki,
    'liczba_krokow' => $liczba_krokow
]);
?>
