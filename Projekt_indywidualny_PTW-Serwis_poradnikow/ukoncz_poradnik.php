<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
$polaczenie = mysqli_connect('localhost', 'root', '', 'blog');

if (!$polaczenie) {
    echo json_encode(['error' => 'Błąd połączenia z bazą danych.']);
    exit();
}

if (!isset($_SESSION['id'])) {
    echo json_encode(['error' => 'Musisz być zalogowany, aby oznaczyć poradnik jako ukończony.']);
    exit();
}

$id_uzytkownika = (int)$_SESSION['id'];
$postId = (int)($_POST['postId'] ?? 0);

if ($postId <= 0) {
    echo json_encode(['error' => 'Nieprawidłowe ID poradnika.']);
    exit();
}

$stmt = $polaczenie->prepare("SELECT id_posta FROM posty WHERE id_posta = ?");
$stmt->bind_param("i", $postId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    echo json_encode(['error' => 'Poradnik nie istnieje.']);
    exit();
}
$stmt->close();

$stmt = $polaczenie->prepare("SELECT id FROM ukonczone_poradniki WHERE id_uzytkownika = ? AND id_posta = ?");
$stmt->bind_param("ii", $id_uzytkownika, $postId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $stmt = $polaczenie->prepare("DELETE FROM ukonczone_poradniki WHERE id_uzytkownika = ? AND id_posta = ?");
    $stmt->bind_param("ii", $id_uzytkownika, $postId);
    $stmt->execute();
    $stmt->close();

    $stmt = $polaczenie->prepare("DELETE ukonczone_kroki FROM ukonczone_kroki JOIN kroki_poradnika ON ukonczone_kroki.id_kroku = kroki_poradnika.id_kroku WHERE ukonczone_kroki.id_uzytkownika = ? AND kroki_poradnika.id_posta = ?");
    $stmt->bind_param("ii", $id_uzytkownika, $postId);
    $stmt->execute();
    $stmt->close();

    $ukonczony = false;
} else {
    $stmt = $polaczenie->prepare("INSERT INTO ukonczone_poradniki (id_uzytkownika, id_posta) VALUES (?, ?)");
    $stmt->bind_param("ii", $id_uzytkownika, $postId);
    $stmt->execute();
    $stmt->close();

    $stmt = $polaczenie->prepare("INSERT IGNORE INTO ukonczone_kroki (id_uzytkownika, id_kroku) SELECT ?, id_kroku FROM kroki_poradnika WHERE id_posta = ?");
    $stmt->bind_param("ii", $id_uzytkownika, $postId);
    $stmt->execute();
    $stmt->close();

    $ukonczony = true;
}

$stmt = $polaczenie->prepare("SELECT COUNT(*) AS liczba_krokow FROM kroki_poradnika WHERE id_posta = ?");
$stmt->bind_param("i", $postId);
$stmt->execute();
$liczba_krokow = (int)$stmt->get_result()->fetch_assoc()['liczba_krokow'];
$stmt->close();

$stmt = $polaczenie->prepare("SELECT ukonczone_kroki.id_kroku FROM ukonczone_kroki JOIN kroki_poradnika ON ukonczone_kroki.id_kroku = kroki_poradnika.id_kroku WHERE ukonczone_kroki.id_uzytkownika = ? AND kroki_poradnika.id_posta = ?");
$stmt->bind_param("ii", $id_uzytkownika, $postId);
$stmt->execute();
$result = $stmt->get_result();
$ukonczone_kroki_ids = [];
while ($row = $result->fetch_assoc()) {
    $ukonczone_kroki_ids[] = (int)$row['id_kroku'];
}
$stmt->close();

$ukonczone_kroki = count($ukonczone_kroki_ids);

echo json_encode([
    'ukonczony' => $ukonczony,
    'ukonczone_kroki' => $ukonczone_kroki,
    'liczba_krokow' => $liczba_krokow,
    'ukonczone_kroki_ids' => $ukonczone_kroki_ids
]);
?>
