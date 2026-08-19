<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$models = ['Activity', 'Agenda', 'Banner', 'Category', 'Document', 'Gallery', 'HomeWidget', 'InstagramPost', 'Menu', 'OrganizationMember', 'Page', 'Post', 'RelatedLink', 'Setting', 'User'];
foreach ($models as $m) {
    $class = "\\App\\Models\\" . $m;
    if (class_exists($class)) {
        $has = in_array(\Spatie\Activitylog\Traits\LogsActivity::class, class_uses_recursive($class));
        echo str_pad($m, 20) . ': ' . ($has ? 'YES' : 'NO') . "\n";
    }
}
