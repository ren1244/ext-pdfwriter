<?php

namespace ren1244\PDFWriter\Ext;

use Svg\Surface\SurfaceInterface;
use Svg\Style;
use Exception;

class SurfacePs implements SurfaceInterface
{
    private $style;
    private $ps = [];

    public function save()
    {
        $this->ps[] = 'q';
    }

    public function restore()
    {
        $this->ps[] = 'Q';
    }

    public function scale($x, $y)
    {
        $this->ps[] = sprintf('%.3f 0 0 %.3f 0 0 cm', $x, $y);
    }

    public function rotate($angle)
    {
        $s = sin($angle / 180 * pi());
        $c = cos($angle / 180 * pi());
        $this->ps[] = sprintf('%.3f %.3f %.3f %.3f 0 0 cm', $c, $s, -$s, $c);
    }

    public function translate($x, $y)
    {
        $this->ps[] = sprintf('1 0 0 1 %.3f %.3f cm', $x, $y);
    }

    public function transform($a, $b, $c, $d, $e, $f)
    {
        $this->ps[] = sprintf('%.3f %.3f %.3f %.3f %.3f %.3f cm', $a, $b, $c, $d, $e, $f);
    }

    // path ends
    public function beginPath()
    {
    }

    public function closePath()
    {
        $this->ps[] = 'h';
    }

    public function fill()
    {
        switch ($this->style->fillRule) {
            case 'nonzero':
                $this->ps[] = "f";
                break;
            case 'evenodd':
                $this->ps[] = "f*";
                break;
            default:
                throw new Exception('unknow fillRule');
        }
    }

    public function stroke(bool $close = false)
    {
        $this->ps[] = $close ? "s" : "S";
    }

    public function endPath()
    {
        $this->ps[] = 'n';
    }

    public function fillStroke(bool $close = false)
    {
        $this->ps[] = ($close ? "b" : "B") . ($this->style->fillRule === "evenodd" ? "*" : "");
    }

    public function clip()
    {
        throw new Exception('not implent ' . __FUNCTION__);
    }

    // text (see also the CanvasDrawingStyles interface)
    public function fillText($text, $x, $y, $maxWidth = null)
    {
        throw new Exception('not implent ' . __FUNCTION__);
    }

    public function strokeText($text, $x, $y, $maxWidth = null)
    {
        throw new Exception('not implent ' . __FUNCTION__);
    }

    public function measureText($text)
    {
        throw new Exception('not implent ' . __FUNCTION__);
    }

    // drawing images
    public function drawImage($image, $sx, $sy, $sw = null, $sh = null, $dx = null, $dy = null, $dw = null, $dh = null)
    {
        throw new Exception('not implent ' . __FUNCTION__);
    }

    // paths
    public function lineTo($x, $y)
    {
        $this->ps[] = sprintf('%.3f %.3f l', $x, $y);
    }

    public function moveTo($x, $y)
    {
        $this->ps[] = sprintf('%.3f %.3f m', $x, $y);
    }

    public function quadraticCurveTo($cpx, $cpy, $x, $y)
    {
        $this->ps[] = sprintf('%.3f %.3f %.3f %.3f v', $cpx, $cpy, $x, $y);
    }

    public function bezierCurveTo($cp1x, $cp1y, $cp2x, $cp2y, $x, $y)
    {
        $this->ps[] = sprintf('%.3f %.3f %.3f %.3f %.3f %.3f c', $cp1x, $cp1y, $cp2x, $cp2y, $x, $y);
    }

    public function arcTo($x1, $y1, $x2, $y2, $radius)
    {
        throw new Exception('not implent ' . __FUNCTION__);
    }

    public function circle($x, $y, $radius)
    {
        throw new Exception('not implent ' . __FUNCTION__);
    }

    public function arc($x, $y, $radius, $startAngle, $endAngle, $anticlockwise = false)
    {
        throw new Exception('not implent ' . __FUNCTION__);
    }

    public function ellipse($x, $y, $radiusX, $radiusY, $rotation, $startAngle, $endAngle, $anticlockwise)
    {
        throw new Exception('not implent ' . __FUNCTION__);
    }

    // Rectangle
    public function rect($x, $y, $w, $h, $rx = 0, $ry = 0)
    {
        $this->ps[] = sprintf('%.3f %.3f %.3f %.3f re', $x, $y, $w, $h);
    }

    public function fillRect($x, $y, $w, $h)
    {
        $this->rect($x, $y, $w, $h);
        $this->fill();
    }

    public function strokeRect($x, $y, $w, $h)
    {
        $this->rect($x, $y, $w, $h);
        $this->stroke();
    }

    public function setStyle(Style $style)
    {
        $this->style = $style;
        if (is_array($style->stroke) && $stroke = $style->stroke) {
            $this->ps[] = sprintf(
                '%.3f %.3f %.3f RG %.3f w',
                $stroke[0] / 255,
                $stroke[1] / 255,
                $stroke[2] / 255,
                $style->strokeWidth
            );
        }

        if (is_array($style->fill) && $fill = $style->fill) {
            $this->ps[] = sprintf(
                '%.3f %.3f %.3f rg',
                $fill[0] / 255,
                $fill[1] / 255,
                $fill[2] / 255
            );
        }
    }

    /**
     * @return Style
     */
    public function getStyle()
    {
        return $this->style;
    }

    public function setFont($family, $style, $weight)
    {

        throw new Exception('not implent ' . __FUNCTION__);
    }

    public function getPostscript()
    {
        return implode(" ", $this->ps);
    }
}
