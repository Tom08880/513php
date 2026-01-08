<?php
// File: about.php

session_start();

// Include required files
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/header.php';
?>

<style>
/* About page specific styles */
.about-hero {
    background: linear-gradient(rgba(45, 90, 39, 0.85), rgba(45, 90, 39, 0.85));
    color: white;
    text-align: center;
    padding: 4rem 1rem;
    margin-bottom: 2rem;
}

.about-hero h1 {
    font-size: 2.5rem;
    margin-bottom: 1rem;
}

.section-padding {
    padding: 3rem 0;
}

.section-header {
    text-align: center;
    margin-bottom: 2rem;
}

.section-tag {
    display: inline-block;
    color: #2d5a27;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.9rem;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
}

.section-header h2 {
    font-size: 2rem;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.overview-content {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 3rem;
    align-items: center;
}

.overview-text h2 {
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    color: #2c3e50;
}

.overview-text p {
    margin-bottom: 1.5rem;
    line-height: 1.8;
    color: #666;
}

.responsive-img {
    width: 100%;
    height: auto;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}

.bg-eco-light {
    background-color: #f5f9f6;
}

.mission-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
}

.mission-card {
    background: white;
    padding: 2rem;
    border-radius: 12px;
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.08);
}

.mission-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.15);
}

.mission-icon {
    font-size: 3rem;
    margin-bottom: 1.5rem;
}

.mission-card h3 {
    margin-bottom: 1rem;
    color: #2c3e50;
    font-size: 1.3rem;
}

.mission-card p {
    color: #666;
    line-height: 1.7;
}

.practices-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
}

.practice-item {
    text-align: center;
}

.practice-image-container {
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 1.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.practice-img {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.practice-item:hover .practice-img {
    transform: scale(1.05);
}

.practice-item h3 {
    margin-bottom: 1rem;
    color: #2c3e50;
    font-size: 1.3rem;
}

.practice-item p {
    color: #666;
    line-height: 1.7;
}

.bg-eco-dark {
    background-color: #2d5a27;
    color: white;
    padding: 3rem 0;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 2rem;
    text-align: center;
}

.stat-item {
    padding: 1.5rem;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 1rem;
    opacity: 0.9;
}

.team-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 2rem;
}

.team-member {
    text-align: center;
}

.member-image {
    border-radius: 50%;
    overflow: hidden;
    width: 180px;
    height: 180px;
    margin: 0 auto 1.5rem;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.team-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.team-member h3 {
    margin-bottom: 0.5rem;
    color: #2c3e50;
    font-size: 1.2rem;
}

.team-member p {
    color: #666;
    font-style: italic;
}

.cta-section {
    background: linear-gradient(rgba(45, 90, 39, 0.9), rgba(45, 90, 39, 0.9));
    color: white;
    text-align: center;
    padding: 4rem 0;
}

.cta-content {
    max-width: 800px;
    margin: 0 auto;
}

.cta-section h2 {
    font-size: 2rem;
    margin-bottom: 1.5rem;
}

.cta-section p {
    font-size: 1.1rem;
    margin-bottom: 2rem;
    opacity: 0.9;
}

.cta-buttons {
    display: flex;
    gap: 1.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.btn {
    display: inline-block;
    padding: 0.8rem 1.5rem;
    background-color: #2d5a27;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: background-color 0.3s;
    text-align: center;
}

.btn:hover {
    background-color: #1e4023;
}

.btn-primary {
    background-color: #2d5a27;
}

.btn-primary:hover {
    background-color: #1e4023;
}

.btn-outline {
    background: transparent;
    color: #2d5a27;
    border: 1px solid #2d5a27;
}

.btn-outline:hover {
    background-color: #2d5a27;
    color: white;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Responsive design */
@media (max-width: 768px) {
    .about-hero h1 {
        font-size: 2rem;
    }

    .overview-content {
        grid-template-columns: 1fr;
        gap: 2rem;
    }

    .section-header h2 {
        font-size: 1.8rem;
    }

    .mission-cards,
    .practices-grid,
    .stats-grid,
    .team-grid {
        grid-template-columns: 1fr;
    }

    .cta-buttons {
        flex-direction: column;
        align-items: center;
    }

    .member-image {
        width: 150px;
        height: 150px;
    }
}

@media (max-width: 480px) {
    .about-hero h1 {
        font-size: 1.8rem;
    }

    .mission-card,
    .practice-item {
        padding: 1.5rem 1rem;
    }

    .stat-number {
        font-size: 2rem;
    }
}
</style>

<div class="about-page">
    <!-- Hero Section -->
    <section class="about-hero">
        <div class="container">
            <div class="hero-content">
                <h1>Our Eco-Friendly Mission</h1>
                <p>Creating sustainable products from recycled materials to protect our planet</p>
            </div>
        </div>
    </section>

    <!-- About Overview -->
    <section id="our-story" class="about-overview section-padding">
        <div class="container">
            <div class="overview-content">
                <div class="overview-text">
                    <span class="section-tag">Our Story</span>
                    <h2>Who We Are</h2>
                    <p>EcoStore was founded in 2018 with a simple mission: to reduce waste and promote sustainability through high-quality recycled products.</p>
                    <p>We believe that everyday choices matter. By choosing recycled products, you're not just making a purchase – you're helping to create a circular economy that respects our planet's resources.</p>
                    <p>Our team of designers and craftspeople work tirelessly to transform waste materials into beautiful, functional products that enhance your life while protecting the environment.</p>
                </div>
                <div class="overview-image">
                    <img src="https://miaobi-lite.bj.bcebos.com/miaobi/5mao/b%275oKJ5bC86ams5LiB5bm/5Zy6XzE3MzMwMjU1NzUuMDI5OTEy%27/0.png" alt="EcoStore team working" class="responsive-img">
                </div>
            </div>
        </div>
    </section>

    <!-- Our Mission -->
    <section class="our-mission section-padding bg-eco-light">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Our Values</span>
                <h2>Our Mission & Values</h2>
            </div>
            <div class="mission-cards">
                <div class="mission-card">
                    <div class="mission-icon">♻️</div>
                    <h3>Zero Waste</h3>
                    <p>We're committed to diverting waste from landfills by giving materials a second life through our products.</p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">🌱</div>
                    <h3>Sustainability</h3>
                    <p>Every decision we make considers its environmental impact, from sourcing to packaging.</p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">🛡️</div>
                    <h3>Transparency</h3>
                    <p>We're open about our materials, manufacturing processes, and environmental footprint.</p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">👥</div>
                    <h3>Community</h3>
                    <p>We work with local communities to collect recyclables and create sustainable livelihoods.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Sustainability Practices -->
    <section class="sustainability-practices section-padding">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Our Practices</span>
                <h2>Our Sustainability Practices</h2>
            </div>
            <div class="practices-grid">
                <div class="practice-item">
                    <div class="practice-image-container">
                        <img src="https://picsum.photos/seed/material-sourcing/400/300" alt="Responsible material sourcing" class="practice-img">
                    </div>
                    <h3>Responsible Sourcing</h3>
                    <p>We collect materials from verified suppliers who follow ethical and environmental standards.</p>
                </div>
                <div class="practice-item">
                    <div class="practice-image-container">
                        <img src="https://picsum.photos/seed/energy-efficient/400/300" alt="Energy efficient production" class="practice-img">
                    </div>
                    <h3>Energy Efficiency</h3>
                    <p>Our production facilities run on renewable energy to minimize our carbon footprint.</p>
                </div>
                <div class="practice-item">
                    <div class="practice-image-container">
                        <img src="https://picsum.photos/seed/zero-packaging/400/300" alt="Zero waste packaging" class="practice-img">
                    </div>
                    <h3>Plastic-Free Packaging</h3>
                    <p>All our products are shipped in biodegradable or recyclable packaging materials.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Impact Stats -->
    <section class="impact-stats section-padding bg-eco-dark">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Our Impact</span>
                <h2>Our Environmental Impact</h2>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-number">120</div>
                    <div class="stat-label">Tons of waste diverted from landfills</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">85%</div>
                    <div class="stat-label">Reduction in carbon footprint</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">50,000</div>
                    <div class="stat-label">Trees saved through recycled paper</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">3M</div>
                    <div class="stat-label">Liters of water conserved</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section class="our-team section-padding">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Meet Us</span>
                <h2>Meet Our Team</h2>
            </div>
            <div class="team-grid">
                <div class="team-member">
                    <div class="member-image">
                        <img src="https://picsum.photos/seed/team-leader/300/300" alt="Emma Rodriguez - Founder" class="team-img">
                    </div>
                    <h3>Emma Rodriguez</h3>
                    <p>Founder & CEO</p>
                </div>
                <div class="team-member">
                    <div class="member-image">
                        <img src="https://picsum.photos/seed/design-director/300/300" alt="Michael Chen - Design Director" class="team-img">
                    </div>
                    <h3>Michael Chen</h3>
                    <p>Design Director</p>
                </div>
                <div class="team-member">
                    <div class="member-image">
                        <img src="https://picsum.photos/seed/sustainability-manager/300/300" alt="Sophia Williams - Sustainability Manager" class="team-img">
                    </div>
                    <h3>Sophia Williams</h3>
                    <p>Sustainability Manager</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to Action -->
    <section class="cta-section">
        <div class="container cta-content">
            <h2>Join Our Sustainable Movement</h2>
            <p>Be part of the solution - together we can create a more sustainable future.</p>
            <div class="cta-buttons">
                <a href="<?php echo BASE_URL; ?>products/" class="btn btn-primary">Shop Sustainable Products</a>
                <a href="<?php echo BASE_URL; ?>contact.php" class="btn btn-outline">Get Involved</a>
            </div>
        </div>
    </section>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>