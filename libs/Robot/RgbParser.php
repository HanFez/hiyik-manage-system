<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 16-7-24
 * Time: 下午7:38
 */

namespace Libs\Robot;
use Imagick;

class RgbParser implements InterfaceToneParser {
    const RGB_DELTA = 20;
    const MIN_NORM = 256;
    const MODE = InterfaceToneParser::COLOR_MODE[1];
    const TONE_DELTA = 0.1;
    const C = 'c';
    const P = 'p';
    public $width = 0;
    public $height = 0;
    public $imageFile = null;
    public $imagick = null;
    public $tones = null;

    public function __construct($filePath = null) {
        if(!is_null($filePath)) {
            $this->setImageFile($filePath);
        }
    }
    public function __destruct()
    {
        if($this->imagick != null) {
            $this->imagick->destroy();
        }
    }

    function setImageFile($filePath)
    {
        if(false !== realpath($filePath)) {
            $this->imageFile = realpath($filePath);
            try {
                if ($this->imagick != null) {
                    $this->imagick->readImage($this->imageFile);
                } else {
                    $this->imagick = new \Imagick($this->imageFile);
                }
                $width = $this->imagick->getImageWidth();
                $height = $this->imagick->getImageHeight();
                if($width > self::MIN_NORM || $height > self::MIN_NORM) {
                    if ($width > $height) {
                        $this->imagick->thumbnailImage(self::MIN_NORM, 0);
                    } else {
                        $this->imagick->thumbnailImage(0, self::MIN_NORM);
                    }
                    $width = $this->imagick->getImageWidth();
                    $height = $this->imagick->getImageHeight();
                }
                $this->width = $width;
                $this->height = $height;
            } catch (\Exception $ex) {
                return false;
            }
            return true;
        }
        return false;
    }
    function setImageBlob($blob)
    {
        if(!is_null($blob)) {
            try {
                if (!is_null($this->imagick)) {
                    $this->imagick->readImageBlob($blob);
                } else {
                    $this->imagick = new \Imagick();
                    $this->imagick->readImageBlob($blob);
                }
                $width = $this->imagick->getImageWidth();
                $height = $this->imagick->getImageHeight();
                if($width > self::MIN_NORM || $height > self::MIN_NORM) {
                    if ($width > $height) {
                        $this->imagick->thumbnailImage(self::MIN_NORM, 0);
                    } else {
                        $this->imagick->thumbnailImage(0, self::MIN_NORM);
                    }
                    $width = $this->imagick->getImageWidth();
                    $height = $this->imagick->getImageHeight();
                }
                $this->width = $width;
                $this->height = $height;
            } catch (\Exception $ex) {
                return false;
            }
            return true;
        }
        return false;
    }
    function getMainTones()
    {
        if(is_null($this->tones)) {
            $this->getTones();
        }
        $mainTones = [];
        $first = $this->tones[0];
        array_push($mainTones, $first);
        for($inx = 1; $inx < count($this->tones); $inx++) {
            if($this->tones[$inx][self::P] > 0.0
                && abs($first[self::P] - $this->tones[$inx][self::P]) < self::TONE_DELTA) {
                array_push($mainTones, $this->tones[$inx]);
            }
        }
        return $mainTones;
    }

    public function getParserMode() {
        return self::MODE;
    }

    function getTones($delta = null)
    {
        if($delta == null) {
            $statistics = $this->getRgbStatistics();
        } else {
            $statistics = $this->getRgbStatistics($delta);
        }
        return $statistics;
    }

    function getImageFile()
    {
        return $this->imageFile;
    }

    function getTotalPixels()
    {
        $total = 0;
        if($this->imagick != null) {
            $width = $this->imagick->getImageWidth();
            $height = $this->imagick->getImageHeight();
            $total = $width * $height;
        }
        return $total;
    }
    public function getRgbStatistics($delta = self::RGB_DELTA) {
        $colors = $this->getColors();
        $total = $this->getTotalPixels();
        $count = count($colors);
        for ($iInx = 0; $iInx < $count; $iInx++) {
            $r = $colors[$iInx]['r'];
            $g = $colors[$iInx]['g'];
            $b = $colors[$iInx]['b'];
            $o = $colors[$iInx]['a'];
            $c = $colors[$iInx][self::C];
            if($delta == 0) {
                $colors[$iInx][self::P] = $c/$total;
                continue;
            }
            if($c <= 0) {
                continue;
            }
            $rMax = $r + $delta;
            $gMax = $g + $delta;
            $bMax = $b + $delta;
            $rMin = $r - $delta;
            $gMin = $g - $delta;
            $bMin = $b - $delta;
            for ($jInx = $iInx + 1; $jInx < $count; $jInx++) {
                $subr = $colors[$jInx]['r'];
                $subg = $colors[$jInx]['g'];
                $subb = $colors[$jInx]['b'];
                $subo = $colors[$jInx]['a'];
                $subc = $colors[$jInx][self::C];
                if($subc != 0) {
                    if((($rMin < $subr && $subr < $rMax)
                        && ($gMin < $subg && $subg < $gMax)
                        && ($bMin < $subb && $subb < $bMax))
                    ) {
                        $c = $c + $subc;
                        $colors[$jInx][self::C] = 0;
                    }
                }
            }
            $colors[$iInx][self::C] = $c;
            $colors[$iInx][self::P] = $c/$total;
        }
        $colors = array_filter($colors, function($val) {
            if($val[self::C] > 0) {
                return true;
            }
            return false;
        });
        usort($colors, function($a, $b){
            if($a[self::P] < $b[self::P]) {
                return true;
            }
            return false;
        });
        return $colors;
    }
    public function getColors() {
        $colors = [];
        if($this->imagick != null) {
            $histogramElements = $this->imagick->getImageHistogram();
            foreach ($histogramElements as $p) {
                $color = $p->getColor();
                $cnt = $p->getColorCount();
                $color[self::C] = $cnt;
                array_push($colors, $color);
            }
        }
        return $colors;
    }
    public function testOutput() {
        $this->outputImage($this->imagick);
        $this->outputRgba($this->getTones(20));
    }
    function outputImage(Imagick $imagick) {
        echo '<img src="data:image/jpeg;base64,'.base64_encode($imagick->getImageBlob()).'">';
        $tempImg = clone $imagick;
        $tempImg->transformImageColorspace(Imagick::COLORSPACE_HSL);
        $tempImg->separateImageChannel(Imagick::CHANNEL_RED);
        echo '<img src="data:image/jpeg;base64,'.base64_encode($tempImg->getImageBlob()).'">';
        $tempImg = clone $imagick;
        $tempImg->transformImageColorspace(Imagick::COLORSPACE_HSL);
        $tempImg->separateImageChannel(Imagick::CHANNEL_GREEN);
        echo '<img src="data:image/jpeg;base64,'.base64_encode($tempImg->getImageBlob()).'">';
        $tempImg = clone $imagick;
        $tempImg->transformImageColorspace(Imagick::COLORSPACE_HSL);
        $tempImg->separateImageChannel(Imagick::CHANNEL_BLUE);
        echo '<img src="data:image/jpeg;base64,'.base64_encode($tempImg->getImageBlob()).'">';
        $tempImg = clone $imagick;
        $tempImg->transformImageColorspace(Imagick::COLORSPACE_HSL);
        $tempImg->separateImageChannel(Imagick::CHANNEL_CYAN);
        echo '<img src="data:image/jpeg;base64,'.base64_encode($tempImg->getImageBlob()).'">';
        $tempImg = clone $imagick;
        $tempImg->transformImageColorspace(Imagick::COLORSPACE_HSL);
        $tempImg->separateImageChannel(Imagick::CHANNEL_YELLOW);
        echo '<img src="data:image/jpeg;base64,'.base64_encode($tempImg->getImageBlob()).'">';
        $tempImg = clone $imagick;
        $tempImg->transformImageColorspace(Imagick::COLORSPACE_HSL);
        $tempImg->separateImageChannel(Imagick::CHANNEL_MAGENTA);
        echo '<img src="data:image/jpeg;base64,'.base64_encode($tempImg->getImageBlob()).'">';
    }
    function outputRgba(array $result) {
        foreach($result as $item) {
            $hexColor = sprintf("#%02X%02X%02X", $item['r'], $item['g'], $item['b']);
            echo '<div style="width:60px;height:20px;background:'.$hexColor.'"/>';
            echo '<div style="margin-left: 80px;">'.$hexColor.'='.sprintf("%0.4f%%", $item[self::P] *  100).'</div>';
        }
    }

}