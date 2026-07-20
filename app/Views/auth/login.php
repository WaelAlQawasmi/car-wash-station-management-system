<?php
use App\Services\LanguageService;
use App\Core\Csrf;

$locale = LanguageService::getLocale();
$isRtl = LanguageService::isRtl();
?>
<!doctype html>
<html lang="<?= $locale ?>" dir="<?= $isRtl ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= __('login') ?> | <?= __('app_name') ?></title>
    <!-- Dynamic Bootstrap loading based on RTL/LTR -->
    <?php if ($isRtl): ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <?php else: ?>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?php endif; ?>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap');
        body { 
            background: linear-gradient(135deg, #0f172a, #1e3a8a); 
            min-height: 100vh; 
            font-family: 'Plus Jakarta Sans', sans-serif;
            display: flex;
            align-items: center;
        }
        .card { 
            border: 0; 
            border-radius: 1.5rem; 
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.3);
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }
        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: 0;
            border-radius: 10px;
            padding: 12px;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #2563eb, #1e40af);
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-4 col-md-6 col-sm-10">
            
            <!-- Language Quick Switcher -->
            <div class="text-center mb-3">
                <a href="/settings/set_lang?lang=<?= $locale === 'en' ? 'ar' : 'en' ?>" class="text-white-50 text-decoration-none small">
                    <i class="bi bi-globe me-1"></i><?= $locale === 'en' ? 'العربية (Arabic)' : 'English' ?>
                </a>
            </div>

            <div class="card p-4">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                            <i class="bi bi-car-front-fill fs-2"></i>
                        </div>
                        <h2 class="fw-bold mt-3 text-dark"><?= __('welcome_back') ?></h2>
                        <p class="text-muted text-sm"><?= __('sign_in_to') ?></p>
                    </div>

                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger border-0 rounded-3 text-sm" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?= htmlspecialchars($_SESSION['error']) ?>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="post" action="/login">
                        <?= Csrf::tokenField() ?>
                        <div class="mb-3">
                            <label class="form-label text-dark fw-medium"><?= __('email') ?></label>
                            <input type="email" class="form-control" name="email" value="admin@carstashen.com" placeholder="name@carstashen.com" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label text-dark fw-medium"><?= __('password') ?></label>
                            <input type="password" class="form-control" name="password" value="password123" placeholder="••••••••" required>
                        </div>
                        <button class="btn btn-primary w-100 shadow-sm"><?= __('login') ?></button>
                    </form>
                </div>
            </div>
            
            <div class="text-center mt-4">
                <small class="text-white-50">&copy; 2026 Car Stashen ERP. All rights reserved.</small>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
