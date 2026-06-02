<?php
// review.php - Player reviews and feedback
require_once 'db.php';

$success_msg = "";
$error_msg = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['player_name']) && isset($_POST['rating']) && isset($_POST['comment'])) {
    $player_name = trim($_POST['player_name']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);
    
    if (empty($player_name) || empty($comment) || $rating < 1 || $rating > 5) {
        $error_msg = "Please fill in all fields and select a valid rating.";
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("INSERT INTO `reviews` (`player_name`, `rating`, `comment`) VALUES (?, ?, ?)");
            $stmt->execute([$player_name, $rating, $comment]);
            
            // Redirect to avoid resubmission on page refresh
            header("Location: review.php?success=1");
            exit;
        } catch (Exception $e) {
            $error_msg = "Database error: " . $e->getMessage();
        }
    }
}

if (isset($_GET['success'])) {
    $success_msg = "Thank you! Your clearance review has been recorded in the database.";
}

// Fetch existing reviews
try {
    $db = getDB();
    $reviews = $db->query("SELECT * FROM `reviews` ORDER BY `created_at` DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $reviews_error = $e->getMessage();
    $reviews = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clearance Reviews - Escape Room</title>
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
                <li><a href="index.php">MISSION BRIEFING</a></li>
                <li><a href="leaderboard.php">LEADERBOARD</a></li>
                <li><a href="review.php" class="active">REVIEWS</a></li>
            </ul>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="container" style="flex-grow: 1;">
        
        <section class="briefing-section" style="margin-bottom: 3rem;">
            <span class="briefing-tag">-- Declassified Operative Feedback --</span>
            <h1 class="briefing-title font-stencil" style="font-size: 3rem;">MISSION REVIEWS</h1>
            <p class="briefing-desc">
                Read what other agents had to say about their mission experiences, or submit your own feedback.
            </p>
        </section>

        <!-- Notification Banner -->
        <?php if (!empty($success_msg)): ?>
            <div class="result-msg-box" style="border-color: var(--success-green); color: var(--success-green); margin-bottom: 2rem; max-width: 100%;">
                <?= htmlspecialchars($success_msg) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error_msg)): ?>
            <div class="result-msg-box" style="border-color: var(--danger-red); color: var(--danger-red); margin-bottom: 2rem; max-width: 100%;">
                <?= htmlspecialchars($error_msg) ?>
            </div>
        <?php endif; ?>

        <div class="reviews-layout">
            
            <!-- Left Side: Review Form -->
            <div class="review-form-panel">
                <h3 class="font-mono" style="margin-bottom: 1.5rem; letter-spacing: 0.1em; color: var(--war-gold);">DECODE FEEDBACK FORM</h3>
                
                <form action="review.php" method="POST">
                    <div class="form-group">
                        <label class="form-label" for="player_name">Your Name / Codename(s)</label>
                        <input type="text" class="form-control" name="player_name" id="player_name" placeholder="Enter your name(s)..." required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">Mission Rating</label>
                        <div class="star-rating-input">
                            <input type="radio" id="star5" name="rating" value="5" required><label for="star5">★</label>
                            <input type="radio" id="star4" name="rating" value="4"><label for="star4">★</label>
                            <input type="radio" id="star3" name="rating" value="3"><label for="star3">★</label>
                            <input type="radio" id="star2" name="rating" value="2"><label for="star2">★</label>
                            <input type="radio" id="star1" name="rating" value="1"><label for="star1">★</label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="comment">Debriefing Message</label>
                        <textarea class="form-control" name="comment" id="comment" rows="5" placeholder="Share your experience, puzzle details, or feedback..." required style="resize: vertical;"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-theme-war" style="width: 100%; justify-content: center; margin-top: 1rem;">
                        SUBMIT REPORT &rarr;
                    </button>
                </form>
            </div>
            
            <!-- Right Side: Reviews List -->
            <div class="reviews-list">
                <h3 class="font-mono" style="margin-bottom: 0.5rem; letter-spacing: 0.1em;">RECORDED AGENT LOGS</h3>
                
                <?php if (isset($reviews_error)): ?>
                    <div style="color: var(--danger-red); padding: 1rem;">
                        Could not retrieve reviews: <?= htmlspecialchars($reviews_error) ?>
                    </div>
                <?php elseif (empty($reviews)): ?>
                    <div style="color: var(--text-muted); font-family: 'Share Tech Mono', monospace; padding: 2rem; border: 1px dashed var(--border-color); border-radius: 8px; text-align: center;">
                        NO RECORDED DEBRIEFINGS FOUND. BE THE FIRST TO SUBMIT A LOG!
                    </div>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                        <div class="review-card-item">
                            <div class="review-card-header">
                                <span class="review-card-author"><?= htmlspecialchars($rev['player_name']) ?></span>
                                <div class="review-card-rating">
                                    <?php 
                                    $stars = intval($rev['rating']);
                                    for ($i = 1; $i <= 5; $i++) {
                                        if ($i <= $stars) {
                                            echo '★';
                                        } else {
                                            echo '<span style="color: var(--text-muted);">★</span>';
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            <p class="review-card-comment"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                            <span class="review-card-date"><?= date('Y-m-d H:i', strtotime($rev['created_at'])) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
        </div>

    </main>

    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="container" style="padding: 0 2rem;">
            <p class="footer-text">&copy; 2026 ESCAPE ROOM SPRINT 1. ALL RIGHTS RESERVED.</p>
        </div>
    </footer>

</body>
</html>
