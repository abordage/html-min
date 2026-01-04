<?php

/**
 * @noinspection HtmlUnknownTarget
 * @noinspection JSUnresolvedVariable
 * @noinspection JSUnusedLocalSymbols
 */

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

    public function testKnockout(): void
    {
        $html = "<div>
        <!-- ko if: true -->
            <span>First item</span>
        <!-- /ko -->

        <!-- ko template: getTemplate() --><!-- /ko -->

        </div>";
        $expected = "<div><!-- ko if: true --><span>First item</span><!-- /ko --><!-- ko template: getTemplate() --><!-- /ko --></div>";
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

    public function testRemoveTrailingSlashes(): void
    {
        $this->htmlMin->removeTrailingSlashes();

        $html = '<link rel="stylesheet" href="style.css" /><meta charset="UTF-8" /><img src="image.png"  alt=""/>';
        $expected = '<link rel="stylesheet" href="style.css"><meta charset="UTF-8"><img src="image.png" alt="">';
        $this->assertEquals($expected, $this->htmlMin->minify($html));

        $this->htmlMin->removeTrailingSlashes(false);
    }

    public function testRemoveTrailingSlashesWithAttributes(): void
    {
        $this->htmlMin->removeTrailingSlashes();

        $html = '<link rel="preload" as="style" href="app.css" /><input type="text" name="test" />';
        $expected = '<link rel="preload" as="style" href="app.css"><input type="text" name="test">';
        $this->assertEquals($expected, $this->htmlMin->minify($html));

        $this->htmlMin->removeTrailingSlashes(false);
    }

    public function testDoNotRemoveTrailingSlashesWhenDisabled(): void
    {
        $this->htmlMin->removeTrailingSlashes(false);

        $html = '<link rel="stylesheet" href="style.css" />';
        $expected = '<link rel="stylesheet" href="style.css" />';
        $this->assertEquals($expected, $this->htmlMin->minify($html));
    }

    public function testRemoveTrailingSlashesPreservesNonVoidElements(): void
    {
        $this->htmlMin->removeTrailingSlashes();

        $html = '<div><link href="style.css" /><span>text</span></div>';
        $expected = '<div><link href="style.css"><span>text</span></div>';
        $this->assertEquals($expected, $this->htmlMin->minify($html));

        $this->htmlMin->removeTrailingSlashes(false);
    }
}
