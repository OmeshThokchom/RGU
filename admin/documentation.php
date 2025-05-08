<?php
require_once('includes/header.php');
require_once(__DIR__ . '/../vendor/erusev/parsedown/Parsedown.php');

// Initialize Parsedown
$parsedown = new Parsedown();

// Function to get documentation content
function getDocContent($filePath) {
    if (file_exists($filePath)) {
        return file_get_contents($filePath);
    }
    return null;
}

// Function to get all documentation files
function getAllDocFiles() {
    $docs = glob(__DIR__ . '/../docs/*.md');
    $docList = [];
    foreach ($docs as $doc) {
        $filename = basename($doc, '.md');
        $title = ucwords(str_replace('_', ' ', $filename));
        $docList[$filename] = $title;
    }
    return $docList;
}

// Get all available docs
$availableDocs = getAllDocFiles();

// Get the requested documentation file
$docFile = isset($_GET['doc']) ? $_GET['doc'] : 'documentation';
$docFile = array_key_exists($docFile, $availableDocs) ? $docFile : 'documentation';
$filePath = __DIR__ . "/../docs/{$docFile}.md";

// Get documentation content
$content = getDocContent($filePath);
?>

<div class="container docs-container">
    <div class="docs-layout">
        <aside class="docs-sidebar glass">
            <div class="docs-header">
                <h2><i class="fi fi-sr-book"></i> Documentation</h2>
            </div>
            <nav class="docs-nav">
                <?php foreach ($availableDocs as $filename => $title): ?>
                <a href="?doc=<?php echo $filename; ?>" 
                   class="doc-link <?php echo $docFile === $filename ? 'active' : ''; ?>">
                    <?php echo $title; ?>
                </a>
                <?php endforeach; ?>
            </nav>
        </aside>
        <main class="docs-content glass">
            <div class="markdown-content">
                <?php 
                if ($content !== null) {
                    echo $parsedown->text($content);
                } else {
                    echo '<div class="error-message">Documentation file not found.</div>';
                }
                ?>
            </div>
        </main>
    </div>
</div>

<style>
.docs-container {
    max-width: 1400px;
    padding: 2rem 1rem;
}

.docs-layout {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 2rem;
    min-height: calc(100vh - 200px);
}

.docs-sidebar {
    position: sticky;
    top: 2rem;
    height: calc(100vh - 4rem); /* Adjust height to viewport minus top and bottom spacing */
    padding: 1.5rem;
    overflow-y: auto; /* Enable vertical scrolling */
    scrollbar-width: thin; /* For Firefox */
    scrollbar-color: rgba(107, 70, 193, 0.5) rgba(255, 255, 255, 0.1); /* For Firefox */
}

.docs-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 2rem;
}

.docs-header h2 {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: rgba(255, 255, 255, 0.95);
    font-size: 1.5rem;
    margin: 0;
}

.docs-nav {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.doc-link {
    display: block;
    padding: 0.75rem 1rem;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.doc-link:hover {
    background: rgba(255, 255, 255, 0.05);
    color: rgba(255, 255, 255, 0.95);
}

.doc-link.active {
    background: rgba(107, 70, 193, 0.2);
    color: rgba(255, 255, 255, 0.95);
}

.docs-content {
    padding: 2.5rem;
    line-height: 1.8;
}

.markdown-content {
    max-width: 800px;
    margin: 0 auto;
}

.docs-content h1, 
.docs-content h2, 
.docs-content h3 {
    color: rgba(255, 255, 255, 0.95);
    margin-top: 2rem;
    margin-bottom: 1rem;
    scroll-margin-top: 2rem;
}

.docs-content h1:first-child {
    margin-top: 0;
}

.docs-content p {
    color: rgba(255, 255, 255, 0.9);
    margin-bottom: 1rem;
}

.docs-content code {
    background: rgba(255, 255, 255, 0.1);
    padding: 0.2rem 0.4rem;
    border-radius: 4px;
    font-family: monospace;
    font-size: 0.9em;
}

.docs-content pre {
    background: rgba(0, 0, 0, 0.2);
    padding: 1.25rem;
    border-radius: 8px;
    overflow-x: auto;
    margin: 1.5rem 0;
}

.docs-content pre code {
    background: none;
    padding: 0;
    font-size: 0.9em;
}

.docs-content ul, 
.docs-content ol {
    margin: 1rem 0 1.5rem;
    padding-left: 1.5rem;
    color: rgba(255, 255, 255, 0.9);
}

.docs-content li {
    margin: 0.5rem 0;
}

.docs-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5rem 0;
}

.docs-content th,
.docs-content td {
    padding: 0.75rem 1rem;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.docs-content th {
    background: rgba(255, 255, 255, 0.05);
    font-weight: 500;
    text-align: left;
}

.docs-content tr:hover td {
    background: rgba(255, 255, 255, 0.02);
}

.error-message {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 3rem 2rem;
    text-align: center;
    color: rgba(255, 255, 255, 0.8);
}

.error-message i {
    font-size: 2.5rem;
    color: rgba(220, 38, 38, 0.8);
}

@media (max-width: 1024px) {
    .docs-layout {
        grid-template-columns: 250px 1fr;
        gap: 1.5rem;
    }
}

@media (max-width: 768px) {
    .docs-layout {
        grid-template-columns: 1fr;
    }

    .docs-sidebar {
        position: relative;
        top: 0;
    }

    .docs-nav {
        display: none;
        margin-top: 1rem;
    }

    .docs-nav.active {
        display: block;
    }

    .docs-content {
        padding: 1.5rem;
    }
}

/* Webkit scrollbar styling */
.docs-sidebar::-webkit-scrollbar {
    width: 6px;
}

.docs-sidebar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 3px;
}

.docs-sidebar::-webkit-scrollbar-thumb {
    background: rgba(107, 70, 193, 0.5);
    border-radius: 3px;
}

.docs-sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(107, 70, 193, 0.7);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const menuToggle = document.getElementById('menuToggle');
    const docsNav = document.getElementById('docsNav');

    menuToggle.addEventListener('click', function() {
        docsNav.classList.toggle('active');
        menuToggle.innerHTML = docsNav.classList.contains('active') 
            ? '<i class="fi fi-sr-cross"></i>' 
            : '<i class="fi fi-sr-menu-burger"></i>';
    });
});
</script>

<?php require_once('includes/footer.php'); ?>