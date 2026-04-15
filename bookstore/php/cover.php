<?php
$category_themes = [
    'English Story Books' => [
        'top'    => '#1a1a2e',
        'bottom' => '#16213e',
        'accent' => '#e94560',
        'text'   => '#ffffff',
        'icon'   => '📖',
        'label'  => 'ENGLISH STORIES',
        'pattern'=> 'lines',
    ],
    'Motivational and Positive Thinking' => [
        'top'    => '#f7971e',
        'bottom' => '#ffd200',
        'accent' => '#ff6b35',
        'text'   => '#1a1a1a',
        'icon'   => '🌟',
        'label'  => 'MOTIVATIONAL',
        'pattern'=> 'dots',
    ],
    'Engineering Books' => [
        'top'    => '#0f3460',
        'bottom' => '#533483',
        'accent' => '#00b4d8',
        'text'   => '#ffffff',
        'icon'   => '⚙',
        'label'  => 'ENGINEERING',
        'pattern'=> 'grid',
    ],
    'Programming and Technology' => [
        'top'    => '#0d1117',
        'bottom' => '#161b22',
        'accent' => '#00ff41',
        'text'   => '#ffffff',
        'icon'   => '&lt;&gt;',
        'label'  => 'PROGRAMMING',
        'pattern'=> 'code',
    ],
    "Children's Books" => [
        'top'    => '#ff9a9e',
        'bottom' => '#fecfef',
        'accent' => '#ff6b9d',
        'text'   => '#2d2d2d',
        'icon'   => '⭐',
        'label'  => "CHILDREN'S",
        'pattern'=> 'stars',
    ],
];

$default_theme = [
    'top'    => '#2c3e50',
    'bottom' => '#3498db',
    'accent' => '#f39c12',
    'text'   => '#ffffff',
    'icon'   => '📚',
    'label'  => 'BOOK',
    'pattern'=> 'lines',
];

// --- Load book data from XML ---
$book_id  = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$xml_path = __DIR__ . '/../xml/books.xml';

$title    = 'Book Title';
$author   = 'Author Name';
$category = '';

if (file_exists($xml_path)) {
    $xml = simplexml_load_file($xml_path);
    foreach ($xml->book as $book) {
        if ((int)$book->id === $book_id) {
            $title    = (string)$book->title;
            $author   = (string)$book->author;
            $category = (string)$book->category;
            break;
        }
    }
}

// --- Get theme for this category ---
$theme = $category_themes[$category] ?? $default_theme;

// --- Escape for SVG ---
function svgesc($str) {
    return htmlspecialchars($str, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

// Word-wrap title for SVG (max ~18 chars per line)
function wrap_title($title, $max = 18) {
    $words = explode(' ', $title);
    $lines = [];
    $line  = '';
    foreach ($words as $word) {
        if (strlen($line . ' ' . $word) > $max && $line !== '') {
            $lines[] = $line;
            $line = $word;
        } else {
            $line = $line === '' ? $word : $line . ' ' . $word;
        }
    }
    if ($line) $lines[] = $line;
    return array_slice($lines, 0, 3); // max 3 lines
}

$title_lines = wrap_title($title);
$author_short = strlen($author) > 22 ? substr($author, 0, 22) . '…' : $author;

// --- Output SVG image ---
header('Content-Type: image/svg+xml');
header('Cache-Control: public, max-age=86400');

$top    = svgesc($theme['top']);
$bottom = svgesc($theme['bottom']);
$accent = svgesc($theme['accent']);
$text   = svgesc($theme['text']);
$label  = svgesc($theme['label']);
$icon   = $theme['icon']; // already HTML entity or text

// Title y positions depending on line count
$line_count    = count($title_lines);
$title_start_y = 155 - ($line_count - 1) * 16;

?>
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 280" width="200" height="280">
  <defs>
    <linearGradient id="bg<?= $book_id ?>" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%"   stop-color="<?= $top ?>"/>
      <stop offset="100%" stop-color="<?= $bottom ?>"/>
    </linearGradient>
    <linearGradient id="spine<?= $book_id ?>" x1="0" y1="0" x2="1" y2="0">
      <stop offset="0%"   stop-color="rgba(0,0,0,0.4)"/>
      <stop offset="100%" stop-color="rgba(0,0,0,0)"/>
    </linearGradient>
    <linearGradient id="shine<?= $book_id ?>" x1="0" y1="0" x2="0" y2="1">
      <stop offset="0%"   stop-color="rgba(255,255,255,0.12)"/>
      <stop offset="50%"  stop-color="rgba(255,255,255,0)"/>
    </linearGradient>
  </defs>

  <!-- Book body -->
  <rect width="200" height="280" rx="4" ry="4" fill="url(#bg<?= $book_id ?>)"/>

  <?php if ($theme['pattern'] === 'lines'): ?>
  <!-- Decorative diagonal lines -->
  <g opacity="0.06" stroke="<?= $text ?>" stroke-width="1">
    <?php for ($i = -280; $i < 400; $i += 18): ?>
    <line x1="<?= $i ?>" y1="0" x2="<?= $i + 280 ?>" y2="280"/>
    <?php endfor; ?>
  </g>
  <?php elseif ($theme['pattern'] === 'dots'): ?>
  <!-- Dot pattern -->
  <g opacity="0.12" fill="<?= $text ?>">
    <?php for ($y = 10; $y < 280; $y += 20): for ($x = 10; $x < 200; $x += 20): ?>
    <circle cx="<?= $x ?>" cy="<?= $y ?>" r="1.5"/>
    <?php endfor; endfor; ?>
  </g>
  <?php elseif ($theme['pattern'] === 'grid'): ?>
  <!-- Grid pattern for engineering -->
  <g opacity="0.08" stroke="<?= $text ?>" stroke-width="0.5">
    <?php for ($y = 0; $y < 280; $y += 20): ?>
    <line x1="0" y1="<?= $y ?>" x2="200" y2="<?= $y ?>"/>
    <?php endfor; ?>
    <?php for ($x = 0; $x < 200; $x += 20): ?>
    <line x1="<?= $x ?>" y1="0" x2="<?= $x ?>" y2="280"/>
    <?php endfor; ?>
  </g>
  <?php elseif ($theme['pattern'] === 'code'): ?>
  <!-- Code lines pattern for programming -->
  <g opacity="0.07" fill="<?= $accent ?>">
    <?php $snippets = ['01', '10', '{}', '[]', '//', '0x', '&&', '=>', '++', '!=', '==', '<<']; ?>
    <?php $si = 0; for ($y = 15; $y < 280; $y += 18): for ($x = 8; $x < 200; $x += 32): ?>
    <text x="<?= $x ?>" y="<?= $y ?>" font-family="monospace" font-size="9"><?= $snippets[$si % count($snippets)] ?></text>
    <?php $si++; endfor; endfor; ?>
  </g>
  <?php elseif ($theme['pattern'] === 'stars'): ?>
  <!-- Star pattern for children's -->
  <g opacity="0.15" fill="<?= $accent ?>">
    <?php $positions = [[20,20],[60,15],[120,25],[170,18],[40,55],[90,45],[150,50],[185,40],[15,90],[75,85],[130,80],[190,95],[50,130],[110,120],[165,135],[25,165],[80,155],[140,170],[185,160],[10,200],[65,195],[125,205],[175,195],[35,235],[100,230],[160,240],[190,225]]; ?>
    <?php foreach ($positions as $p): ?>
    <text x="<?= $p[0] ?>" y="<?= $p[1] ?>" font-size="<?= rand(6,12) ?>">★</text>
    <?php endforeach; ?>
  </g>
  <?php endif; ?>

  <!-- Top accent bar -->
  <rect x="0" y="0" width="200" height="6" fill="<?= $accent ?>" rx="4" ry="0"/>
  <rect x="0" y="4" width="200" height="3" fill="<?= $accent ?>"/>

  <!-- Category label badge -->
  <rect x="12" y="16" width="<?= min(strlen($label) * 7 + 16, 176) ?>" height="18" rx="9" fill="<?= $accent ?>" opacity="0.9"/>
  <text x="20" y="28" font-family="Arial, sans-serif" font-size="8" font-weight="bold"
        fill="<?= $theme['pattern'] === 'dots' ? '#1a1a1a' : '#ffffff' ?>" letter-spacing="0.5"><?= $label ?></text>

  <!-- Big category icon -->
  <text x="100" y="108" font-family="Arial, sans-serif" font-size="52"
        text-anchor="middle" dominant-baseline="middle" opacity="0.9"><?= $icon ?></text>

  <!-- Horizontal divider -->
  <line x1="20" y1="128" x2="180" y2="128" stroke="<?= $accent ?>" stroke-width="1.5" opacity="0.6"/>

  <!-- Book Title -->
  <?php foreach ($title_lines as $i => $line): ?>
  <text x="100" y="<?= $title_start_y + ($i * 22) ?>"
        font-family="Georgia, serif"
        font-size="<?= strlen($line) > 14 ? '13' : '15' ?>"
        font-weight="bold"
        fill="<?= $text ?>"
        text-anchor="middle"
        dominant-baseline="middle"><?= svgesc($line) ?></text>
  <?php endforeach; ?>

  <!-- Author -->
  <text x="100" y="<?= $title_start_y + ($line_count * 22) + 14 ?>"
        font-family="Arial, sans-serif"
        font-size="10"
        fill="<?= $text ?>"
        text-anchor="middle"
        dominant-baseline="middle"
        opacity="0.75">— <?= svgesc($author_short) ?></text>

  <!-- Bottom publisher bar -->
  <rect x="0" y="256" width="200" height="24" fill="rgba(0,0,0,0.35)"/>
  <text x="100" y="270" font-family="Arial, sans-serif" font-size="8"
        fill="<?= $accent ?>" text-anchor="middle" letter-spacing="2" font-weight="bold">BOOKVAULT</text>

  <!-- Spine shadow (left edge) -->
  <rect x="0" y="0" width="12" height="280" fill="url(#spine<?= $book_id ?>)" rx="4" ry="4"/>

  <!-- Shine overlay -->
  <rect x="0" y="0" width="200" height="140" fill="url(#shine<?= $book_id ?>)" rx="4" ry="4"/>

  <!-- Right page edge lines -->
  <line x1="196" y1="6" x2="196" y2="260" stroke="rgba(255,255,255,0.1)" stroke-width="1"/>
  <line x1="198" y1="6" x2="198" y2="260" stroke="rgba(255,255,255,0.05)" stroke-width="1"/>
</svg>
