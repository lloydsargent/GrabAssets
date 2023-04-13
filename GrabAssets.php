<?php
/**
 * List of assets and selected assets for PicoCMS.
 * 
 * Marginally based off the code by Nicolas Liautaud 
 * (https://github.com/nliautaud/pico-pages-list)
 *
 * - Adds twig global `{{ array_of_assets }}` and `{{ selected_assets }}`
 *
 * Selected assets are only set if file.md contains the following YAML:
 * 
 *  selected_assets: assets/path_to_assets
 * 
 * So what is it good for? Directories of images. Using this one can get
 * image paths and create, using TWIG, a page full of images.
 *
 * @author  Lloyd Sargent
 * @link    
 * @link    http://picocms.org
 * @license http://opensource.org/licenses/MIT The MIT License
 * 
 * Hours having written php code, maybe 4 hours. Heh.
 */
class GrabAssets extends AbstractPicoPlugin
{
    const API_VERSION = 2;
    private $currentPagePath;

    /**
     * Triggered when Pico discovered the current, previous and next pages
     *
     * This function is designed on save the base_url.
     *
     * @see Pico::getCurrentPage()
     * @see Pico::getPreviousPage()
     * @see Pico::getNextPage()
     *
     * @param array|null &$currentPage  data of the page being served
     * @param array|null &$previousPage data of the previous page
     * @param array|null &$nextPage     data of the next page
     *
     * @return void
     */
    protected function onCurrentPageDiscovered(array &$currentPage = null, array &$previousPage = null, array &$nextPage = null) {
        if ($currentPage == null) {
            return;
        }
        $base_url = $this->getConfig('base_url');
        $this->currentPagePath = str_replace(array('?', $base_url), '', urldecode($currentPage['url']));
    }


    /**
     * Triggered before Pico renders the page
     * 
     * This depends on two settings that exist in your config.yml file.
     *     assets_dir: assets       # path to assets
     *     supported_assets: [gif, jpg, jpeg, png, svg]
     * 
     * assets_dir is for sites with very complex paths. This must exist for the
     * plugin to work. Otherwise the twig variables are empty.
     * 
     * supported_assets defines what type of assets you want in your list.
     * 
     * Note that this code is VERY wordy and could be shortened. As they say,
     * "Next version."
     *
     * Register twig variable 'array_of_assets'
     * Register twig variable 'selected_assets'
     * 
     * array_of_assets will contain all assets
     * selected_assets will contain files in the diretory in
     * the meta.selected_assets (in the header of the .md file)
     *
     * @see DummyPlugin::onPageRendered()
     *
     * @param string &$templateName  file name of the template
     * @param array  &$twigVariables template variables
     *
     * @return void
     */

    public function onPageRendering(string &$templateName, array &$twigVariables)
    {
        $twig = $this->getPico()->getTwig();
        $twigConfig = $twigVariables['config'];
        $twigMeta = $twigVariables['meta'];

        //----- see if our assets configuration variable exists
        //----- if it doesn't exist, return early
        if (!array_key_exists('assets_dir', $twigConfig)) {
            $twigVariables['nested_assets'] = [];
            $twigVariables['selected_assets'] = [];
            return;    
        }

        //----- assume a bogus asset directory. Very unlikely.
        $selectedAssetDirectory = '/deadbeef/beefdead/deadbeef';        
        if (array_key_exists('selected_assets', $twigMeta)) {
            $selectedAssetDirectory = $twigMeta['selected_assets'];
        } else {
            return;
        }

        //----- create our assets directory
        $base_dir = $twigVariables['base_dir'];
        $base_dir .= '/';

        $assets_dir = $twigVariables['base_dir'];
        $assets_dir .= '/';
        $assets_dir .= $selectedAssetDirectory;

        //----- get our supported images
        $supported_assets = $twigConfig['supported_assets'];

        //----- create our iterator
        $Directory = new RecursiveDirectoryIterator($assets_dir);
        $Iterator = new RecursiveIteratorIterator($Directory);

        //----- iterate through each directory
        $selected_files = array();
        $all_assets = array();
        foreach ($Iterator as $info) {
            $src_file_name = $info->getPathName();
            $src_file_name = str_replace($base_dir, '', $src_file_name);
            $ext = strtolower(pathinfo($src_file_name, PATHINFO_EXTENSION));
            
            //----- only keep a list of supported file types
            if (in_array($ext, $supported_assets)) {
                $all_assets[] = $src_file_name;

                //----- chop off the leading part so we are left with `assets/blah/blah.png`
                if (substr($src_file_name, 0, strlen($selectedAssetDirectory)) === $selectedAssetDirectory) {
                    $selected_files[] = $src_file_name;
                }
            }
        }

        //----- set twig variables that we just created
        sort($all_assets);
        $twigVariables['array_of_assets'] = $all_assets;
        sort($selected_files);
        $twigVariables['selected_assets'] = $selected_files;
    }
}