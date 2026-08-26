<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$contentModels = [
    \App\Models\Post::class,
    \App\Models\Page::class,
    \App\Models\Document::class,
    \App\Models\Category::class,
    \App\Models\Banner::class,
    \App\Models\Gallery::class,
    \App\Models\Agenda::class,
    \App\Models\Announcement::class,
    \App\Models\HomeWidget::class,
    \App\Models\RelatedLink::class,
    \App\Models\InstagramPost::class,
    \App\Models\OrganizationMember::class,
    \App\Models\Menu::class,
    \App\Models\Setting::class,
];

$logs = Spatie\Activitylog\Models\Activity::whereIn('subject_type', $contentModels)->latest()->limit(5)->get();
foreach($logs as $l) {
    echo $l->log_name . ' | ' . $l->description . ' | ' . $l->subject_type . "\n";
}
