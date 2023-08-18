<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

class ConvertTest extends TestCase
{
    public function testConvert()
    {
        $output = shell_exec('php convert.php');

        $this->assertStringContainsString('Conversion Finished', $output);
    }
}
