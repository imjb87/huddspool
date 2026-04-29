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
        $this->assertStringContainsString('Se.blur=', $asset);
        $this->assertStringContainsString('this.on("blur",this.options.onBlur)', $asset);
        $this->assertStringContainsString('z.on("blur",()=>{V||this.$wire.commit()})', $asset);
    }
}
