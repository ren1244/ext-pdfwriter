<?php

namespace ren1244\PDFWriter\Ext;

use ren1244\PDFWriter\Module\ModuleInterface;
use ren1244\PDFWriter\PageMetrics;
use ren1244\PDFWriter\StreamWriter;
use Svg\Document;

class Svg implements ModuleInterface
{
    private $mtx;

    public function __construct(PageMetrics $mtx)
    {
        $this->mtx = $mtx;
    }

    public function addSvg($svg, $x = 0, $y = 0, $w = null, $h = null)
    {
        $doc = new Document();
        $doc->loadFile($svg);
        $info = $doc->getDimensions();
        if ($w === null && $h === null) {
            // 設 dpi 為 96
            $scaleX = $scaleY = 72 / 96;
        } elseif ($w === null) {
            $scaleX = $scaleY = PageMetrics::getPt($h) / $info['height'];
        } elseif ($h === null) {
            $scaleX = $scaleY = PageMetrics::getPt($w) / $info['width'];
        } else {
            $scaleX = PageMetrics::getPt($w) / $info['width'];
            $scaleY = PageMetrics::getPt($h) / $info['height'];
        }

        $scaleY = -$scaleY;
        $y = $this->mtx->height - PageMetrics::getPt($y);
        $x = PageMetrics::getPt($x);
        $ps = new SurfacePs($info);
        $doc->render($ps);
        $this->mtx->pushData($this, sprintf('q %.3f 0 0 %.3f %.3f %.3f cm ', $scaleX, $scaleY, $x, $y) . $ps->getPostscript(). ' Q');
    }

    public function write(StreamWriter $writer, array $data)
    {
        return $writer->writeStream(implode(' ', $data), StreamWriter::COMPRESS);
    }
}