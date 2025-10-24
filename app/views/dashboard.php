<?php require __DIR__ . '/header.php'; ?>
<div class="container py-5">
    <h2 class="text-center mb-4">📊 Tableau de bord des ventes</h2>

    <form method="get" action="index.php" class="row mb-4">
        <input type="hidden" name="action" value="dashboard">
        <div class="col-md-4">
            <label>Année :</label>
            <select name="annee" class="form-control">
                <?php foreach ($years as $year): ?>
                    <option value="<?= $year ?>" <?= $year == $selectedYear ? 'selected' : '' ?>>
                        <?= $year ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label>Catégorie :</label>
            <select name="categorie" class="form-control">
                <option value="">Toutes</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $selectedCat ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">🔄 Actualiser</button>
        </div>
    </form>

    <div class="text-center">
        <img src="index.php?action=chart&type=ventes_mensuelles&annee=<?= $selectedYear ?>&categorie=<?= $selectedCat ?>"
             class="img-fluid mb-4 rounded shadow">
        <img src="index.php?action=chart&type=repartition_categories&annee=<?= $selectedYear ?>"
             class="img-fluid mb-4 rounded shadow">
        <img src="index.php?action=chart&type=quantites_mensuelles&annee=<?= $selectedYear ?>&categorie=<?= $selectedCat ?>"
             class="img-fluid mb-4 rounded shadow">
    </div>
</div>
<?php require __DIR__ . '/footer.php'; ?>
