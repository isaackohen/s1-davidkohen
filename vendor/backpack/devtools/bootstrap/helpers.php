<?php

if (!function_exists('link_to_code_editor')) {
    function link_to_code_editor($path, $editor = null)
    {
        $editor = $editor ?? config('backpack.devtools.editor');

        $editors = [
            'vscode' => 'vscode://file/%path',
            'vscode-insiders' => 'vscode-insiders://file/%path',
            'sublime' => 'sublime://open?url=file://%path',
            'subl' => 'subl://open?url=file://%path',
            'textmate' => 'textmate://open?url=file://%path',
            'emacs' => 'emacs://open?url=file://%path',
            'macvim' => 'macvim://open/?url=file://%path',
            'phpstorm' => 'phpstorm://open?file=%path',
            'idea' => 'idea://open?file=%path',
            'atom' => 'atom://core/open/file?filename=%path',
            'nova' => 'nova://core/open/file?filename=%path',
            'netbeans' => 'netbeans://open/?f=%path',
            'xdebug' => 'xdebug://%path',
        ];

        return str_replace('%path', $path, $editors[$editor] ?? '');
    }
}
