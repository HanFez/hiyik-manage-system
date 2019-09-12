<?php
namespace Libs\Robot;
class HslParser implements InterfaceToneParser {
    const HSL_SPACE = ['red' => 0, 'yellowRed' => 30, 'yellow' => 60,
        'greenYellow' => 90, 'green' => 120, 'cyanGreen' => 150,
        'cyan' => 180, 'blueCyan' => 210, 'blue' => 240,
        'magentaBlue' => 270, 'magenta' => 300, 'redMagenta' => 330,
    ];
    const MODE = 'hsl';//InterfaceToneParser::COLOR_MODE[0];
    const HSL_DELTA = 15;
    const MIN_NORM = 256;
    const TONE_DELTA = 0.1;
    const DARK = 'dark';
    const LIGHT = 'light';
    const PERCENT = 'percent';
    const BLACK = 'black';
    const WHITE = 'white';
    const GRAY_DARK = 'grayDark';
    const GRAY_LIGHT = 'grayLight';
    const C = 'c';
    const P = 'p';
    public $imageFile = null;
    public $imagick = null;
    public $width = 0;
    public $height = 0;
    public $tones = null;

    public function __construct($filePath = null) {
        if(!is_null($filePath)) {
            $this->setImageFile($filePath);
        }
    }
    public function __destruct()
    {
        if(!is_null($this->imagick)) {
            $this->imagick->destroy();
        }
    }

    function setImageFile($filePath)
    {
        if(false !== realpath($filePath)) {
            $this->imageFile = realpath($filePath);
            try {
                if (!is_null($this->imagick)) {
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

    function getTones($delta=null)
    {
        $result = [];
        $statistics = $this->getHslStatistics();
        foreach($statistics as $key => $val) {
            if($key == self::BLACK) {
                $rgb = $this->hslToRgb(0, 1.0, 0.0);
                $total = $val[self::DARK] + $val[self::LIGHT];
                $rgb[self::C] = $total;
                $rgb[self::P] = $val[self::PERCENT];
            } else if($key == self::WHITE) {
                $rgb = $this->hslToRgb(0, 1.0, 1.0);
                $total = $val[self::DARK] + $val[self::LIGHT];
                $rgb[self::C] = $total;
                $rgb[self::P] = $val[self::PERCENT];
            } else if($key == self::GRAY_DARK) {
                $rgb = $this->hslToRgb(0, 0.125, 0.3125);
                $total = $val[self::DARK] + $val[self::LIGHT];
                $rgb[self::C] = $total;
                $rgb[self::P] = $val[self::PERCENT];
            } else if($key == self::GRAY_LIGHT) {
                $rgb = $this->hslToRgb(0, 0.125, 0.6875);
                $total = $val[self::DARK] + $val[self::LIGHT];
                $rgb[self::C] = $total;
                $rgb[self::P] = $val[self::PERCENT];
            } else {
                $rgb = $this->hslToRgb(self::HSL_SPACE[$key], 1.0, 0.5);
                $total = $val[self::DARK] + $val[self::LIGHT];
                $rgb[self::C] = $total;
                $rgb[self::P] = $val[self::PERCENT];
            }
            $result[$key] = $rgb;
        }
        $this->tones = $result;
        return $this->tones;
    }

    public function getMainTones() {
        if(is_null($this->tones)) {
            $this->getTones();
        }
        $mainTones = [];
        $first = array_values($this->tones)[0];
        $first['color'] = array_keys($this->tones)[0];
        array_push($mainTones, $first);
        for($inx = 1; $inx < count($this->tones); $inx++) {
            //if(array_values($this->tones)[$inx][self::P] > 0.0) {
                $temp = array_values($this->tones)[$inx];
                $temp['color'] = array_keys($this->tones)[$inx];
                array_push($mainTones, $temp);
            //}
        }

        return [self::MODE_NAME => self::MODE, self::VALUES => $mainTones];
    }
    public function getParserMode() {
        return self::MODE;
    }

    function getImageFile()
    {
        return $this->imageFile;
    }

    function getTotalPixels()
    {
        $total = $this->width * $this->height;
        return $total;
    }
    public function getHslStatistics($delta = self::HSL_DELTA) {
        $colorStatistics = [];
        if(!is_null($this->imagick)) {
            $width = $this->imagick->getImageWidth();
            $height = $this->imagick->getImageHeight();
            $totalPixels = $width * $height;
            $colors = $this->getColors();
            foreach (self::HSL_SPACE as $key => $val) {
                $colorStatistics[$key] = [self::DARK => 0, self::LIGHT => 0];
            }
            $black = [self::DARK => 0, self::LIGHT => 0];
            $white = [self::DARK => 0, self::LIGHT => 0];
            $greyDark = [self::DARK => 0, self::LIGHT => 0];
            $greyLight = [self::DARK => 0, self::LIGHT => 0];

            foreach (self::HSL_SPACE as $key => $val) {
                $temp = [self::DARK => 0, self::LIGHT => 0];
                foreach ($colors as $color) {
                    $h = $color['hue'] * 360;
                    if ($h > 345) {
                        $h = $h - 360;
                    }
                    $s = $color['saturation'];
                    $l = $color['luminosity'];
                    $c = $color[self::C];
                    if ($c == 0) {
                        continue;
                    }
                    if (abs($h - $val) < $delta) {
                        if ($l < 0.125) {
                            $black[self::DARK] += $c;
                        } else if ($l > 0.875) {
                            $white[self::LIGHT] += $c;
                        } else {
                            if ($s < 0.25) {
                                if ($l < 0.5) {
                                    $greyDark[self::DARK] += $c;
                                } else {
                                    $greyLight[self::LIGHT] += $c;
                                }
                            } else {
                                if ($l < 0.5) {
                                    $temp[self::DARK] += $c;
                                } else {
                                    $temp[self::LIGHT] += $c;
                                }
                            }
                        }
                        $color[self::C] = 0;
                    }
                }
                $colorStatistics[$key][self::DARK] += $temp[self::DARK];
                $colorStatistics[$key][self::LIGHT] += $temp[self::LIGHT];
            }
            $colorStatistics[self::BLACK] = $black;
            $colorStatistics[self::GRAY_DARK] = $greyDark;
            $colorStatistics[self::WHITE] = $white;
            $colorStatistics[self::GRAY_LIGHT] = $greyLight;

            foreach ($colorStatistics as &$statistics) {
                $statistics[self::PERCENT] = ($statistics[self::DARK] + $statistics[self::LIGHT]) / $totalPixels;
            }
            uasort($colorStatistics, function ($a, $b) {
                if ($a[self::PERCENT] < $b[self::PERCENT]) {
                    return true;
                }
                return false;
            });
        }
        return $colorStatistics;
    }
    public function getColors() {
        $colors = [];
        if(!is_null($this->imagick)) {
            $histogramElements = $this->imagick->getImageHistogram();
            foreach ($histogramElements as $p) {
                $color = $p->getHSL();
                $cnt = $p->getColorCount();
                $color['c'] = $cnt;
                array_push($colors, $color);
            }
        }
        return $colors;
    }
    public function hslToRgb($h, $s, $l) {
        if($h > 360) {
            $h = 0;
        }
        $pixel = new \ImagickPixel();
        $tempH = ($h > 1) ? $h/360 : $h;
        $pixel->setHSL($tempH, $s, $l);
        $rgb = $pixel->getColor();
        $pixel->destroy();
        return $rgb;
    }

    public function testOutput() {
        if(!is_null($this->imagick)) {
            $this->outputImage($this->imagick);
            $this->outputHsl($this->getTones());
        } else {
            echo 'null imagick';
        }

    }
    function outputImage(\Imagick $imagick) {
        echo '<img src="data:image/jpeg;base64,'.base64_encode($imagick->getImageBlob()).'">';
        $tempImg = clone $imagick;
        $tempImg->transformImageColorspace(\Imagick::COLORSPACE_HSL);
        $tempImg->separateImageChannel(\Imagick::CHANNEL_RED);
        echo '<img src="data:image/jpeg;base64,'.base64_encode($tempImg->getImageBlob()).'">';
        $tempImg = clone $imagick;
        $tempImg->transformImageColorspace(\Imagick::COLORSPACE_HSL);
        $tempImg->separateImageChannel(\Imagick::CHANNEL_GREEN);
        echo '<img src="data:image/jpeg;base64,'.base64_encode($tempImg->getImageBlob()).'">';
        $tempImg = clone $imagick;
        $tempImg->transformImageColorspace(\Imagick::COLORSPACE_HSL);
        $tempImg->separateImageChannel(\Imagick::CHANNEL_BLUE);
        echo '<img src="data:image/jpeg;base64,'.base64_encode($tempImg->getImageBlob()).'">';
        $tempImg = clone $imagick;
        $tempImg->transformImageColorspace(\Imagick::COLORSPACE_HSL);
        $tempImg->separateImageChannel(\Imagick::CHANNEL_CYAN);
        echo '<img src="data:image/jpeg;base64,'.base64_encode($tempImg->getImageBlob()).'">';
        $tempImg = clone $imagick;
        $tempImg->transformImageColorspace(\Imagick::COLORSPACE_HSL);
        $tempImg->separateImageChannel(\Imagick::CHANNEL_YELLOW);
        echo '<img src="data:image/jpeg;base64,'.base64_encode($tempImg->getImageBlob()).'">';
        $tempImg = clone $imagick;
        $tempImg->transformImageColorspace(\Imagick::COLORSPACE_HSL);
        $tempImg->separateImageChannel(\Imagick::CHANNEL_MAGENTA);
        echo '<img src="data:image/jpeg;base64,'.base64_encode($tempImg->getImageBlob()).'">';
    }
    function outputHsl(array $result) {
        echo "<br/>";
        foreach($result as $key=>$item) {
            $hexColor = sprintf("#%02X%02X%02X",$item['r'], $item['g'], $item['b']);
            echo '<div style="width:60px;height:20px;background:'.$hexColor.'"/>';
            $hexStr = sprintf("%0.4f%%", $item['p'] * 100);
            echo '<div style="margin-left:80px;">'.$hexColor.'='.$hexStr.'</div>';
       }
    }

}