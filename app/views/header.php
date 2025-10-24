<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ShipiShop - Votre boutique en ligne</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <style>
    :root {
    --primary-color: #FF6FB5;        
    --primary-hover: #FCA3CC;        
    --accent-color: #E0BBE4;         
    --background-light: #FFF9FB;     
    --text-dark: #4B4453;            
    --border-light: #F3EAF4;         
    --shadow-soft: 0 4px 8px rgba(0, 0, 0, 0.08);
    }

        
        body { 
            background: linear-gradient(135deg, #ffffff 0%, #f9fafb 100%);
            font-family: 'DM Sans', sans-serif;
            color: var(--text-dark);
            line-height: 1.6;
        }
        
        .navbar-modern {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            box-shadow: var(--shadow-soft);
            padding: 1rem 0;
            border-bottom: 3px solid rgba(255, 255, 255, 0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: white !important;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .navbar-nav .nav-link {
            color: rgba(255, 255, 255, 0.9) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            margin: 0 0.25rem;
        }
        
        .navbar-nav .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            color: white !important;
            transform: translateY(-1px);
        }
        
        .card-modern {
            border: none;
            border-radius: 1rem;
            background: var(--background-light);
            box-shadow: var(--shadow-soft);
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .card-modern:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .card-modern img {
            height: 220px;
            object-fit: cover;
            transition: transform 0.3s ease;
        }
        
        .card-modern:hover img {
            transform: scale(1.05);
        }
        
        .btn-modern-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--accent-color) 100%);
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
        }
        
        .btn-modern-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 15px -3px #f6eafbff;
            color: white;
        }
        
        .form-modern {
            background: white;
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-light);
        }
        
        .form-control-modern {
            border: 2px solid var(--border-light);
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control-modern:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(234, 88, 12, 0.25);
        }
        
        .filter-section {
            background: white;
            border-radius: 1rem;
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-soft);
            border: 1px solid var(--border-light);
        }
        
        .price-tag {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        
        .container-modern {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }
        
        .main-content {
            padding-top: 2rem;
            min-height: calc(100vh - 200px);
        }
        
        @media (max-width: 768px) {
            .navbar-brand {
                font-size: 1.25rem;
            }
            
            .card-modern img {
                height: 180px;
            }
            
            .form-modern {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-modern sticky-top">
    <div class="container-modern">
        <a class="navbar-brand" href="index.php">

            <img src="logo_shipishop.png" alt="Logo ShipiShop" class="logo" style="border-radius: 80px; ">
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (!empty($_SESSION['user']) && $_SESSION['user']['usertype'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=admin_products">
                            <i class="fas fa-cogs me-1"></i>Admin Produits
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=admin_list_orders">
                            <i class="fas fa-list-alt me-1"></i>Admin Commandes
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=dashboard">
                            <i class="fas fa-list-alt me-1"></i>Analyse vente
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if (!empty($_SESSION['user'])): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=logout">
                            <i class="fas fa-sign-out-alt me-1"></i>Déconnexion
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=login">
                            <i class="fas fa-sign-in-alt me-1"></i>Connexion
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=register">
                            <i class="fas fa-user-plus me-1"></i>Inscription
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php if (!empty($_SESSION['user']) && $_SESSION['user']['usertype'] === 'client'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php?action=cart">
                            <i class="fas fa-shopping-cart me-1"></i>Panier
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<div class="container-modern main-content">
