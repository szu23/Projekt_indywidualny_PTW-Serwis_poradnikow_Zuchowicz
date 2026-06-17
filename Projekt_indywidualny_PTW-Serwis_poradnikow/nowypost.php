<?php
require_once "sesja.php";

$kategorie2 = mysqli_query($polaczenie, "SELECT nazwa_kategorii FROM kategorie ORDER BY nazwa_kategorii");
$kategorie3 = mysqli_fetch_all($kategorie2, MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dodaj poradnik</title>
    <link rel="stylesheet" href="styl.css?v=<?php echo time(); ?>">
</head>
<body>
    <div id="pasekgorny">
        <img id="logo" src="blogger.png" alt="Logo">
        <a id="index" href="welcome.php">Poradniki</a>
    </div>

    <div class="formpost">
        <h2>Dodaj poradnik krok po kroku</h2>

        <form method="post" action="nowypost2.php" enctype="multipart/form-data">
            <label for="tytulpostu">Tytuł poradnika:</label>
            <input type="text" name="tytulpostu" id="tytulpostu" required><br><br>

            <label for="trescpostu">Krótki opis poradnika:</label>
            <textarea name="trescpostu" id="trescpostu" required></textarea><br><br>

            <label for="kategoriapostu">Kategoria poradnika:</label>
            <select name="kategoriapostu" id="kategoriapostu" required>
                <?php foreach ($kategorie3 as $kategoria): ?>
                    <option value="<?php echo htmlspecialchars($kategoria['nazwa_kategorii']); ?>">
                        <?php echo htmlspecialchars($kategoria['nazwa_kategorii']); ?>
                    </option>
                <?php endforeach; ?>
            </select><br><br>

            <label for="poziom_trudnosci">Poziom trudności:</label>
            <select name="poziom_trudnosci" id="poziom_trudnosci" required>
                <option value="Łatwy">Łatwy</option>
                <option value="Średni">Średni</option>
                <option value="Trudny">Trudny</option>
            </select><br><br>

            <label for="czas_wykonania">Szacowany czas wykonania:</label>
            <input type="text" name="czas_wykonania" id="czas_wykonania" placeholder="np. 15 minut" required><br><br>

            <label for="zdjeciepostu">Zdjęcie poradnika:</label>
            <input type="file" name="zdjeciepostu" id="zdjeciepostu" accept="image/*" required><br><br>

            <h3>Kroki poradnika</h3>
            <p class="pomoc">Wpisz przynajmniej jeden krok. Puste pola zostaną pominięte.</p>

            <?php for ($i = 1; $i <= 5; $i++): ?>
                <label for="krok<?php echo $i; ?>">Krok <?php echo $i; ?>:</label>
                <textarea name="kroki[]" id="krok<?php echo $i; ?>" placeholder="Opisz krok <?php echo $i; ?>" <?php echo $i == 1 ? 'required' : ''; ?>></textarea><br><br>
            <?php endfor; ?>

            <button id="dodajpost2" type="submit">Dodaj poradnik</button>
            <a href="welcome.php" class="btn btn-secondary">Anuluj</a>
        </form>
    </div>
</body>
</html>
