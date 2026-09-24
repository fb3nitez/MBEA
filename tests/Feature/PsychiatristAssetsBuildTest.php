<?php

/*
 * Safeguards for the split psychiatrist asset modules.
 *
 * The psychiatrist layout loads these files directly in dependency order.
 * A generated psychiatrist.js bundle is intentionally not used or required.
 */

const PSYCHIATRIST_MODULES = [
    'psychiatrist_core.js',
    'psychiatrist_patients.js',
    'psychiatrist_consultations.js',
    'psychiatrist_assessments.js',
    'psychiatrist_lifestyle.js',
    'psychiatrist_prescriptions.js',
    'psychiatrist_init.js',
];

function psychiatristModulePaths(): array
{
    return array_map(
        fn (string $file) => public_path('js/psych/'.$file),
        PSYCHIATRIST_MODULES
    );
}

function psychiatristLayoutSource(): string
{
    return file_get_contents(resource_path('views/layouts/psychiatrist.blade.php'));
}

test('all psychiatrist modules exist', function () {
    foreach (psychiatristModulePaths() as $path) {
        expect(file_exists($path))->toBeTrue('Missing module file: '.basename($path));
    }
});

test('psychiatrist layout loads split modules in dependency order', function () {
    $layout = psychiatristLayoutSource();
    $offset = 0;

    foreach (PSYCHIATRIST_MODULES as $module) {
        $moduleMarker = "'{$module}'";
        $found = strpos($layout, $moduleMarker, $offset);

        expect($found)->not->toBeFalse("Missing or out-of-order module {$module}");
        $offset = $found;
    }

    expect($layout)->not->toContain("asset('js/psych/psychiatrist.js')");
    expect($layout)->toContain("asset('js/psych/' . \$psychScript)");
});

test('core and init are independently loadable module boundaries', function () {
    $core = file_get_contents(public_path('js/psych/psychiatrist_core.js'));
    $init = file_get_contents(public_path('js/psych/psychiatrist_init.js'));

    expect($core)->not->toContain("document.addEventListener('DOMContentLoaded'");
    expect(trim($init))->not->toEndWith('});');
});

test('prescription module contains modal print functions', function () {
    $source = file_get_contents(public_path('js/psych/psychiatrist_prescriptions.js'));

    expect($source)->toContain('function showPrintPreview(');
    expect($source)->toContain('window.printRx');
    expect($source)->toContain('window.printDx');
    expect($source)->not->toMatch('/window\\.open\\s*\\(/');
});
