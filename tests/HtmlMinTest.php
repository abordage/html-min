<?php

namespace Abordage\HtmlMin\Tests;

use Abordage\HtmlMin\HtmlMin;
use PHPUnit\Framework\TestCase;

class HtmlMinTest extends TestCase
{
    public function testMinify(): void
    {
        $htmlMin = new HtmlMin();

        /** test */
        $html = "<div> <a  href=''> doctype  not  found </a> </div>";
        $expected = "<div> <a  href=''> doctype  not  found </a> </div>";
        $this->assertEquals($expected, $htmlMin->minify($html));

        /** test */
        $html = "<!DOCTYPE HTML><div> <a  href=''> doctype    found </a> </div>";
        $expected = "<!DOCTYPE HTML><div><a href=''>doctype found</a></div>";
        $this->assertEquals($expected, $htmlMin->minify($html));
        $htmlMin->findDoctypeInDocument(false);


        /** test */
        $html = "<div> <a href=''> abc  def </a> </div>";
        $expected = "<div><a href=''>abc def</a></div>";
        $this->assertEquals($expected, $htmlMin->minify($html));

        /** test */
        $htmlMin->removeWhitespaceBetweenTags(false);
        $html = "<div> <a  href=''> abc  def </a> </div>";
        $expected = "<div> <a href=''> abc def </a> </div>";
        $this->assertEquals($expected, $htmlMin->minify($html));
        $htmlMin->removeWhitespaceBetweenTags();

        /** test */
        $html = "<p>
                    <span  style='color:   #fff'>
                        <i> one </i> and <u> </u>
                        two
                    </span>
                </p>";
        $expected = "<p><span style='color: #fff'><i>one</i> and <u></u> two</span></p>";
        $this->assertEquals($expected, $htmlMin->minify($html));

        /** test */
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
        $this->assertEquals($expected, $htmlMin->minify($html));

        /** test */
        $html = "<style>
        a:  {
            color: inherit;
            background-color:  transparent;
            text-decoration: inherit
        }
        </style>";
        $expected = "<style>a: { color: inherit; background-color: transparent; text-decoration: inherit }</style>";
        $this->assertEquals($expected, $htmlMin->minify($html));

        /** test */
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
        $this->assertEquals($expected, $htmlMin->minify($html));


        /** test */
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

        $this->assertEquals($expected, $htmlMin->removeBlankLinesInScriptElements()->minify($html));
    }
}
