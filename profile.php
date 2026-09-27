<?php
session_start();

$is_logged_in = isset($_SESSION['email']);
$user_name = $is_logged_in ? ($_SESSION['full_name'] ?? 'MindEase Friend') : 'MindEase Guest';
$user_email = $is_logged_in ? $_SESSION['email'] : '';
$initial = !empty($user_name) ? strtoupper(substr($user_name, 0, 1)) : 'M';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daily Progress & Profile | MindEase Safe Space</title>
  <meta name="description" content="View your daily emotional progress, mindfulness streaks, 7-day mood tracker, and completed self-care exercises on MindEase.">

  <!-- MindEase Design System Styles -->
  <link rel="stylesheet" href="css/style.css?v=botanical-sitewide-4">
  <style>
    /* Scoped Profile & Progress Enhancements */
    .profile-hero {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1.5rem;
      flex-wrap: wrap;
      padding: 2rem;
      margin-bottom: 2rem;
    }

    .profile-avatar-group {
      display: flex;
      align-items: center;
      gap: 1.25rem;
    }

    .profile-avatar {
      width: 72px;
      height: 72px;
      border-radius: 50%;
      background: var(--gradient-pink-purple);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 2rem;
      font-weight: 700;
      color: #FFF;
      box-shadow: var(--glow-purple);
      border: 3px solid rgba(255, 255, 255, 0.2);
    }

    .profile-meta h1 {
      font-size: 1.6rem;
      font-weight: 700;
      margin-bottom: 0.25rem;
    }

    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1.25rem;
      margin-bottom: 2.5rem;
    }

    .stat-card {
      padding: 1.5rem;
      display: flex;
      flex-direction: column;
      gap: 0.5rem;
      position: relative;
      overflow: hidden;
    }

    .stat-card .stat-icon {
      font-size: 1.75rem;
      margin-bottom: 0.25rem;
    }

    .stat-card .stat-val {
      font-size: 2rem;
      font-weight: 700;
      line-height: 1.1;
      font-family: var(--font-family-heading);
    }

    .stat-card .stat-label {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    /* Habit Checklist */
    .habit-list {
      display: flex;
      flex-direction: column;
      gap: 1rem;
    }

    .habit-item {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: 1.2rem 1.4rem;
      background: rgba(18, 34, 42, 0.7);
      border: 1px solid var(--bg-glass-border);
      border-radius: var(--radius-md);
      transition: all var(--transition-fast);
      cursor: pointer;
    }

    .habit-item:hover {
      background: rgba(28, 54, 65, 0.8);
      border-color: rgba(202, 231, 236, 0.3);
      transform: translateY(-2px);
    }

    .habit-item.completed {
      background: rgba(40, 75, 70, 0.35);
      border-color: rgba(134, 239, 172, 0.35);
    }

    .habit-item.completed .habit-title {
      text-decoration: line-through;
      color: var(--text-muted);
    }

    .habit-checkbox {
      width: 26px;
      height: 26px;
      border-radius: 8px;
      border: 2px solid var(--purple-soft);
      display: flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      transition: all var(--transition-fast);
      background: rgba(255, 255, 255, 0.05);
    }

    .habit-item.completed .habit-checkbox {
      background: #34d399;
      border-color: #34d399;
      color: #0d1820;
    }

    .habit-info {
      flex: 1;
    }

    .habit-title {
      font-size: 1.05rem;
      font-weight: 600;
      margin-bottom: 0.2rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .habit-desc {
      font-size: 0.85rem;
      color: var(--text-muted);
    }

    .habit-action-link {
      font-size: 0.825rem;
      color: var(--blue-soft);
      padding: 0.35rem 0.75rem;
      border-radius: var(--radius-full);
      background: rgba(169, 216, 231, 0.12);
      border: 1px solid rgba(169, 216, 231, 0.25);
      transition: all var(--transition-fast);
      white-space: nowrap;
    }

    .habit-action-link:hover {
      background: rgba(169, 216, 231, 0.25);
      color: #FFF;
    }

    /* Weekly Timeline */
    .weekly-timeline {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 0.75rem;
      margin-top: 1rem;
    }

    .timeline-day {
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
      padding: 1rem 0.5rem;
      background: rgba(18, 34, 42, 0.6);
      border: 1px solid var(--bg-glass-border);
      border-radius: var(--radius-md);
      text-align: center;
      transition: all var(--transition-fast);
    }

    .timeline-day.is-today {
      background: rgba(197, 180, 223, 0.18);
      border-color: var(--purple-soft);
      box-shadow: var(--glow-purple);
    }

    .timeline-name {
      font-size: 0.8rem;
      font-weight: 600;
      color: var(--text-muted);
    }

    .timeline-date {
      font-size: 0.75rem;
      color: var(--text-subtle);
    }

    .timeline-emoji {
      font-size: 1.6rem;
      margin: 0.25rem 0;
      height: 36px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .timeline-bar-bg {
      width: 100%;
      height: 6px;
      background: rgba(255, 255, 255, 0.1);
      border-radius: 99px;
      overflow: hidden;
    }

    .timeline-bar-fill {
      height: 100%;
      background: var(--gradient-pink-purple);
      border-radius: 99px;
    }

    /* Mood Picker */
    .mood-selector {
      display: flex;
      flex-wrap: wrap;
      gap: 0.65rem;
      margin-top: 0.75rem;
    }

    .mood-chip {
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      padding: 0.55rem 1rem;
      border-radius: var(--radius-full);
      background: rgba(25, 51, 61, 0.7);
      border: 1px solid var(--bg-glass-border);
      color: var(--text-main);
      font-size: 0.9rem;
      cursor: pointer;
      transition: all var(--transition-fast);
      user-select: none;
    }

    .mood-chip:hover {
      background: rgba(40, 75, 85, 0.8);
      transform: translateY(-2px);
    }

    .mood-chip.selected {
      background: var(--gradient-pink-purple);
      border-color: transparent;
      color: #FFF;
      box-shadow: var(--glow-pink);
    }

    /* Reflection Notes */
    .reflection-card {
      padding: 1.1rem 1.25rem;
      background: rgba(18, 34, 42, 0.65);
      border-left: 3px solid var(--purple-soft);
      border-radius: var(--radius-sm);
      margin-bottom: 0.75rem;
      display: flex;
      justify-content: space-between;
      align-items: flex-start;
      gap: 1rem;
    }

    .reflection-time {
      font-size: 0.75rem;
      color: var(--text-subtle);
      margin-bottom: 0.25rem;
    }

    .reflection-text {
      font-size: 0.925rem;
      line-height: 1.5;
      color: var(--text-main);
    }

    .btn-delete-reflection {
      background: transparent;
      border: none;
      color: var(--text-subtle);
      cursor: pointer;
      font-size: 1.1rem;
      padding: 0.2rem;
      line-height: 1;
      transition: color var(--transition-fast);
    }

    .btn-delete-reflection:hover {
      color: #f87171;
    }

    /* Progress Ring / Bar */
    .progress-bar-container {
      width: 100%;
      height: 12px;
      background: rgba(255, 255, 255, 0.08);
      border-radius: 9999px;
      overflow: hidden;
      margin: 0.75rem 0;
      border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .progress-bar-fill {
      height: 100%;
      background: var(--gradient-pink-purple);
      border-radius: 9999px;
      transition: width 0.5s ease-out;
    }

    @media (max-width: 768px) {
      .profile-hero {
        padding: 1.5rem 1rem;
        flex-direction: column;
        align-items: flex-start;
      }
      .weekly-timeline {
        grid-template-columns: repeat(4, 1fr);
      }
    }
  </style>
</head>
<body>

  <!-- Ambient Backdrop -->
  <div class="ambient-bg" aria-hidden="true">
    <div class="orb orb-1"></div>
    <div class="orb orb-2"></div>
    <div class="orb orb-3"></div>
  </div>
  <div class="leaf-scene" aria-hidden="true">
    <div class="leaf-sprig sprig-top-left"><i></i><b></b><em></em></div>
    <div class="leaf-sprig sprig-top-right"><i></i><b></b><em></em></div>
    <div class="leaf-sprig sprig-left-mid"><i></i><b></b><em></em></div>
    <div class="leaf-sprig sprig-right-mid"><i></i><b></b><em></em></div>
    <div class="leaf-sprig sprig-left-low"><i></i><b></b><em></em></div>
    <div class="leaf-sprig sprig-right-low"><i></i><b></b><em></em></div>
  <div class="leaf-sprig sprig-left-upper"><i></i><b></b><em></em></div>
  <div class="leaf-sprig sprig-right-upper"><i></i><b></b><em></em></div>
  <div class="leaf-sprig sprig-left-center"><i></i><b></b><em></em></div>
  <div class="leaf-sprig sprig-right-center"><i></i><b></b><em></em></div>
  </div>

  <div class="app-container">
    
    <!-- Top Navigation Bar -->
    <header class="navbar">
      <a href="index.html" class="brand-logo" aria-label="MindEase Homepage">
        <div class="brand-icon">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
          </svg>
        </div>
        <span>MindEase</span>
      </a>

      <nav class="nav-links" aria-label="Primary Navigation">
        <a href="index.html" class="nav-link">Home</a>
        <a href="Feelings.html" class="nav-link">How I Feel</a>
        <a href="index.html#breathing" class="nav-link">Breathing</a>
        <a href="Grounding.html" class="nav-link">Grounding</a>
        <a href="Journal.html" class="nav-link">Journal</a>
        <a href="Naturesounds.html" class="nav-link">Nature</a>
                <a href="Calm corner.html" class="nav-link">Calm Corner</a>
        <a href="profile.php" class="nav-link active" aria-current="page">Profile</a>
        <?php if ($is_logged_in): ?>
          <a href="logout.php" class="nav-link" style="color: var(--pink-soft);">Sign Out</a>
        <?php else: ?>
          <a href="login.html" class="nav-link">Sign In</a>
        <?php endif; ?>
      </nav>

      <!-- Sudden Unexplained Feeling Button -->
      <button class="btn btn-sos btn-sm" data-open-modal="crisis" aria-label="Suddenly got a weird feeling?">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
          <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
        </svg>
        <span>Suddenly got a weird feeling?</span>
      </button>
    </header>

    <main class="main-content">
      
      <!-- Profile Header Hero Card -->
      <section class="card card-glass profile-hero animate-fade-in">
        <div class="profile-avatar-group">
          <div class="profile-avatar">
            <span><?php echo htmlspecialchars($initial); ?></span>
          </div>
          <div class="profile-meta">
            <div class="flex items-center gap-2 flex-wrap mb-1">
              <h1 class="text-gradient"><?php echo htmlspecialchars($user_name); ?></h1>
              <?php if ($is_logged_in): ?>
                <span class="badge badge-purple">Safe Space Member</span>
              <?php else: ?>
                <span class="badge badge-blue">Guest Safe Space</span>
              <?php endif; ?>
            </div>
            <p class="text-muted" style="font-size: 0.95rem;">
              <?php if ($is_logged_in): ?>
                <span>📧 <?php echo htmlspecialchars($user_email); ?></span> &bull;
              <?php else: ?>
                <span><a href="login.html" style="color: var(--blue-soft); text-decoration: underline;">Sign in</a> to save progress across devices</span> &bull;
              <?php endif; ?>
              <span id="current-date-display">Today</span>
            </p>
          </div>
        </div>

        <div class="flex gap-2 flex-wrap">
          <button id="btn-reset-today" class="btn btn-secondary btn-sm" title="Clear today's checked items to start a fresh daily reset">
            ↻ Reset Today's Habits
          </button>
          <?php if ($is_logged_in): ?>
            <a href="logout.php" class="btn btn-secondary btn-sm">Sign Out</a>
          <?php else: ?>
            <a href="login.html" class="btn btn-primary btn-sm">Sign In / Register</a>
          <?php endif; ?>
        </div>
      </section>

      <!-- Top Stats Overview Grid -->
      <section class="stats-grid animate-fade-in">
        
        <div class="card card-glass stat-card">
          <div class="stat-icon">🔥</div>
          <div class="stat-val text-gradient" id="stat-streak">1 Day</div>
          <div class="stat-label">Daily Mindful Streak</div>
        </div>

        <div class="card card-glass stat-card">
          <div class="stat-icon">🎯</div>
          <div class="stat-val text-gradient" id="stat-progress-pct">0%</div>
          <div class="stat-label" id="stat-progress-ratio">0 of 5 Daily Habits Done</div>
        </div>

        <div class="card card-glass stat-card">
          <div class="stat-icon">⏱️</div>
          <div class="stat-val text-gradient" id="stat-minutes">0 min</div>
          <div class="stat-label">Mindful Calming Time Today</div>
        </div>

        <div class="card card-glass stat-card">
          <div class="stat-icon" id="stat-mood-icon">🌈</div>
          <div class="stat-val" style="font-size: 1.45rem;" id="stat-mood-label">Not logged yet</div>
          <div class="stat-label">Today's Emotional Tone</div>
        </div>

      </section>

      <!-- Daily Progress Section: Habits Checklist -->
      <section class="card card-glass mb-4 animate-fade-in">
        <div class="flex items-center justify-between flex-wrap gap-2 mb-2">
          <div>
            <span class="badge badge-pink mb-1">Daily Self-Care Reset</span>
            <h2 class="card-title text-gradient" style="font-size: 1.4rem;">Today's Mindful Checklist</h2>
            <p class="card-subtitle">Complete gentle activities throughout your day. Click any task to toggle completion.</p>
          </div>
          <div class="text-right">
            <span class="badge badge-blue" id="checklist-summary-badge">0 / 5 Completed</span>
          </div>
        </div>

        <!-- Progress Bar -->
        <div class="progress-bar-container">
          <div id="daily-progress-bar" class="progress-bar-fill" style="width: 0%;"></div>
        </div>

        <!-- Checklist Items -->
        <div class="habit-list mt-3">
          
          <div class="habit-item" data-goal="checkin">
            <div class="habit-checkbox" id="check-box-checkin">✓</div>
            <div class="habit-info">
              <div class="habit-title">
                <span>☀️</span> Morning Emotional Check-in
              </div>
              <p class="habit-desc">Tune in to what feelings or bodily sensations are present right now.</p>
            </div>
            <a href="Feelings.html" class="habit-action-link">Check In &rarr;</a>
          </div>

          <div class="habit-item" data-goal="breathing">
            <div class="habit-checkbox" id="check-box-breathing">✓</div>
            <div class="habit-info">
              <div class="habit-title">
                <span>🫁</span> 1-Minute Guided Breathing Pause
              </div>
              <p class="habit-desc">Follow the rhythm to regulate your nervous system and release muscle tension.</p>
            </div>
            <a href="index.html#breathing" class="habit-action-link">Breathe &rarr;</a>
          </div>

          <div class="habit-item" data-goal="grounding">
            <div class="habit-checkbox" id="check-box-grounding">✓</div>
            <div class="habit-info">
              <div class="habit-title">
                <span>🌿</span> 5-4-3-2-1 Sensory Grounding
              </div>
              <p class="habit-desc">Anchor your mind back into the present room using your physical senses.</p>
            </div>
            <a href="Grounding.html" class="habit-action-link">Start Grounding &rarr;</a>
          </div>

          <div class="habit-item" data-goal="nature">
            <div class="habit-checkbox" id="check-box-nature">✓</div>
            <div class="habit-info">
              <div class="habit-title">
                <span>🌧️</span> Nature Sound & Rain Audio Pause
              </div>
              <p class="habit-desc">Give your senses peaceful background rainfall, birds, or forest ambience.</p>
            </div>
            <a href="Naturesounds.html" class="habit-action-link">Listen &rarr;</a>
          </div>

          <div class="habit-item" data-goal="reflection">
            <div class="habit-checkbox" id="check-box-reflection">✓</div>
            <div class="habit-info">
              <div class="habit-title">
                <span>✍️</span> Evening Reflection & Gratitude Note
              </div>
              <p class="habit-desc">Jot down one kind reminder, relief moment, or thought you are letting go of.</p>
            </div>
            <a href="#reflection-section" class="habit-action-link">Write Note &darr;</a>
          </div>

        </div>
      </section>

      <!-- 7-Day Mood Journey & Quick Mood Logger -->
      <section class="card card-glass mb-4 animate-fade-in">
        <div class="mb-3">
          <span class="badge badge-purple mb-1">Emotional Timeline</span>
          <h2 class="card-title text-gradient" style="font-size: 1.4rem;">7-Day Mood & Progress Journey</h2>
          <p class="card-subtitle">Tracking your moods helps you notice calm patterns without any pressure or judgment.</p>
        </div>

        <!-- Weekly Cards Grid -->
        <div class="weekly-timeline" id="weekly-timeline-container">
          <!-- Populated by JavaScript -->
        </div>

        <!-- Quick Log Mood -->
        <div class="mt-4 pt-3" style="border-top: 1px solid var(--bg-glass-border);">
          <label class="form-label" style="font-size: 0.95rem; font-weight: 600;">Log or update today's feeling:</label>
          <div class="mood-selector" id="mood-chips-container">
            <button type="button" class="mood-chip" data-mood="peaceful" data-emoji="😊" data-label="Peaceful">
              <span>😊</span> Peaceful
            </button>
            <button type="button" class="mood-chip" data-mood="relaxed" data-emoji="😌" data-label="Relaxed">
              <span>😌</span> Relaxed
            </button>
            <button type="button" class="mood-chip" data-mood="anxious" data-emoji="⚡" data-label="Anxious">
              <span>⚡</span> Anxious
            </button>
            <button type="button" class="mood-chip" data-mood="overwhelmed" data-emoji="🌊" data-label="Overwhelmed">
              <span>🌊</span> Overwhelmed
            </button>
            <button type="button" class="mood-chip" data-mood="sad" data-emoji="🌧️" data-label="Heavy/Sad">
              <span>🌧️</span> Heavy / Sad
            </button>
            <button type="button" class="mood-chip" data-mood="burnout" data-emoji="🔥" data-label="Burnout">
              <span>🔥</span> Burnout
            </button>
            <button type="button" class="mood-chip" data-mood="uncertain" data-emoji="🌫️" data-label="Uncertain">
              <span>🌫️</span> Disconnected
            </button>
          </div>
        </div>
      </section>

      <!-- Daily Private Thought & Gratitude Log -->
      <section id="reflection-section" class="card card-glass mb-4 animate-fade-in">
        <div class="mb-3">
          <span class="badge badge-blue mb-1">Private Safe Journal</span>
          <h2 class="card-title text-gradient" style="font-size: 1.4rem;">Today's Reflections & Calm Notes</h2>
          <p class="card-subtitle">Private to your account when signed in, or saved on this device as a guest. Write a small thought, gratitude, or vent.</p>
        </div>

        <form id="reflection-form" class="mb-3">
          <div class="form-group mb-2">
            <textarea id="reflection-input" class="form-input" rows="3" placeholder="What is one kind thought you have for yourself today, or a small moment of relief?..." style="resize: vertical; font-family: inherit;"></textarea>
          </div>
          <button type="submit" class="btn btn-primary btn-sm">
            Save Daily Reflection
          </button>
        </form>

        <div id="reflections-list">
          <!-- Populated by JavaScript -->
        </div>
      </section>

    </main>

    <!-- Footer Component -->
    <footer class="footer">
      <div class="footer-content">
        <div class="brand-logo">
          <div class="brand-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>
            </svg>
          </div>
          <span>MindEase</span>
        </div>
        <p class="footer-disclaimer">
          🔒 <strong>100% Private Self-Care:</strong> Your daily habit checks and mood logs are stored locally; signed-in reflections are saved privately to your account. MindEase is an emotional first-aid safe space.
        </p>
        <p class="text-subtle" style="font-size: 0.8rem;">
          &copy; 2026 MindEase. Crafted with care for student & young adult well-being.
        </p>
      </div>
    </footer>

  </div>

  <!-- Unexpected Feeling Modal -->
  <div id="crisis-modal" class="modal-backdrop" role="dialog" aria-modal="true" aria-labelledby="crisis-title">
    <div class="modal-card">
      <div class="modal-header">
        <div class="flex items-center gap-2">
          <span class="badge badge-sos">Gentle First-Aid</span>
          <h3 id="crisis-title" style="font-size: 1.25rem;">Suddenly got a weird feeling?</h3>
        </div>
        <button class="modal-close" data-close-modal="crisis" aria-label="Close modal">&times;</button>
      </div>

      <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 1.5rem;">
        <div class="p-3" style="background: rgba(18, 16, 32, 0.6); border-radius: var(--radius-md); border: 1px solid var(--bg-glass-border);">
          <h4 class="text-pink mb-2" style="font-size: 1.05rem;"><span>🌿</span> Change your physical environment</h4>
          <ul style="display: flex; flex-direction: column; gap: 0.4rem; padding-left: 1.25rem; font-size: 0.9rem;" class="text-muted">
            <li>Sit somewhere comfortable.</li>
            <li>Loosen your shoulders and unclench your jaw.</li>
            <li>Get a sip of water or fresh air.</li>
          </ul>
        </div>
        <div class="p-3" style="background: rgba(18, 16, 32, 0.6); border-radius: var(--radius-md); border: 1px solid var(--bg-glass-border);">
          <h4 class="text-purple mb-2" style="font-size: 1.05rem;"><span>🧠</span> Don't fight the feeling</h4>
          <p class="text-muted" style="font-size: 0.9rem;">
            “I'm experiencing an uncomfortable sensation. I don't have to fix everything this second. I'll give myself a few minutes to settle.”
          </p>
        </div>
      </div>

      <div class="text-center">
        <button class="btn btn-secondary btn-sm" data-close-modal="crisis">Close Window</button>
      </div>
    </div>
  </div>

  <script src="js/config.js"></script>
  <script src="js/app.js?v=database-1"></script>
  <script src="js/progress.js"></script>
  <script>
    const PROFILE_DATABASE_ENABLED = <?php echo $is_logged_in ? 'true' : 'false'; ?>;

    document.addEventListener('DOMContentLoaded', () => {
      // Set current date in hero
      const dateEl = document.getElementById('current-date-display');
      if (dateEl) {
        const options = { weekday: 'long', month: 'short', day: 'numeric', year: 'numeric' };
        dateEl.textContent = new Date().toLocaleDateString(undefined, options);
      }

      // Render all progress elements
      renderProfileProgress();
      if (PROFILE_DATABASE_ENABLED) loadDatabaseReflections();

      // Setup Habit Checklist item clicks
      document.querySelectorAll('.habit-item').forEach(item => {
        item.addEventListener('click', (e) => {
          // If clicked the direct action link, allow normal navigation
          if (e.target.closest('.habit-action-link')) return;

          const goalKey = item.dataset.goal;
          const isCurrentlyCompleted = item.classList.contains('completed');
          MindEaseProgress.toggleGoal(goalKey, !isCurrentlyCompleted);
          renderProfileProgress();
        });
      });

      // Setup Mood Chip clicks
      document.querySelectorAll('.mood-chip').forEach(chip => {
        chip.addEventListener('click', () => {
          const mood = chip.dataset.mood;
          const label = chip.dataset.label;
          const emoji = chip.dataset.emoji;
          MindEaseProgress.logMood(mood, label, emoji);
          renderProfileProgress();
        });
      });

      // Setup Reflection Form submit
      const refForm = document.getElementById('reflection-form');
      const refInput = document.getElementById('reflection-input');
      if (refForm && refInput) {
        refForm.addEventListener('submit', (e) => {
          e.preventDefault();
          const text = refInput.value.trim();
          if (text) {
            if (PROFILE_DATABASE_ENABLED) {
              saveDatabaseReflection(text);
              return;
            }
            MindEaseProgress.addReflection(text);
            refInput.value = '';
            renderProfileProgress();
          }
        });
      }

      // Setup Reset Today button
      const resetBtn = document.getElementById('btn-reset-today');
      if (resetBtn) {
        resetBtn.addEventListener('click', () => {
          if (confirm("Reset today's checked habits so you can start your daily reset over?")) {
            MindEaseProgress.resetToday();
            renderProfileProgress();
          }
        });
      }
    });

    function renderProfileProgress() {
      const stats = MindEaseProgress.getStats();

      // 1. Update Streak
      const streakEl = document.getElementById('stat-streak');
      if (streakEl) {
        streakEl.textContent = `${stats.streak} ${stats.streak === 1 ? 'Day' : 'Days'}`;
      }

      // 2. Update Progress Percentage & Bar
      const pctEl = document.getElementById('stat-progress-pct');
      const ratioEl = document.getElementById('stat-progress-ratio');
      const barFill = document.getElementById('daily-progress-bar');
      const badgeSummary = document.getElementById('checklist-summary-badge');

      if (pctEl) pctEl.textContent = `${stats.percentage}%`;
      if (ratioEl) ratioEl.textContent = `${stats.completedGoals} of ${stats.totalGoals} Daily Habits Done`;
      if (barFill) barFill.style.width = `${stats.percentage}%`;
      if (badgeSummary) badgeSummary.textContent = `${stats.completedGoals} / ${stats.totalGoals} Completed`;

      // 3. Update Mindful Minutes
      const minEl = document.getElementById('stat-minutes');
      if (minEl) {
        // Estimate minutes based on completed activities if minutes not logged
        const estimatedMinutes = Math.max(stats.minutesMindful, stats.completedGoals * 2);
        minEl.textContent = `${estimatedMinutes} min`;
      }

      // 4. Update Mood
      const moodLabelEl = document.getElementById('stat-mood-label');
      const moodIconEl = document.getElementById('stat-mood-icon');
      if (moodLabelEl && moodIconEl) {
        if (stats.moodLabel) {
          moodLabelEl.textContent = stats.moodLabel;
          moodIconEl.textContent = stats.moodEmoji || '✨';
        } else {
          moodLabelEl.textContent = 'Not logged yet';
          moodIconEl.textContent = '🌈';
        }
      }

      // Highlight selected mood chip
      document.querySelectorAll('.mood-chip').forEach(chip => {
        if (stats.mood && chip.dataset.mood === stats.mood) {
          chip.classList.add('selected');
        } else {
          chip.classList.remove('selected');
        }
      });

      // 5. Update Habit Checklist items
      Object.keys(stats.goals).forEach(key => {
        const item = document.querySelector(`.habit-item[data-goal="${key}"]`);
        const box = document.getElementById(`check-box-${key}`);
        const isDone = stats.goals[key];

        if (item) {
          if (isDone) {
            item.classList.add('completed');
          } else {
            item.classList.remove('completed');
          }
        }
        if (box) {
          box.style.display = isDone ? 'flex' : 'none';
        }
      });

      // 6. Render Weekly Timeline
      renderWeeklyTimeline();

      // 7. Render Reflections
      if (!PROFILE_DATABASE_ENABLED) renderReflections(stats.reflections);
    }

    function renderWeeklyTimeline() {
      const container = document.getElementById('weekly-timeline-container');
      if (!container) return;

      const week = MindEaseProgress.getWeeklyHistory();
      container.innerHTML = '';

      week.forEach(day => {
        const col = document.createElement('div');
        col.className = `timeline-day ${day.isToday ? 'is-today' : ''}`;

        const dayName = document.createElement('div');
        dayName.className = 'timeline-name';
        dayName.textContent = day.isToday ? 'Today' : day.dayLabel;

        const dayNum = document.createElement('div');
        dayNum.className = 'timeline-date';
        dayNum.textContent = day.dayNumber;

        const emojiEl = document.createElement('div');
        emojiEl.className = 'timeline-emoji';
        emojiEl.textContent = day.moodEmoji || (day.completedCount > 0 ? '✨' : '·');

        const barBg = document.createElement('div');
        barBg.className = 'timeline-bar-bg';
        const barFill = document.createElement('div');
        barFill.className = 'timeline-bar-fill';
        barFill.style.width = `${day.percentage}%`;
        barBg.appendChild(barFill);

        col.appendChild(dayName);
        col.appendChild(dayNum);
        col.appendChild(emojiEl);
        col.appendChild(barBg);

        container.appendChild(col);
      });
    }

    async function loadDatabaseReflections() {
      try {
        const response = await fetch('reflection_api.php');
        const data = await response.json();
        if (response.ok && data.ok) renderReflections(data.reflections, true);
      } catch (error) {
        // The rest of the profile remains available if MySQL is offline.
      }
    }

    async function saveDatabaseReflection(text) {
      try {
        const response = await fetch('reflection_api.php', { method: 'POST', body: new URLSearchParams({ content: text }) });
        const data = await response.json();
        if (!response.ok || !data.ok) return;
        document.getElementById('reflection-input').value = '';
        loadDatabaseReflections();
      } catch (error) {
        // Keep the written text in place if it could not be saved.
      }
    }
    function renderReflections(reflections, useDatabase = false) {
      const container = document.getElementById('reflections-list');
      if (!container) return;

      if (!reflections || reflections.length === 0) {
        container.innerHTML = `
          <div class="p-3 text-center text-muted" style="background: rgba(18, 34, 42, 0.4); border-radius: var(--radius-sm); font-size: 0.875rem;">
            No reflection notes added today yet. Write one gentle thought above to capture your calm moment.
          </div>`;
        return;
      }

      container.innerHTML = '';
      reflections.forEach(note => {
        const card = document.createElement('div');
        card.className = 'reflection-card';

        const content = document.createElement('div');
        content.innerHTML = `
          <div class="reflection-time">🕒 Today at ${note.time}</div>
          <div class="reflection-text">“${escapeHtml(note.text)}”</div>
        `;

        const delBtn = document.createElement('button');
        delBtn.className = 'btn-delete-reflection';
        delBtn.innerHTML = '&times;';
        delBtn.title = 'Delete note';
        delBtn.addEventListener('click', async () => {
          if (useDatabase) {
            try {
              await fetch('reflection_api.php', { method: 'DELETE', body: new URLSearchParams({ id: note.id }) });
              loadDatabaseReflections();
            } catch (error) { /* Keep the note visible if deletion fails. */ }
          } else {
            MindEaseProgress.deleteReflection(note.id);
            renderProfileProgress();
          }
        });

        card.appendChild(content);
        card.appendChild(delBtn);
        container.appendChild(card);
      });
    }

    function escapeHtml(str) {
      const div = document.createElement('div');
      div.textContent = str;
      return div.innerHTML;
    }
  </script>
</body>
</html>
