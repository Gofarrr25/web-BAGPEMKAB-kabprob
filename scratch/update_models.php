<?php
$models = ['HomeWidget', 'InstagramPost', 'Menu', 'OrganizationMember', 'Page', 'RelatedLink'];
foreach ($models as $model) {
    $path = __DIR__ . '/../app/Models/' . $model . '.php';
    if (!file_exists($path)) continue;
    $content = file_get_contents($path);
    
    if (strpos($content, 'LogsActivity') !== false) {
        echo $model . " already has LogsActivity.\n";
        continue;
    }
    
    // Add use statements
    $useStatement = "use Spatie\Activitylog\Traits\LogsActivity;\nuse Spatie\Activitylog\LogOptions;";
    $content = preg_replace('/use Illuminate\\\\Database\\\\Eloquent\\\\Model;/', "use Illuminate\Database\Eloquent\Model;\n" . $useStatement, $content);
    
    // Add trait to class
    if (strpos($content, 'use HasFactory, SoftDeletes;') !== false) {
        $content = str_replace('use HasFactory, SoftDeletes;', "use HasFactory, SoftDeletes, LogsActivity;", $content);
    } elseif (strpos($content, 'use HasFactory;') !== false) {
        $content = str_replace('use HasFactory;', "use HasFactory, LogsActivity;", $content);
    } else {
        $content = preg_replace('/class\s+[a-zA-Z0-9_]+\s+extends\s+[a-zA-Z0-9_]+\s*\{/', "$0\n    use LogsActivity;\n", $content);
    }
    
    // Add getActivitylogOptions method
    $method = "

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
";
    $content = preg_replace('/\}\s*$/', $method . "}\n", $content);
    
    file_put_contents($path, $content);
    echo "Updated " . $model . "\n";
}
