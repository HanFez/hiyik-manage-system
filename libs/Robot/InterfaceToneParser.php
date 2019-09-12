<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 16-7-24
 * Time: 下午6:50
 */

namespace Libs\Robot;


/** Interface define to parse tone of a image.
 * Interface InterfaceParser
 * @package libs\Robot
 */
interface InterfaceToneParser {
    const COLOR_MODE = ['hsl', 'rgb'];
    const MODE_NAME = 'mode';
    const VALUES = 'values';
    /** To specify image file to parse.
     * @param $filePath
     * @return boolean
     */
    function setImageFile($filePath);

    /** To specify image blob to parse.
     * @param $blob
     * @return mixed
     */
    function setImageBlob($blob);
    /** To obtain tone statistics of a image.
     * @param mixed $delta The color range to merge.
     * @return array [['r' => x, 'g' => x, 'b' => x, 'c' => , 'p' => x.x]...]
     * A RGB color list or empty array.
     * r:RED, g:GREEN, b:BLUE, c:pixel count, p:percent in total
     */
    function getTones($delta=null);

    /** To get main tones of image.
     * @return array ['mode' => xxx, 'values' => [$colorName => ['r' =>, 'g'=>, 'b'=>, 'c'=>, 'p'=>]...]]
     */
    function getMainTones();
    /** Obtain the image path parsed.
     * @return string | null
     */
    function getImageFile();

    /** Obtain the total pixels of image.
     * @return Integer
     */
    function getTotalPixels();

    /** Obtain the name of parser.
     * @return mixed
     */
    function getParserMode();
}