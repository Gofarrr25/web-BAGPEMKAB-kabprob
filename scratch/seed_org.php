<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\OrganizationMember;

OrganizationMember::truncate();

$kepala = OrganizationMember::create([
    'name' => 'Hudan Syarifuddin, S.Sos., M.Si',
    'position' => 'KEPALA DINAS',
    'type' => 'person',
    'order_index' => 1
]);

OrganizationMember::create([
    'name' => null,
    'position' => 'KELOMPOK JABATAN FUNGSIONAL',
    'type' => 'functional_group',
    'parent_id' => $kepala->id,
    'order_index' => 1
]);

$sekretaris = OrganizationMember::create([
    'name' => 'Wahyu Hidayat',
    'position' => 'SEKRETARIS',
    'type' => 'person',
    'parent_id' => $kepala->id,
    'order_index' => 2
]);

OrganizationMember::create([
    'name' => 'Sri Hidayati, SE',
    'position' => 'Kasubag Umum dan Kepegawaian',
    'type' => 'person',
    'parent_id' => $sekretaris->id,
    'order_index' => 1
]);

OrganizationMember::create([
    'name' => 'Hasyim Ashari, SH. MM',
    'position' => 'Kasubag Perencanaan dan Keuangan',
    'type' => 'person',
    'parent_id' => $sekretaris->id,
    'order_index' => 2
]);

$bidang1 = OrganizationMember::create([
    'name' => 'Dody Kasman, S.Sos',
    'position' => 'KEPALA BIDANG INFORMASI DAN KOMUNIKASI PUBLIK',
    'type' => 'person',
    'parent_id' => $kepala->id,
    'order_index' => 3
]);
OrganizationMember::create([
    'position' => 'KELOMPOK JABATAN FUNGSIONAL',
    'type' => 'functional_group',
    'parent_id' => $bidang1->id,
    'order_index' => 1
]);

$bidang2 = OrganizationMember::create([
    'name' => 'Rahadi SK., S.Kom.,M.Eng.',
    'position' => 'KEPALA BIDANG TEKNOLOGI DAN INFORMASI',
    'type' => 'person',
    'parent_id' => $kepala->id,
    'order_index' => 4
]);
OrganizationMember::create([
    'position' => 'KELOMPOK JABATAN FUNGSIONAL',
    'type' => 'functional_group',
    'parent_id' => $bidang2->id,
    'order_index' => 1
]);

$bidang3 = OrganizationMember::create([
    'name' => 'Dodik Budianto, S.Sos, M.Si',
    'position' => 'KEPALA BIDANG STATISTIK DAN PERSANDIAN',
    'type' => 'person',
    'parent_id' => $kepala->id,
    'order_index' => 5
]);
OrganizationMember::create([
    'position' => 'KELOMPOK JABATAN FUNGSIONAL',
    'type' => 'functional_group',
    'parent_id' => $bidang3->id,
    'order_index' => 1
]);

echo "Seeded!";
