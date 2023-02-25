<?php

/** @noinspection JSUnresolvedVariable */
/** @noinspection JSUnusedLocalSymbols */

declare(strict_types=1);

namespace Abordage\HtmlMin\Tests;

use Abordage\HtmlMin\HtmlMin;
use PHPUnit\Framework\TestCase;

class HtmlMinTest extends TestCase
{
    private HtmlMin $htmlMin;

    protected function setUp(): void
    {
        $this->htmlMin = new HtmlMin();
        $this->htmlMin->findDoctypeInDocument(false);
        $this->htmlMin->removeWhitespaceBetweenTags();
    }

    public function testDoNotMinifyIfDoctypeIsNotFound(): void
    {
        $this->htmlMin->findDoctypeInDocument();
        $this->htmlMin->removeWhitespaceBetweenTags(false);

        $html = "<div> <a  href=''> doctype  not  found </a> </div>";
        $expected = "<div> <a  href=''> doctype  not  found </a> </div>";
        $this->assertEquals($expected, $this->htmlMin->minify($html));

        $this->htmlMin->findDoctypeInDocument(false);
        $this->htmlMin->removeWhitespaceBetweenTags();
    }

    public function testMinifyIfDoctypeIsFound(): void
    {
        $this->htmlMin->findDoctypeInDocument();

        $html = "<!DOCTYPE HTML><div> <a  href=''> doctype    found </a> </div>";
        $expected = "<!DOCTYPE HTML><div><a href=''>doctype found</a></div>";
        $this->assertEquals($expected, $this->htmlMin->minify($html));

        $this->htmlMin->findDoctypeInDocument(false);
    }

    public function testMinifyWithoutDoctype(): void
    {
        $html = "<div> <a href=''> abc  def </a> </div>";
        $expected = "<div><a href=''>abc def</a></div>";
        $this->assertEquals($expected, $this->htmlMin->minify($html));
    }

    public function testDoNotRemoveWhitespaceBetweenTags(): void
    {
        $this->htmlMin->removeWhitespaceBetweenTags(false);

        $html = "<div> <a  href=''> abc  def </a> </div>";
        $expected = "<div> <a href=''> abc def </a> </div>";
        $this->assertEquals($expected, $this->htmlMin->minify($html));

        $this->htmlMin->removeWhitespaceBetweenTags();
    }

    public function testRemoveWhitespaceBetweenTags(): void
    {
        $html = "<p>
                    <span  style='color:   #fff'>
                        <i> one </i> and <u> </u>
                        two
                    </span>
                </p>";
        $expected = "<p><span style='color: #fff'><i>one</i> and <u></u> two</span></p>";
        $this->assertEquals($expected, $this->htmlMin->minify($html));
    }

    public function testLivewire(): void
    {
        $html = "<div><!-- d --> a

        <? b   ?>  c

        <!--
        def()
        #'
        -->

        <!-- livewire -->
        <!-- Livewire Component -->
            {dd:xx}
        </div>";
        $expected = "<div>a <? b ?> c <!-- Livewire Component --> {dd:xx}</div>";
        $this->assertEquals($expected, $this->htmlMin->minify($html));
    }

    public function testStyleTag(): void
    {
        $html = "<style>
        a:  {
            color: inherit;
            background-color:  transparent;
            text-decoration: inherit
        }
        </style>";
        $expected = "<style>a: { color: inherit; background-color: transparent; text-decoration: inherit }</style>";
        $this->assertEquals($expected, $this->htmlMin->minify($html));
    }

    public function testTextarea(): void
    {
        $html = "<textarea>
        this
        is
        textarea
        </textarea>";
        $expected = "<textarea>
        this
        is
        textarea
        </textarea>";
        $this->assertEquals($expected, $this->htmlMin->minify($html));
    }

    public function testJavascript(): void
    {
        $this->htmlMin->removeBlankLinesInScriptElements();

        $html = <<<EOT
<footer>
        <script type="text/javascript">
            (function() {
                let a = 1
                const b = c
            })();
        </script>
    </footer>
EOT;

        $expected = <<<EOT
<footer><script type="text/javascript">(function() {
let a = 1
const b = c
})();</script></footer>
EOT;
        $this->assertEquals($expected, $this->htmlMin->minify($html));

        $this->htmlMin->removeBlankLinesInScriptElements(false);
    }
}
