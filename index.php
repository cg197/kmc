<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kabwe Municipal Council | Official Portal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --secondary: #0f172a;
            --accent: #f59e0b;
            --white: #ffffff;
            --bg-light: #f8fafc;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { color: var(--secondary); background-color: var(--white); line-height: 1.6; }

        /* --- Navigation --- */
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 8%;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
            transition: 0.3s;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .logo { font-size: 1.5rem; font-weight: 800; color: var(--primary); display: flex; align-items: center; gap: 10px; }
        .nav-links { display: flex; gap: 30px; list-style: none; }
        .nav-links a { text-decoration: none; color: var(--secondary); font-weight: 600; font-size: 0.9rem; transition: 0.2s; }
        .nav-links a:hover { color: var(--primary); }

        .btn-login { background: var(--primary); color: white !important; padding: 10px 24px; border-radius: 8px; transition: 0.3s; }
        .btn-login:hover { background: var(--primary-dark); transform: translateY(-2px); }

        .hero {
            height: 85vh;
            background: linear-gradient(rgba(15, 23, 42, 0.7), rgba(15, 23, 42, 0.7)), url('https://images.unsplash.com/photo-1449824913935-59a10b8d2000?auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: white;
            padding: 0 20px;
        }

        .hero h1 { font-size: 3.5rem; font-weight: 900; margin-bottom: 20px; max-width: 800px; }
        .hero p { font-size: 1.2rem; margin-bottom: 40px; opacity: 0.9; max-width: 600px; }

        .hero-btns { display: flex; gap: 20px; }
        .btn-main { padding: 15px 35px; border-radius: 10px; font-weight: 700; text-decoration: none; transition: 0.3s; }
        .btn-filled { background: var(--primary); color: white; }
        .btn-outline { border: 2px solid white; color: white; }
        .btn-outline:hover { background: white; color: var(--secondary); }

        .services { padding: 100px 8%; background: var(--bg-light); text-align: center; }
        .section-title { font-size: 2.2rem; margin-bottom: 50px; }

        .service-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
        }

        .service-item {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            transition: 0.3s;
            text-decoration: none;
            color: inherit;
        }

        .service-item:hover { transform: translateY(-10px); }
        .service-item i { font-size: 2.5rem; color: var(--primary); margin-bottom: 20px; }
        .service-item h3 { margin-bottom: 15px; }

        /* --- Stats Section --- */
        .stats { padding: 80px 8%; background: var(--secondary); color: white; display: flex; justify-content: space-around; text-align: center; }
        .stat-box h2 { font-size: 3rem; color: var(--accent); }

        /* --- Footer --- */
        footer { padding: 80px 8% 40px; background: #0a0f1d; color: #94a3b8; }
        .footer-grid { display: grid; grid-template-columns: 2fr 1fr 1fr 1fr; gap: 50px; margin-bottom: 50px; }
        .footer-col h4 { color: white; margin-bottom: 20px; }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 10px; }

        .copyright { text-align: center; padding-top: 40px; border-top: 1px solid rgba(255,255,255,0.1); font-size: 0.8rem; }
    </style>
</head>
<body>

    <nav>
        <div class="logo">
            <i class="fas fa-landmark"></i> KMC ZAMBIA
        </div>
        <ul class="nav-links">
            <li><a href="#home">Home</a></li>
            <li><a href="#services">Services</a></li>
            <li><a href="#contact">About Council</a></li>
            <li><a href="#news">Public Notices</a></li>
            <li><a href="log_in.php" class="btn-login">Citizen Portal</a></li>

        </ul>
    </nav>

    <section class="hero" id="home">
        <h1>Building a Modern, Sustainable Kabwe</h1>
        <p>Access council services, pay rates, and apply for permits from the comfort of your home.</p>
        <div class="hero-btns">
            <a href="log_in.php" class="btn-main btn-filled">Get Started</a>
            <a href="#services" class="btn-main btn-outline">Explore Services</a>
        </div>
    </section>

    <section class="services" id="services">
        <h2 class="section-title">Digital Citizen Services</h2>
        <div class="service-grid">
            <a href="#" class="service-item">
                <i class="fas fa-file-invoice-dollar"></i>
                <h3>Pay Rates</h3>
                <p>Fast and secure payment for property rates and ground rent.</p>
            </a>
            <a href="#" class="service-item">
                <i class="fas fa-tools"></i>
                <h3>Permits</h3>
                <p>Apply for building, trading, and fire safety certificates online.</p>
            </a>
            <a href="#" class="service-item">
                <i class="fas fa-bullhorn"></i>
                <h3>Report Issue</h3>
                <p>Notify the council about waste, potholes, or water issues.</p>
            </a>
            <a href="#" class="service-item">
                <i class="fas fa-map-marked-alt"></i>
                <h3>Urban Planning</h3>
                <p>View city plans, land surveys, and developmental projects.</p>
            </a>
        </div>
    </section>

    <section class="stats">
        <div class="stat-box">
            <h2>24/7</h2>
            <p>Online Access</p>
        </div>
        <div class="stat-box">
            <h2>15k+</h2>
            <p>Registered Citizens</p>
        </div>
        <div class="stat-box">
            <h2>48h</h2>
            <p>Average Response Time</p>
        </div>
    </section>

    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <h4 style="color: white; font-weight: 800;">KMC</h4>
                <p>Official Digital Gateway for the Kabwe Municipal Council. Serving the community through innovation and transparency.</p>
            </div>
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul>
                    <li>Official Gazettes</li>
                    <li>Council Minutes</li>
                    <li>By-Laws</li>
                    <li>Tenders</li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Departments</h4>
                <ul>
                    <li>Engineering</li>
                    <li>Public Health</li>
                    <li>Finance</li>
                    <li>Administration</li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Contact</h4>
                <p><i class="fas fa-phone"></i> +260 215 123456</p>
                <p><i class="fas fa-envelope"></i> info@kmc.gov.zm</p>
                <p><i class="fas fa-map-marker-alt"></i> Civic Center, Kabwe</p>
            </div>
        </div>
        <div class="copyright">
            &copy; 2026 Kabwe Municipal Council. All Rights Reserved. <br>
            Designed for the progress of Zambia.
        </div>
    </footer>

    <script>
        // Smooth scroll for nav links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html>