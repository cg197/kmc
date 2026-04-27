<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Kabwe Municipal Council</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #0f172a;
            --bg-light: #f8fafc;
            --white: #ffffff;
            --border: #e2e8f0;
            --text: #1e293b;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Inter', sans-serif; }
        body { background: var(--bg-light); color: var(--text); line-height: 1.6; }

        /* --- Header & Navigation --- */
        header { background: var(--white); border-bottom: 1px solid var(--border); padding: 1.5rem 8%; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
        .logo { font-size: 1.4rem; font-weight: 800; color: var(--primary); text-decoration: none; display: flex; align-items: center; gap: 10px; }
        .nav-links a { text-decoration: none; color: var(--text); font-weight: 600; font-size: 0.9rem; margin-left: 25px; }
        .btn-portal { background: var(--primary); color: white !important; padding: 10px 20px; border-radius: 8px; }

        /* --- Hero Section --- */
        .hero { background: var(--secondary); color: white; padding: 80px 8%; text-align: center; }
        .hero h1 { font-size: 2.5rem; margin-bottom: 15px; }
        .hero p { opacity: 0.8; font-size: 1.1rem; max-width: 600px; margin: 0 auto; }

        /* --- Layout --- */
        .container { max-width: 1200px; margin: -50px auto 50px; padding: 0 20px; display: grid; grid-template-columns: 1fr 2fr; gap: 30px; }

        /* --- Contact Info Sidebar --- */
        .info-sidebar { background: var(--white); padding: 30px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .info-group { margin-bottom: 30px; }
        .info-group h3 { font-size: 0.9rem; text-transform: uppercase; color: var(--primary); letter-spacing: 1px; margin-bottom: 15px; }
        .contact-method { display: flex; align-items: flex-start; gap: 15px; margin-bottom: 20px; }
        .contact-method i { margin-top: 5px; color: var(--primary); }
        .contact-method p { font-size: 0.95rem; font-weight: 600; }
        .contact-method span { display: block; font-size: 0.85rem; color: #64748b; font-weight: 400; }

        /* --- Contact Form --- */
        .form-container { background: var(--white); padding: 40px; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; }
        .input-group label { display: block; font-size: 0.85rem; font-weight: 700; margin-bottom: 8px; }
        .input-group input, .input-group select, .input-group textarea {
            width: 100%; padding: 12px; border: 1px solid var(--border); border-radius: 10px; outline: none; font-size: 1rem; transition: 0.3s;
        }
        .input-group input:focus, .input-group textarea:focus { border-color: var(--primary); box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.05); }
        
        .btn-submit { background: var(--primary); color: white; border: none; width: 100%; padding: 15px; border-radius: 10px; font-weight: 700; cursor: pointer; font-size: 1rem; }
        .btn-submit:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(37, 99, 235, 0.3); }

        /* --- Map Placeholder --- */
        .map-box { height: 250px; background: #e5e7eb; border-radius: 15px; margin-top: 30px; display: flex; align-items: center; justify-content: center; color: #9ca3af; overflow: hidden; border: 1px solid var(--border); }

        @media (max-width: 900px) {
            .container { grid-template-columns: 1fr; margin-top: 30px; }
            .form-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="logo"><i class="fas fa-landmark"></i> KMC</a>
        <nav class="nav-links">
            <a href="index.php">Home</a>
            <a href="services.php">Services</a>
            <a href="contacts.php">Contact</a>
            <a href="user_dashboard.php" class="btn-portal">Citizen Portal</a>
        </nav>
    </header>

    <section class="hero">
        <h1>Get In Touch</h1>
        <p>Whether you have a query about property rates or want to report a municipal issue, our team is here to help.</p>
    </section>

    <div class="container">
        <aside class="info-sidebar">
            <div class="info-group">
                <h3>Our Office</h3>
                <div class="contact-method">
                    <i class="fas fa-map-marker-alt"></i>
                    <p>Civic Center, Kabwe<span>Plot 1, Independence Avenue, Kabwe, Zambia</span></p>
                </div>
            </div>

            <div class="info-group">
                <h3>Direct Lines</h3>
                <div class="contact-method">
                    <i class="fas fa-phone-alt"></i>
                    <p>+260 215 222123<span>General Enquiries</span></p>
                </div>
                <div class="contact-method">
                    <i class="fas fa-envelope"></i>
                    <p>info@kmc.gov.zm<span>Support Email</span></p>
                </div>
            </div>

            <div class="info-group">
                <h3>Working Hours</h3>
                <div class="contact-method">
                    <i class="fas fa-clock"></i>
                    <p>Mon - Fri: 08:00 - 17:00<span>Closed Weekends & Holidays</span></p>
                </div>
            </div>

            <div class="map-box">
                <p><i class="fas fa-map-marked-alt"></i> Interactive Map Loading...</p>
            </div>
        </aside>

        <main class="form-container">
            <h2 style="margin-bottom: 10px;">Send us a message</h2>
            <p style="color: #64748b; margin-bottom: 30px;">Complete the form below and the relevant department will contact you within 48 hours.</p>
            <form id="publicContactForm">
                <div class="form-grid">
                    <div class="input-group">
                        <label>Full Name</label>
                        <input type="text" placeholder="e.g. Mwansa Phiri" required>
                    </div>
                    <div class="input-group">
                        <label>Email Address</label>
                        <input type="email" placeholder="mwansa@example.com" required>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="input-group">
                        <label>Phone Number</label>
                        <input type="tel" placeholder="+260 9xx xxxxxx">
                    </div>
                    <div class="input-group">
                        <label>Inquiry Category</label>
                        <select>
                            <option>Property Rates</option>
                            <option>Waste Management</option>
                            <option>Building Permits</option>
                            <option>Public Health</option>
                            <option>General Feedback</option>
                        </select>
                    </div>
                </div>

                <div class="input-group" style="margin-bottom: 25px;">
                    <label>Your Message</label>
                    <textarea rows="6" placeholder="How can we help you?"></textarea>
                </div>

                <button type="submit" class="btn-submit">Send Inquiry</button>
            </form>
        </main>
        <a href="user_dashboard.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> back</a>
    </div>

    <script>
        document.getElementById('publicContactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.querySelector('.btn-submit');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            //  API call
            setTimeout(() => {
                btn.innerHTML = '<i class="fas fa-check"></i> Message Sent!';
                btn.style.background = '#10b981';
                this.reset();
                setTimeout(() => {
                    btn.innerHTML = 'Send Inquiry';
                    btn.style.background = '#2563eb';
                }, 3000);
            }, 1500);
        });
    </script>
</body>
</html>