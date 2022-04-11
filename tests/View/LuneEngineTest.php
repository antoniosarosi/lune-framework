<?php

namespace Lune\Tests\View;

use Lune\View\LuneEngine;
use PHPUnit\Framework\TestCase;

class LuneEngineTest extends TestCase {
    public function testRendersTemplateWithParameters() {
        $parameter1 = "Test 1";
        $parameter2 = 2;

        $engine = new LuneEngine(__DIR__."/test-views");

        $content = $engine->render("view", compact('parameter1', 'parameter2'), "layout");

        $expected = "
            <html>
                <body>
                    <h1>$parameter1</h1>
                    <h2>$parameter2</h2>
                </body>
            </html>
        ";


        $this->assertEquals(
            preg_replace("/\s*/m", "", $expected),
            preg_replace("/\s*/m", "", $content)
        );
    }
}
