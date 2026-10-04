<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Process;
use Tests\TestCase;

/**
 * CLAUDE.md's footprint rule: the reference product is named only inside the
 * study tool (which signs in to it) and in the raw study data it wrote.
 */
class FootprintTest extends TestCase
{
    public function test_the_reference_product_is_not_named_outside_the_study_tool(): void
    {
        // The word is assembled so this file does not name it either.
        $word = 'accu'.'rate';

        $result = Process::path(base_path())->run([
            'git', 'grep', '-i', '-l', $word, '--', '.',
            ':!tools/referensi-scan', ':!docs/referensi/scan.json',
        ]);

        $this->assertSame('', trim($result->output()), "Named in:\n".$result->output());
    }
}
