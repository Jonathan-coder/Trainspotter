<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainspotter - Zugbeobachtung</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #333;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        header {
            background: rgba(255, 255, 255, 0.95);
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border-radius: 10px;
            margin-bottom: 2rem;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 2rem;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .logo span {
            color: #e74c3c;
        }

        nav ul {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        nav a {
            text-decoration: none;
            color: #2c3e50;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #e74c3c;
        }

        .hero {
            background: rgba(255, 255, 255, 0.95);
            padding: 3rem 2rem;
            border-radius: 15px;
            text-align: center;
            margin-bottom: 2rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }

        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #2c3e50;
        }

        .hero p {
            font-size: 1.2rem;
            color: #666;
            margin-bottom: 2rem;
        }

        .btn {
            display: inline-block;
            background: #e74c3c;
            color: white;
            padding: 12px 30px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s, transform 0.3s;
        }

        .btn:hover {
            background: #c0392b;
            transform: translateY(-2px);
        }

        .features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
            font-size: 1.5rem;
        }

        .feature-card p {
            color: #666;
            line-height: 1.6;
        }

        .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            color: #e74c3c;
        }

        footer {
            background: rgba(255, 255, 255, 0.95);
            padding: 2rem;
            border-radius: 10px;
            text-align: center;
            margin-top: 2rem;
            box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        }

        .stats {
            display: flex;
            justify-content: center;
            gap: 3rem;
            margin: 2rem 0;
        }

        .stat {
            text-align: center;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #e74c3c;
            display: block;
        }

        .stat-label {
            color: #666;
            font-size: 0.9rem;
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 1rem;
            }
            
            nav ul {
                gap: 1rem;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .stats {
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <div class="header-content">
                <div class="logo">Train<span>Spotter</span></div>
                <nav>
                    <ul>
                        <li><a href="#home">Startseite</a></li>
                        <li><a href="#about">Über uns</a></li>
                        <li><a href="#gallery">Galerie</a></li>
                        <li><a href="#contact">Kontakt</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <section class="hero" id="home">
            <h1>Willkommen bei TrainSpotter</h1>
            <p>Deine Plattform für Zugbeobachtungen, Fotos und Eisenbahn-Enthusiasmus</p>
            <a href="#gallery" class="btn">Zur Fotogalerie</a>
        </section>

        <div class="stats">
            <div class="stat">
                <span class="stat-number">1,247</span>
                <span class="stat-label">Zugbeobachtungen</span>
            </div>
            <div class="stat">
                <span class="stat-number">584</span>
                <span class="stat-label">Fotos</span>
            </div>
            <div class="stat">
                <span class="stat-number">89</span>
                <span class="stat-label">Aktive Mitglieder</span>
            </div>
        </div>

        <section class="features">
            <div class="feature-card">
                <div class="icon">📸</div>
                <h3>Fotogalerie</h3>
                <p>Entdecke atemberaubende Fotos von Zügen aus ganz Deutschland und Europa.</p>
            </div>
            
            <div class="feature-card">
                <div class="icon">📍</div>
                <h3>Hotspots</h3>
                <p>Finde die besten Orte für Zugbeobachtungen mit unseren detaillierten Spotting-Guides.</p>
            </div>
            
            <div class="feature-card">
                <div class="icon">👥</div>
                <h3>Community</h3>
                <p>Tausche dich mit anderen Eisenbahn-Enthusiasten aus und teile deine Beobachtungen.</p>
            </div>
        </section>

        <footer>
            <p>&copy; 2024 TrainSpotter - Die Plattform für Eisenbahn-Enthusiasten</p>
            <p>Made with ❤️ by Trainspotting Community</p>
        </footer>
    </div>

    <script>
        // Einfache Animation für die Statistik-Zahlen
        document.addEventListener('DOMContentLoaded', function() {
            const statNumbers = document.querySelectorAll('.stat-number');
            
            statNumbers.forEach(stat => {
                const target = parseInt(stat.textContent);
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        current = target;
                        clearInterval(timer);
                    }
                    stat.textContent = Math.floor(current).toLocaleString();
                }, 50);
            });
        });
    </script>
</body>
</html>