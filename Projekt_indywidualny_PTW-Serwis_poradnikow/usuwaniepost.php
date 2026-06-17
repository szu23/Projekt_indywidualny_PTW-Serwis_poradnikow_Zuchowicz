<?php
require_once "sesja.php";

if (!isset($_SESSION["id"])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET["id"])) {
    header("Location: settings.php");
    exit();
}

$post_id = (int)$_GET["id"];
$id_uzytkownika = (int)$_SESSION["id"];

$stmt = $polaczenie->prepare("SELECT id_posta FROM posty WHERE id_posta = ? AND id_uzytkownika = ?");
$stmt->bind_param("ii", $post_id, $id_uzytkownika);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows != 1) {
    echo "<script>alert('Nie możesz usunąć tego poradnika.'); window.location.href = 'settings.php';</script>";
    exit();
}
$stmt->close();

$stmt = $polaczenie->prepare("DELETE FROM komentarze WHERE id_posta = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->close();

$stmt = $polaczenie->prepare("DELETE FROM glosy WHERE id_posta = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->close();

$stmt = $polaczenie->prepare("DELETE FROM ukonczone_poradniki WHERE id_posta = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->close();

$stmt = $polaczenie->prepare("DELETE ukonczone_kroki FROM ukonczone_kroki JOIN kroki_poradnika ON ukonczone_kroki.id_kroku = kroki_poradnika.id_kroku WHERE kroki_poradnika.id_posta = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->close();

$stmt = $polaczenie->prepare("DELETE FROM kroki_poradnika WHERE id_posta = ?");
$stmt->bind_param("i", $post_id);
$stmt->execute();
$stmt->close();

$stmt = $polaczenie->prepare("DELETE FROM posty WHERE id_posta = ? AND id_uzytkownika = ?");
$stmt->bind_param("ii", $post_id, $id_uzytkownika);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "<script>alert('Poradnik został usunięty'); window.location.href = 'settings.php';</script>";
} else {
    echo "<script>alert('Wystąpił problem podczas usuwania. Spróbuj ponownie.'); window.location.href = 'settings.php';</script>";
}

$stmt->close();
?>
