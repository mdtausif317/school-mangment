<?php

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Designation;
use App\Models\PageAuth;
use App\Models\PageButton;
use App\Models\PageMenu;
use App\Models\School;
use App\Models\User;

function printTable(string $title, array $headers, iterable $rows): void
{
    echo "\n=== {$title} ===\n";
    $rows = is_array($rows) ? $rows : iterator_to_array($rows);

    if ($rows === []) {
        echo "(empty)\n";
        return;
    }

    echo implode(' | ', $headers)."\n";
    echo str_repeat('-', 80)."\n";

    foreach ($rows as $row) {
        $line = [];
        foreach ($headers as $header) {
            $key = strtolower(str_replace(' ', '_', $header));
            $value = is_array($row) ? ($row[$key] ?? '') : ($row->{$key} ?? '');
            $line[] = (string) $value;
        }
        echo implode(' | ', $line)."\n";
    }
}

printTable('schools', ['id', 'name', 'slug', 'email', 'is_active'], School::all()->map(fn ($s) => [
    'id' => $s->id,
    'name' => $s->name,
    'slug' => $s->slug,
    'email' => $s->email ?? '-',
    'is_active' => $s->is_active ? 'yes' : 'no',
]));

printTable('users', ['id', 'school_id', 'designation_id', 'user_type', 'name', 'email'], User::all()->map(fn ($u) => [
    'id' => $u->id,
    'school_id' => $u->school_id ?? '-',
    'designation_id' => $u->designation_id ?? '-',
    'user_type' => $u->user_type,
    'name' => $u->name,
    'email' => $u->email,
]));

printTable('designations', ['id', 'school_id', 'name', 'slug'], Designation::orderBy('school_id')->get()->map(fn ($d) => [
    'id' => $d->id,
    'school_id' => $d->school_id,
    'name' => $d->name,
    'slug' => $d->slug,
]));

printTable('pages_menu_list', ['id', 'school_id', 'parent_id', 'title', 'slug', 'sort_order', 'display'], PageMenu::orderBy('sort_order')->get()->map(fn ($m) => [
    'id' => $m->id,
    'school_id' => $m->school_id ?? '-',
    'parent_id' => $m->parent_id ?? '-',
    'title' => $m->title,
    'slug' => $m->slug,
    'sort_order' => $m->sort_order,
    'display' => $m->display ? 'hidden' : 'visible',
]));

printTable('pages_auth', ['id', 'school_id', 'menu_id', 'user_id', 'designation_id'], PageAuth::all()->map(fn ($a) => [
    'id' => $a->id,
    'school_id' => $a->school_id ?? '-',
    'menu_id' => $a->menu_id,
    'user_id' => $a->user_id ?? '-',
    'designation_id' => $a->designation_id ?? '-',
]));

printTable('pages_buttons', ['id', 'menu_id', 'button_title', 'button_link', 'status'], PageButton::all()->map(fn ($b) => [
    'id' => $b->id,
    'menu_id' => $b->menu_id,
    'button_title' => $b->button_title,
    'button_link' => $b->button_link,
    'status' => $b->status ? 'inactive' : 'active',
]));
