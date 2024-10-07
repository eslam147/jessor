<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jessor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #5d5fef;
            --secondary-color: #ffeaea;
            --text-color: #333;
        }

        body {
            font-family: 'Arial', sans-serif;
            color: var(--text-color);
        }

        .navbar {
            background-color: #fff;
            padding: 1rem 0;
        }

        .navbar-brand {
            font-weight: bold;
            color: var(--primary-color);
        }

        .nav-link {
            color: var(--text-color);
        }

        .btn-contact {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .hero {
            background-color: #fff;
            padding: 4rem 0;
        }

        .hero h1 {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .hero p {
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .btn-primary {
            background-color: var(--secondary-color);
            color: var(--primary-color);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .btn-secondary {
            background-color: #fff;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 20px;
        }

        .trusted-by {
            background-color: #f8f9fa;
            padding: 2rem 0;
        }

        .features {
            padding: 4rem 0;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Jessor</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Pricing</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Blog</a></li>
                    <li class="nav-item"><a class="nav-link btn-contact" href="#">Contact Now</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1>Launch a software businesses website today with us!</h1>
                    <p>Launch a business today with our help and get it done with amazing launch features, websites and
                        more with Jessor. We help businesses like yours thrive every day and beyond.</p>
                    <button class="btn btn-primary me-2">Contact Now</button>
                    <button class="btn btn-secondary">Book a Demo Today</button>
                    <div class="mt-3">
                        <small><img src="/api/placeholder/20/20" alt="Star icon" class="me-1"> Rated 4.9 out of 5000+
                            reviews</small>
                    </div>
                </div>
                <div class="col-md-6">
                    <img src="/api/placeholder/500/400" alt="Woman with laptop" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <section class="trusted-by">
        <div class="container text-center">
            <h5>Trusted by over 100+ businesses worldwide!</h5>
            <div class="row mt-4">
                <!-- Add placeholder logos here -->
                <div class="col"><img src="/api/placeholder/100/50" alt="Company logo"></div>
                <div class="col"><img src="/api/placeholder/100/50" alt="Company logo"></div>
                <div class="col"><img src="/api/placeholder/100/50" alt="Company logo"></div>
                <div class="col"><img src="/api/placeholder/100/50" alt="Company logo"></div>
                <div class="col"><img src="/api/placeholder/100/50" alt="Company logo"></div>
            </div>
        </div>
    </section>

    <!-- Additional sections would go here -->

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
