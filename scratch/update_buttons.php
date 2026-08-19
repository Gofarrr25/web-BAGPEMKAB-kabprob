<?php
$dir = "c:/Users/Hype G12/New folder/Semester 3/semester 4/BAGPEMKAB/resources/views/admin";
$files = [
    "activity_logs/show.blade.php",
    "banners/edit.blade.php",
    "documents/create.blade.php",
    "documents/edit.blade.php",
    "home_widgets/create.blade.php",
    "home_widgets/edit.blade.php",
    "organization_members/create.blade.php",
    "organization_members/edit.blade.php",
    "pages/create.blade.php",
    "pages/edit.blade.php",
    "posts/create.blade.php",
    "posts/edit.blade.php",
    "related_links/create.blade.php",
    "related_links/edit.blade.php",
    "users/create.blade.php",
    "users/edit.blade.php"
];

$routes = [
    'activity_logs/show.blade.php' => 'admin.activity-logs.index',
    'banners/edit.blade.php' => 'admin.banners.index',
    'documents/create.blade.php' => 'admin.documents.index',
    'documents/edit.blade.php' => 'admin.documents.index',
    'home_widgets/create.blade.php' => 'admin.settings.index',
    'home_widgets/edit.blade.php' => 'admin.settings.index',
    'organization_members/create.blade.php' => 'admin.organization-members.index',
    'organization_members/edit.blade.php' => 'admin.organization-members.index',
    'pages/create.blade.php' => 'admin.pages.index',
    'pages/edit.blade.php' => 'admin.pages.index',
    'posts/create.blade.php' => 'admin.posts.index',
    'posts/edit.blade.php' => 'admin.posts.index',
    'related_links/create.blade.php' => 'admin.settings.index',
    'related_links/edit.blade.php' => 'admin.settings.index',
    'users/create.blade.php' => 'admin.users.index',
    'users/edit.blade.php' => 'admin.users.index',
];

foreach ($files as $f) {
    $path = "$dir/$f";
    if(!file_exists($path)) continue;
    $content = file_get_contents($path);
    $route = $routes[$f];

    // Remove old "Kembali" buttons at the top
    $content = preg_replace('/<a href="\{\{\s*route\([^}]+\)\s*\}\}"[^>]*>\s*<i class="fas fa-arrow-left[^>]*><\/i>\s*Kembali\s*<\/a>/s', '', $content);
    // Remove "Kembali" icons that are just w-10 h-10 circles (like in pages)
    $content = preg_replace('/<a href="[^"]+" class="w-10 h-10[^"]+" title="Kembali">\s*<i class="fas fa-arrow-left[^>]*><\/i>\s*<\/a>/s', '', $content);
    // Remove "Batal" buttons usually placed near submit buttons
    $content = preg_replace('/<a href="\{\{\s*route\([^}]+\)\s*\}\}"[^>]*>[\s\r\n]*Batal[\s\r\n]*<\/a>/is', '', $content);
    // Remove "Batal" links explicitly if they use different formatting
    $content = preg_replace('/<a href="\{\{\s*route\([^}]+\)\s*\}\}" class="[^"]*">Batal<\/a>/', '', $content);
    
    // Create new standard back button string
    $btn = '<a href="{{ route(\''.$route.'\') }}" class="text-gray-500 hover:text-gray-700 text-sm font-semibold transition flex items-center justify-center gap-1 bg-white border border-gray-200 px-3 py-1.5 rounded-lg hover:bg-gray-50"><i class="fas fa-arrow-left"></i> Kembali</a>';

    // Inject into the header div
    // We look for a header div like <div class="px-6 py-4 ... justify-between items-center">
    // or <div class="flex justify-between items-center mb-6 ...">
    if (preg_match('/<div class="[^"]*justify-between\s+items-center[^"]*">\s*<h3 class="[^"]*">.*?<\/h3>/s', $content, $m)) {
        $replacement = $m[0] . "\n            " . $btn;
        $content = str_replace($m[0], $replacement, $content);
    } else if (preg_match('/<div class="px-6 py-4 border-b border-gray-100 bg-gray-50\/50 flex justify-between items-center">\s*<h3 class="font-bold text-gray-800">.*?<\/h3>\s*(<span[^>]*>.*?<\/span>)?/s', $content, $m)) {
        $replacement = $m[0] . "\n            " . $btn;
        $content = str_replace($m[0], $replacement, $content);
    } else if (preg_match('/<div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center">\s*<h2 class="[^"]*">.*?<\/h2>/s', $content, $m)) {
        $replacement = $m[0] . "\n            " . $btn;
        $content = str_replace($m[0], $replacement, $content);
    }

    file_put_contents($path, $content);
    echo "Processed $f\n";
}
?>
