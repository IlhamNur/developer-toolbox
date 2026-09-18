<?php

use App\Livewire\Dashboard;
use App\Livewire\Tools\Api\Workbench as ApiWorkbench;
use App\Livewire\Tools\Color\Workbench as ColorWorkbench;
use App\Livewire\Tools\DateTime\Workbench as DateTimeWorkbench;
use App\Livewire\Tools\Developer\Workbench as DeveloperWorkbench;
use App\Livewire\Tools\Encoding\Workbench as EncodingWorkbench;
use App\Livewire\Tools\Generator\Workbench as GeneratorWorkbench;
use App\Livewire\Tools\Json\Workbench;
use App\Livewire\Tools\Network\Workbench as NetworkWorkbench;
use App\Livewire\Tools\Number\Workbench as NumberWorkbench;
use App\Livewire\Tools\Security\Workbench as SecurityWorkbench;
use App\Livewire\Tools\Text\Workbench as TextWorkbench;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('dashboard');

foreach (['json-formatter', 'json-validator', 'json-minifier', 'json-escape', 'json-unescape', 'json-diff'] as $jsonTool) {
    Route::get("/tools/{$jsonTool}", Workbench::class)
        ->defaults('slug', $jsonTool)
        ->name("tools.{$jsonTool}");
}

foreach (['base64', 'url-encode', 'html-encode'] as $encodingTool) {
    Route::get("/tools/{$encodingTool}", EncodingWorkbench::class)
        ->defaults('slug', $encodingTool)
        ->name("tools.{$encodingTool}");
}

foreach (['uuid-generator', 'random-string-generator', 'password-generator'] as $generatorTool) {
    Route::get("/tools/{$generatorTool}", GeneratorWorkbench::class)
        ->defaults('slug', $generatorTool)
        ->name("tools.{$generatorTool}");
}

Route::get('/tools/timestamp', DateTimeWorkbench::class)
    ->defaults('slug', 'timestamp')
    ->name('tools.timestamp');

foreach (['curl-generator', 'http-request'] as $apiTool) {
    Route::get("/tools/{$apiTool}", ApiWorkbench::class)
        ->defaults('slug', $apiTool)
        ->name("tools.{$apiTool}");
}

foreach (['regex-tester', 'jwt-decoder', 'dummy-json'] as $developerTool) {
    Route::get("/tools/{$developerTool}", DeveloperWorkbench::class)
        ->defaults('slug', $developerTool)
        ->name("tools.{$developerTool}");
}

foreach (['text-case', 'text-cleaner', 'slug-generator'] as $textTool) {
    Route::get("/tools/{$textTool}", TextWorkbench::class)
        ->defaults('slug', $textTool)
        ->name("tools.{$textTool}");
}

foreach (['hash-generator', 'hash-compare'] as $securityTool) {
    Route::get("/tools/{$securityTool}", SecurityWorkbench::class)
        ->defaults('slug', $securityTool)
        ->name("tools.{$securityTool}");
}

foreach (['number-format', 'number-base'] as $numberTool) {
    Route::get("/tools/{$numberTool}", NumberWorkbench::class)
        ->defaults('slug', $numberTool)
        ->name("tools.{$numberTool}");
}

foreach (['color-hex-rgb', 'color-rgb-hex'] as $colorTool) {
    Route::get("/tools/{$colorTool}", ColorWorkbench::class)
        ->defaults('slug', $colorTool)
        ->name("tools.{$colorTool}");
}

foreach (['ip-validator', 'ip-classifier'] as $networkTool) {
    Route::get("/tools/{$networkTool}", NetworkWorkbench::class)
        ->defaults('slug', $networkTool)
        ->name("tools.{$networkTool}");
}

Route::view('/tools/{slug}', 'tools.placeholder')
    ->where('slug', '^(?!(json-formatter|json-validator|json-minifier|json-escape|json-unescape|json-diff|base64|url-encode|html-encode|uuid-generator|random-string-generator|password-generator|timestamp|curl-generator|http-request|regex-tester|jwt-decoder|dummy-json|text-case|text-cleaner|slug-generator|hash-generator|hash-compare|number-format|number-base|color-hex-rgb|color-rgb-hex|ip-validator|ip-classifier)$)[a-z0-9-]+$')
    ->name('tools.show');
