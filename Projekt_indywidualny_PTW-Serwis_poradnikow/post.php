<?php
session_start();
$polaczenie = mysqli_connect('localhost', 'root', '', 'blog');
if (!$polaczenie) {
    die('Błąd połączenia z bazą danych');
}

$postId = (int)($_GET['id'] ?? 0);
$post = null;

$stmt = mysqli_prepare($polaczenie, "SELECT posty.*, uzytkownicy.nazwa_uzytkownika AS autor FROM posty JOIN uzytkownicy ON posty.id_uzytkownika = uzytkownicy.id_uzytkownika WHERE posty.id_posta = ?");
mysqli_stmt_bind_param($stmt, "i", $postId);
mysqli_stmt_execute($stmt);
$wynik = mysqli_stmt_get_result($stmt);
$post = mysqli_fetch_assoc($wynik);

if (isset($_POST['dodaj_komentarz'])) {
    if (!isset($_SESSION['id'])) {
        echo '<script>alert("Musisz być zalogowany, aby dodać komentarz."); window.location.href="login.php";</script>';
        exit();
    }

    $id_uzytkownika = (int)$_SESSION['id'];
    $id_posta = (int)($_POST['id_posta'] ?? 0);
    $tresc_komentarza = trim($_POST['tresc_komentarza'] ?? '');

    if ($id_posta > 0 && $tresc_komentarza !== '') {
        $stmt = mysqli_prepare($polaczenie, "INSERT INTO komentarze (id_uzytkownika, id_posta, tresc_komentarza) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "iis", $id_uzytkownika, $id_posta, $tresc_komentarza);
        mysqli_stmt_execute($stmt);
    }

    header("Location: post.php?id=" . $id_posta);
    exit();
}
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Poradnik</title>
    <link rel="stylesheet" href="styl.css?v=<?php echo time(); ?>">
</head>
<body>
    <div id="pasekgorny">
        <img id="logo" src="blogger.png" alt="Logo">
        <?php if (!isset($_SESSION['id'])): ?>
            <a id="index" href="index.php">Poradniki</a>
        <?php else: ?>
            <a id="index" href="welcome.php">Poradniki</a>
        <?php endif; ?>
    </div>

    <?php if ($post): ?>
        <?php
        $stmt = mysqli_prepare($polaczenie, "SELECT COUNT(*) AS likes FROM glosy WHERE id_posta = ? AND glosowanie = 1");
        mysqli_stmt_bind_param($stmt, "i", $postId);
        mysqli_stmt_execute($stmt);
        $likes = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['likes'];

        $stmt = mysqli_prepare($polaczenie, "SELECT COUNT(*) AS dislikes FROM glosy WHERE id_posta = ? AND glosowanie = -1");
        mysqli_stmt_bind_param($stmt, "i", $postId);
        mysqli_stmt_execute($stmt);
        $dislikes = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt))['dislikes'];

        $stmt = mysqli_prepare($polaczenie, "SELECT id_kroku, numer_kroku, tresc_kroku FROM kroki_poradnika WHERE id_posta = ? ORDER BY numer_kroku");
        mysqli_stmt_bind_param($stmt, "i", $postId);
        mysqli_stmt_execute($stmt);
        $wynik_kroki = mysqli_stmt_get_result($stmt);
        $kroki = mysqli_fetch_all($wynik_kroki, MYSQLI_ASSOC);
        $liczba_krokow = count($kroki);

        $ukonczony = false;
        $ukonczone_kroki = [];
        $liczba_ukonczonych_krokow = 0;

        if (isset($_SESSION['id'])) {
            $id_uzytkownika = (int)$_SESSION['id'];

            $stmt = mysqli_prepare($polaczenie, "SELECT id FROM ukonczone_poradniki WHERE id_uzytkownika = ? AND id_posta = ?");
            mysqli_stmt_bind_param($stmt, "ii", $id_uzytkownika, $postId);
            mysqli_stmt_execute($stmt);
            $wynik_ukonczony = mysqli_stmt_get_result($stmt);
            $ukonczony = mysqli_num_rows($wynik_ukonczony) > 0;

            $stmt = mysqli_prepare($polaczenie, "SELECT ukonczone_kroki.id_kroku FROM ukonczone_kroki JOIN kroki_poradnika ON ukonczone_kroki.id_kroku = kroki_poradnika.id_kroku WHERE ukonczone_kroki.id_uzytkownika = ? AND kroki_poradnika.id_posta = ?");
            mysqli_stmt_bind_param($stmt, "ii", $id_uzytkownika, $postId);
            mysqli_stmt_execute($stmt);
            $wynik_ukonczone_kroki = mysqli_stmt_get_result($stmt);
            while ($wiersz_kroku = mysqli_fetch_assoc($wynik_ukonczone_kroki)) {
                $ukonczone_kroki[] = (int)$wiersz_kroku['id_kroku'];
            }
            $liczba_ukonczonych_krokow = count($ukonczone_kroki);
        }

        $adres_kategorii = isset($_SESSION['id']) ? 'welcome.php' : 'index.php';
        ?>

        <div class="stronaoposcie">
            <img src="<?php echo htmlspecialchars($post['zdjecie']); ?>" alt="Zdjęcie poradnika">
            <h1><?php echo htmlspecialchars($post['tytul']); ?></h1>
            <h3>Autor: <?php echo htmlspecialchars($post['autor']); ?></h3>
            <h3>Kategoria: <a href="<?php echo $adres_kategorii; ?>?kategoria=<?php echo urlencode($post['kategoria']); ?>"><?php echo htmlspecialchars($post['kategoria']); ?></a></h3>
            <h3>Trudność: <?php echo htmlspecialchars($post['poziom_trudnosci'] ?? 'brak'); ?></h3>
            <h3>Czas wykonania: <?php echo htmlspecialchars($post['czas_wykonania'] ?? 'brak'); ?></h3>
            <h3>Opublikowano: <?php echo htmlspecialchars($post['data_utworzenia']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($post['tresc'])); ?></p>
        </div>

        <div class="kroki-box">
            <h2>Kroki poradnika:</h2>

            <?php if ($liczba_krokow > 0): ?>
                <?php if (isset($_SESSION['id'])): ?>
                    <p id="postep-krokow">
                        Wykonano <span id="kroki-done"><?php echo $liczba_ukonczonych_krokow; ?></span>
                        z <span id="kroki-total"><?php echo $liczba_krokow; ?></span> kroków.
                    </p>

                    <ol class="lista-krokow lista-krokow-checkboxy">
                        <?php foreach ($kroki as $krok): ?>
                            <?php $czy_krok_ukonczony = in_array((int)$krok['id_kroku'], $ukonczone_kroki, true); ?>
                            <li class="<?php echo $czy_krok_ukonczony ? 'krok-wykonany' : ''; ?>">
                                <label class="krok-checkbox-label">
                                    <input
                                        type="checkbox"
                                        class="krok-checkbox"
                                        data-post-id="<?php echo $postId; ?>"
                                        data-krok-id="<?php echo (int)$krok['id_kroku']; ?>"
                                        <?php echo $czy_krok_ukonczony ? 'checked' : ''; ?>
                                    >
                                    <span><?php echo nl2br(htmlspecialchars($krok['tresc_kroku'])); ?></span>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ol>
                <?php else: ?>
                    <ol class="lista-krokow">
                        <?php foreach ($kroki as $krok): ?>
                            <li><?php echo nl2br(htmlspecialchars($krok['tresc_kroku'])); ?></li>
                        <?php endforeach; ?>
                    </ol>
                <?php endif; ?>
            <?php else: ?>
                <p class="brak">Ten poradnik nie ma jeszcze dodanych kroków.</p>
            <?php endif; ?>
        </div>

        <div class="like-section">
            <button class="like-btn" data-id="<?php echo $postId; ?>">👍 Pomocny (<span id="like-count"><?php echo $likes; ?></span>)</button>
            <button class="dislike-btn" data-id="<?php echo $postId; ?>">👎 Niepomocny (<span id="dislike-count"><?php echo $dislikes; ?></span>)</button>

            <?php if (isset($_SESSION['id'])): ?>
                <button id="ukoncz-btn" data-id="<?php echo $postId; ?>">
                    <?php echo $ukonczony ? 'Cofnij ukończenie' : 'Oznacz jako ukończony'; ?>
                </button>
                <span id="ukoncz-status"><?php echo $ukonczony ? 'Poradnik ukończony' : ''; ?></span>
            <?php else: ?>
                <p class="brak">Zaloguj się, aby oznaczyć poradnik jako ukończony.</p>
            <?php endif; ?>
        </div>

        <hr>
        <form method="post" action="post.php?id=<?php echo $postId; ?>">
            <input type="hidden" name="id_posta" value="<?php echo $postId; ?>">
            <textarea name="tresc_komentarza" id="tresc_komentarza" placeholder="Masz pytanie albo uwagę do poradnika?" required></textarea><br>
            <button type="submit" name="dodaj_komentarz" id="dodanie_komentarza">Dodaj komentarz</button>
        </form>

        <hr>
        <div class="komentarze">
            <h2>Wszystkie komentarze:</h2>
            <?php
            $stmt = mysqli_prepare($polaczenie, "SELECT komentarze.tresc_komentarza, uzytkownicy.nazwa_uzytkownika FROM komentarze JOIN uzytkownicy ON komentarze.id_uzytkownika = uzytkownicy.id_uzytkownika WHERE komentarze.id_posta = ? ORDER BY komentarze.id_komentarza DESC");
            mysqli_stmt_bind_param($stmt, "i", $postId);
            mysqli_stmt_execute($stmt);
            $wynik_komentarze = mysqli_stmt_get_result($stmt);
            ?>

            <?php while ($komentarz = mysqli_fetch_assoc($wynik_komentarze)): ?>
                <div class="komentarz">
                    <p><b><?php echo htmlspecialchars($komentarz['nazwa_uzytkownika']); ?></b> napisał: <?php echo htmlspecialchars($komentarz['tresc_komentarza']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="brak">Nie znaleziono poradnika o podanym ID.</p>
    <?php endif; ?>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        function ustawStanUkonczenia(data) {
            const ukonczBtn = document.getElementById("ukoncz-btn");
            const ukonczStatus = document.getElementById("ukoncz-status");
            const krokiDone = document.getElementById("kroki-done");
            const krokiTotal = document.getElementById("kroki-total");

            if (ukonczBtn && typeof data.ukonczony !== "undefined") {
                ukonczBtn.innerText = data.ukonczony ? "Cofnij ukończenie" : "Oznacz jako ukończony";
            }

            if (ukonczStatus && typeof data.ukonczony !== "undefined") {
                ukonczStatus.innerText = data.ukonczony ? "Poradnik ukończony" : "";
            }

            if (krokiDone && typeof data.ukonczone_kroki !== "undefined") {
                krokiDone.innerText = data.ukonczone_kroki;
            }

            if (krokiTotal && typeof data.liczba_krokow !== "undefined") {
                krokiTotal.innerText = data.liczba_krokow;
            }
        }

        document.querySelectorAll(".like-btn, .dislike-btn").forEach(button => {
            button.addEventListener("click", function () {
                const postId = this.getAttribute("data-id");
                const action = this.classList.contains("like-btn") ? "like" : "dislike";

                fetch("like_dislike.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `postId=${postId}&action=${action}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    document.getElementById("like-count").innerText = data.likes;
                    document.getElementById("dislike-count").innerText = data.dislikes;
                });
            });
        });

        document.querySelectorAll(".krok-checkbox").forEach(checkbox => {
            checkbox.addEventListener("change", function () {
                const krokId = this.getAttribute("data-krok-id");
                const postId = this.getAttribute("data-post-id");
                const li = this.closest("li");
                const poprzedniStan = !this.checked;

                fetch("ukoncz_krok.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `postId=${postId}&krokId=${krokId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        this.checked = poprzedniStan;
                        return;
                    }

                    this.checked = data.zaznaczony;
                    if (li) {
                        li.classList.toggle("krok-wykonany", data.zaznaczony);
                    }
                    ustawStanUkonczenia(data);
                })
                .catch(() => {
                    alert("Wystąpił błąd podczas zapisywania kroku.");
                    this.checked = poprzedniStan;
                });
            });
        });

        const ukonczBtn = document.getElementById("ukoncz-btn");
        if (ukonczBtn) {
            ukonczBtn.addEventListener("click", function () {
                const postId = this.getAttribute("data-id");

                fetch("ukoncz_poradnik.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `postId=${postId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    ustawStanUkonczenia(data);

                    if (Array.isArray(data.ukonczone_kroki_ids)) {
                        document.querySelectorAll(".krok-checkbox").forEach(checkbox => {
                            const idKroku = parseInt(checkbox.getAttribute("data-krok-id"));
                            const zaznaczony = data.ukonczone_kroki_ids.includes(idKroku);
                            checkbox.checked = zaznaczony;
                            const li = checkbox.closest("li");
                            if (li) {
                                li.classList.toggle("krok-wykonany", zaznaczony);
                            }
                        });
                    }
                });
            });
        }
    });
    </script>
</body>
</html>
