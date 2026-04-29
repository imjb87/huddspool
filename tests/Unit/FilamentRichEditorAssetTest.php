<?php

namespace Tests\Unit;

use Tests\TestCase;

class FilamentRichEditorAssetTest extends TestCase
{
    public function test_published_filament_rich_editor_asset_uses_valid_blur_event_names(): void
    {
        $asset = file_get_contents(public_path('js/filament/forms/components/rich-editor.js'));

        $this->assertIsString($asset);
        $this->assertStringNotContainsString('blur-sm', $asset);
        $this->assertStringNotContainsString('??', $asset);
        $this->assertStringNotContainsString('window?.getSelection()', $asset);
        $this->assertStringNotContainsString('findLast(', $asset);
        $this->assertStringContainsString('setMeta("blur",{event:', $asset);
        $this->assertStringContainsString('this.on("blur",this.options.onBlur)', $asset);
        $this->assertStringContainsString('this.$wire.commit()', $asset);
    }
}
