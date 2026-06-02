<?php
// index.php - Escape Room Home Page & Mission Briefing
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mission Briefing - Escape Room</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header Navigation -->
    <header class="site-header">
        <nav class="header-nav">
            <a href="index.php" class="nav-brand">
                <span style="color: var(--war-gold);">[</span> ESCAPE ROOM <span style="color: var(--space-purple);">]</span>
            </a>
            <ul class="nav-links">
                <li><a href="index.php" class="active">MISSION BRIEFING</a></li>
                <li><a href="leaderboard.php">LEADERBOARD</a></li>
                <li><a href="review.php">REVIEWS</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content Container -->
    <main class="container">
        
        <!-- Mission Briefing Title & Summary -->
        <section class="briefing-section">
            <span class="briefing-tag">-- Mission Briefing --</span>
            <h1 class="briefing-title font-stencil">ESCAPE ROOM</h1>
            <p class="briefing-desc">
                Choose your mission. Solve the puzzles. Escape before time runs out.
                Each room holds secrets — only the sharpest minds break free.
            </p>
            
            <!-- Global Stats Bar -->
            <div class="mission-stats-panel">
                <div class="mission-stat-item">
                    <span class="mission-stat-icon">⏱</span>
                    <span class="mission-stat-val">60 MIN</span>
                    <span class="mission-stat-lbl">Time Limit</span>
                </div>
                <div class="mission-stat-item">
                    <span class="mission-stat-icon">🚪</span>
                    <span class="mission-stat-val">2 ROOMS</span>
                    <span class="mission-stat-lbl">Available</span>
                </div>
                <div class="mission-stat-item">
                    <span class="mission-stat-icon">⭐</span>
                    <span class="mission-stat-val">5 STARS</span>
                    <span class="mission-stat-lbl">Max Rating</span>
                </div>
                <div class="mission-stat-item">
                    <span class="mission-stat-icon">⚡</span>
                    <span class="mission-stat-val">EXTREME</span>
                    <span class="mission-stat-lbl">Difficulty</span>
                </div>
            </div>
        </section>

        <!-- Room Cards Grid -->
        <section class="rooms-grid">
            
            <!-- Room 1: Theater of War -->
            <article class="room-card theme-war">
                <div class="card-corner card-corner-tl"></div>
                <div class="card-corner card-corner-br"></div>
                
                <div class="card-visual-header">
                    <span class="card-emblem">⚔️</span>
                    <span class="card-subtitle">Sector 7 - Classified</span>
                    <span class="card-badge">CLASSIFIED</span>
                </div>
                
                <div class="card-body">
                    <div class="card-meta-row">
                        <span class="card-number">ROOM 01</span>
                        <div class="card-difficulty" style="color: var(--war-gold);">
                            <div class="difficulty-stars">
                                <span class="star-dot filled"></span>
                                <span class="star-dot filled"></span>
                                <span class="star-dot filled"></span>
                                <span class="star-dot filled"></span>
                                <span class="star-dot"></span>
                            </div>
                            <span class="difficulty-text">HARD</span>
                        </div>
                    </div>
                    
                    <h2 class="card-title">Theater of War</h2>
                    <p class="card-description">
                        You are trapped in a war bunker. Decode military ciphers, disarm booby traps, 
                        and transmit the escape signal before the enemy breaches the perimeter.
                    </p>
                    
                    <div class="card-tags">
                        <span class="tag">CIPHERS</span>
                        <span class="tag">EXPLOSIVES</span>
                        <span class="tag">RADIO</span>
                        <span class="tag">MAPS</span>
                    </div>
                    
                    <div class="card-footer">
                        <span class="player-count">1-2 Players</span>
                        <button class="btn btn-theme-war enter-room-btn" data-room="Theater of War" data-theme="war">
                            ENTER ROOM &rarr;
                        </button>
                    </div>
                </div>
            </article>
            
            <!-- Room 2: Void Protocol -->
            <article class="room-card theme-space">
                <div class="card-corner card-corner-tl"></div>
                <div class="card-corner card-corner-br"></div>
                
                <div class="card-visual-header">
                    <span class="card-emblem">🌌</span>
                    <span class="card-subtitle">Deep Space - Unknown</span>
                    <span class="card-badge">UNCHARTED</span>
                </div>
                
                <div class="card-body">
                    <div class="card-meta-row">
                        <span class="card-number">ROOM 02</span>
                        <div class="card-difficulty" style="color: var(--space-purple);">
                            <div class="difficulty-stars">
                                <span class="star-dot filled"></span>
                                <span class="star-dot filled"></span>
                                <span class="star-dot filled"></span>
                                <span class="star-dot filled"></span>
                                <span class="star-dot filled"></span>
                            </div>
                            <span class="difficulty-text">EXTREME</span>
                        </div>
                    </div>
                    
                    <h2 class="card-title">Void Protocol</h2>
                    <p class="card-description">
                        Your spacecraft has drifted into an anomaly. Realign the star charts, 
                        restore warp core sequences, and open the escape pod hatch before oxygen levels reach zero.
                    </p>
                    
                    <div class="card-tags">
                        <span class="tag">NAVIGATION</span>
                        <span class="tag">QUANTUM</span>
                        <span class="tag">OXYGEN</span>
                        <span class="tag">WARP</span>
                    </div>
                    
                    <div class="card-footer">
                        <span class="player-count">1-2 Players</span>
                        <button class="btn btn-theme-space enter-room-btn" data-room="Void Protocol" data-theme="space">
                            ENTER ROOM &rarr;
                        </button>
                    </div>
                </div>
            </article>
            
        </section>
    </main>

    <!-- Registration Modal Overlay -->
    <div class="modal-overlay" id="registrationModal">
        <div class="modal-content" id="modalContent">
            <button class="modal-close" id="modalClose">&times;</button>
            
            <h2 class="modal-title font-mono" id="modalTitle">INITIATE MISSION</h2>
            <p class="modal-desc">Configure player clearance before entering the lockdown chamber.</p>
            
            <form action="game_sim.php" method="POST" id="startMissionForm">
                <input type="hidden" name="room_name" id="modalRoomInput">
                
                <div class="form-group">
                    <label class="form-label" for="player1">Player 1 Name (Required)</label>
                    <input type="text" class="form-control" name="player1" id="player1" placeholder="Enter codename or name..." required>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="player2">Player 2 Name (Optional)</label>
                    <input type="text" class="form-control" name="player2" id="player2" placeholder="Leave empty for single player...">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" id="btnCancel">CANCEL</button>
                    <button type="submit" class="btn btn-primary" id="btnStart">START MISSION</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="container" style="padding: 0 2rem;">
            <p class="footer-text">&copy; 2026 ESCAPE ROOM SPRINT 1. ALL RIGHTS RESERVED.</p>
        </div>
    </footer>

    <!-- JS to handle modal and theme changes -->
    <script>
        const modal = document.getElementById('registrationModal');
        const modalContent = document.getElementById('modalContent');
        const modalTitle = document.getElementById('modalTitle');
        const modalRoomInput = document.getElementById('modalRoomInput');
        const btnStart = document.getElementById('btnStart');
        const enterRoomBtns = document.querySelectorAll('.enter-room-btn');
        const modalClose = document.getElementById('modalClose');
        const btnCancel = document.getElementById('btnCancel');

        enterRoomBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const roomName = btn.getAttribute('data-room');
                const theme = btn.getAttribute('data-theme');
                
                modalRoomInput.value = roomName;
                modalTitle.textContent = `START MISSION: ${roomName.toUpperCase()}`;
                
                // Reset classes
                modalContent.className = 'modal-content';
                btnStart.className = 'btn';
                
                // Apply theme styles
                if (theme === 'war') {
                    modalContent.classList.add('war-modal');
                    btnStart.classList.add('btn-theme-war');
                } else {
                    modalContent.classList.add('space-modal');
                    btnStart.classList.add('btn-theme-space');
                }
                
                modal.style.display = 'flex';
                document.getElementById('player1').focus();
            });
        });

        function closeModal() {
            modal.style.display = 'none';
            document.getElementById('startMissionForm').reset();
        }

        modalClose.addEventListener('click', closeModal);
        btnCancel.addEventListener('click', closeModal);
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                closeModal();
            }
        });
    </script>
</body>
</html>