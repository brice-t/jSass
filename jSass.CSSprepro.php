<?php
/**
* @package     
* @subpackage  
* @author      Brice Tencé
* @copyright   2012 Brice Tencé
* @link        
* @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

/**
* plugin for jResponseHTML, which processes Sass files
*/

define( 'COMPILE_TYPE_SASS', 'sass');
define( 'COMPILE_TYPE_SCSS', 'scss');

class jSassCSSpreproPlugin implements ICSSpreproPlugin {

    private $sassExtensions = array('sass', 'scss');
    private $sassPath = '/usr/bin/sass';
    private $compileType = null;
    private $loadPaths = array();
    private $sassStyle = 'nested'; // nested (default), compact, compressed, or expanded

    public function __construct( CSSpreproHTMLResponsePlugin $CSSpreproInstance ) {

        $gJConfig = jApp::config();

        if( isset($gJConfig->jResponseHtml['CSSprepro_jSass_extensions']) ) {
            $extString = $gJConfig->jResponseHtml['CSSprepro_jSass_extensions'];
            $this->sassExtensions = preg_split( '/ *, */', trim($extString) );
        }

        if( isset($gJConfig->jResponseHtml['CSSprepro_jSass_sasspath']) ) {
            $this->sassPath = $gJConfig->jResponseHtml['CSSprepro_jSass_sasspath'];
        }

        if( isset($gJConfig->jResponseHtml['CSSprepro_jSass_style']) ) {
            $this->sassStyle = $gJConfig->jResponseHtml['CSSprepro_jSass_style'];
        }

        if( isset($gJConfig->jResponseHtml['CSSprepro_jSass_loadpaths']) ) {
            $this->loadPaths = $gJConfig->jResponseHtml['CSSprepro_jSass_loadpaths'];
        }
    }


    public function handles( $inputCSSLinkUrl, $CSSLinkParams ) {
        $this->compileType = ( isset($CSSLinkParams['sass']) && $CSSLinkParams['sass'] ?
            COMPILE_TYPE_SASS :
                isset($CSSLinkParams['scss']) && $CSSLinkParams['scss'] ?
                COMPILE_TYPE_SCSS : null );

        if( in_array( pathinfo($inputCSSLinkUrl, PATHINFO_EXTENSION), $this->sassExtensions ) ||
            (isset($CSSLinkParams['sass']) && $CSSLinkParams['sass']) ) {
                return true;
            }
    }

    public function compile( $filePath, $outputPath ) {

        $sassProcessArgs = array();

        $sassProcessArgs[] = '--load-path';
        $sassProcessArgs[] = dirname( $filePath );

        if( $this->compileType == COMPILE_TYPE_SCSS ||
             ($this->compileType === null && 'scss' == pathinfo($filePath, PATHINFO_EXTENSION)) ) {
            $sassProcessArgs[] = '--scss';
        }

        $sassProcessArgs[] = '--style';
        $sassProcessArgs[] = $this->sassStyle;

        foreach ($this->loadPaths as $loadPath) {
            $sassProcessArgs[] = '--load-path';
            $sassProcessArgs[] = $loadPath;
        }

        // input
        $sassProcessArgs[] = $filePath;


        $sassProcessArgs = array_map( 'escapeshellarg', $sassProcessArgs );
        $sassProcessCmd = $this->sassPath . ' ' . implode(' ', $sassProcessArgs) . ' 2>&1';

        exec( $sassProcessCmd, $sassProcessOutput, $code );

        if( $code !== 0 ) {
            trigger_error( "Sass error (returned $code) for '$filePath' : " . implode("\n", $sassProcessOutput), E_USER_ERROR );
        }

        file_put_contents( $outputPath, implode("\n", $sassProcessOutput) );
    }


    public function cleanCSSLinkParams( & $CSSLinkParams ) {
        unset($CSSLinkParams['sass']);
    }

}


