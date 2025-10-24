<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../jpgraph/src/jpgraph.php';
require_once __DIR__ . '/../jpgraph/src/jpgraph_bar.php';
require_once __DIR__ . '/../jpgraph/src/jpgraph_pie.php';
require_once __DIR__ . '/../jpgraph/src/jpgraph_pie3d.php';

class DashboardController {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    public function index() {
        // 📅 Récupère les années disponibles dans les ventes
        $years = $this->pdo->query("
            SELECT DISTINCT YEAR(created_at) AS annee 
            FROM commandes 
            ORDER BY annee DESC
        ")->fetchAll(PDO::FETCH_COLUMN);

        // 📦 Récupère les catégories
        $categories = $this->pdo->query("
            SELECT id, nom FROM categories ORDER BY nom
        ")->fetchAll(PDO::FETCH_ASSOC);

        $selectedYear = $_GET['annee'] ?? date('Y');
        $selectedCat = $_GET['categorie'] ?? '';

        require __DIR__ . '/../views/dashboard.php';
    }

    public function generateChart() {
        $type = $_GET['type'] ?? '';
        $annee = $_GET['annee'] ?? date('Y');
        $categorie = $_GET['categorie'] ?? null;

        switch ($type) {
            case 'ventes_mensuelles':
                $this->ventesMensuelles($annee, $categorie);
                break;
            case 'repartition_categories':
                $this->repartitionCategories($annee);
                break;
            case 'quantites_mensuelles':
                $this->quantitesMensuelles($annee, $categorie);
                break;
        }
    }

    // ================== 📊 1. VENTES MENSUELLES ==================
    public function ventesMensuelles($annee = null, $categorie_id = null) {
        $annee = $annee ?: date('Y');

        $sql = "SELECT 
                    MONTH(c.created_at) AS mois, 
                    SUM(ci.quantite * ci.prix_unitaire) AS total_ventes
                FROM commandes c
                JOIN commande_items ci ON c.id = ci.commande_id
                JOIN produits p ON ci.produit_id = p.id
                WHERE YEAR(c.created_at) = :annee";

        if (!empty($categorie_id)) {
            $sql .= " AND p.categorie_id = :categorie_id";
        }

        $sql .= " GROUP BY mois ORDER BY mois ASC";
        $stmt = $this->pdo->prepare($sql);
        $params = ['annee' => $annee];
        if (!empty($categorie_id)) $params['categorie_id'] = $categorie_id;
        $stmt->execute($params);

        $ventes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = [];
        $data = [];
        foreach ($ventes as $row) {
            $labels[] = "Mois " . $row['mois'];
            $data[] = (float)$row['total_ventes'];
        }

        $graph = new Graph(800, 400);
        $graph->SetScale('textlin');
        $graph->img->SetMargin(60, 20, 40, 80);
        $graph->title->Set("Ventes mensuelles ($annee)");
        $graph->xaxis->SetTickLabels($labels);
        $graph->xaxis->SetLabelAngle(45);

        $bar = new BarPlot($data);
        $bar->SetFillGradient('#007bff', '#87cefa', GRAD_VER);
        $bar->value->Show();
        $bar->value->SetFormat('%0.2f DH');
        $graph->Add($bar);

        header('Content-Type: image/png');
        $graph->Stroke();
        exit;
    }

    // ================== 🥧 2. RÉPARTITION PAR CATÉGORIE ==================
    private function repartitionCategories($annee) {
        $sql = "SELECT 
                    cat.nom AS categorie,
                    SUM(ci.quantite * ci.prix_unitaire) AS total
                FROM commandes c
                JOIN commande_items ci ON c.id = ci.commande_id
                JOIN produits p ON ci.produit_id = p.id
                JOIN categories cat ON p.categorie_id = cat.id
                WHERE YEAR(c.created_at) = :annee
                GROUP BY cat.nom";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['annee' => $annee]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = array_column($rows, 'categorie');
        $data = array_map(fn($r) => (float)$r['total'], $rows);

        $graph = new PieGraph(700, 400);
        $graph->title->Set("Répartition du chiffre d’affaires par catégorie ($annee)");
        $pie = new PiePlot3D($data);
        $pie->SetLegends($labels);
        $pie->ExplodeAll(10);
        $graph->Add($pie);

        header('Content-Type: image/png');
        $graph->Stroke();
    }

    // ================== 📦 3. QUANTITÉS MENSUELLES ==================
    private function quantitesMensuelles($annee, $categorie = null) {
        $sql = "SELECT 
                    MONTH(c.created_at) AS mois,
                    SUM(ci.quantite) AS total_qte
                FROM commandes c
                JOIN commande_items ci ON c.id = ci.commande_id
                JOIN produits p ON ci.produit_id = p.id
                WHERE YEAR(c.created_at) = :annee";

        $params = ['annee' => $annee];
        if ($categorie) {
            $sql .= " AND p.categorie_id = :cat";
            $params['cat'] = $categorie;
        }

        $sql .= " GROUP BY mois ORDER BY mois ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $labels = array_map(fn($r) => 'M' . $r['mois'], $rows);
        $data = array_map(fn($r) => (float)$r['total_qte'], $rows);

        $graph = new Graph(700, 400);
        $graph->SetScale('textlin');
        $graph->title->Set("Quantités vendues par mois ($annee)");
        $graph->xaxis->SetTickLabels($labels);
        $bar = new BarPlot($data);
        $bar->SetFillGradient("#007bff", "#87cefa", GRAD_VER);
        $bar->value->Show();
        $graph->Add($bar);

        header('Content-Type: image/png');
        $graph->Stroke();
    }
}
?>
