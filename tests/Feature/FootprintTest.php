<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Process;
use Tests\TestCase;

/**
 * CLAUDE.md's footprint rule: the commercial product this standard was once
 * studied from is never named anywhere in the repository.
 */
class FootprintTest extends TestCase
{
    public function test_the_original_product_is_not_named_anywhere(): void
    {
        // The word is assembled so this file does not name it either.
        $word = 'accu'.'rate';

        $result = Process::path(base_path())->run(['git', 'grep', '-i', '-l', $word, '--', '.']);

        $this->assertSame('', trim($result->output()), "Named in:\n".$result->output());
    }
}
