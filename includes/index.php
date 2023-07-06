<?php 
    $config = require($_SERVER["DOCUMENT_ROOT"] . "/../.config/config.php");
    //scheduler project directory
    $schRoot = $_SERVER['DOCUMENT_ROOT'] . '/scheduler';

    $baseUrl = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
    $baseUrl .= $_SERVER['HTTP_HOST'] . '/scheduler';

    #region paths get and print functions
    function get_templates_directory(){
        global $schRoot;

        if(file_exists($schRoot . '/templates')){
            return $schRoot . '/templates';
        }
        else{
            throw_template_dir_error();
        }
    }
    function get_templates_directory_url(){
        global $schRoot;
        global $baseUrl;

        if(file_exists($schRoot . '/templates')){
            return $baseUrl . '/templates';
        }
        else{
            throw_template_dir_error();
        }
    }
    function get_assets_directory(){
        global $schRoot;

        if(file_exists($schRoot . '/assets')){
            return $schRoot . '/assets';
        }
        else{
            throw_template_dir_error();
        }
    }
    function get_assets_directory_url(){
        global $schRoot;
        global $baseUrl;

        if(file_exists($schRoot . '/assets')){
            return $baseUrl . '/assets';
        }
        else{
            throw_template_dir_error();
        }
    }
    function get_css_direcrory(){
        if(file_exists(get_assets_directory() . '/css')){
            return get_assets_directory() . '/css';
        }
        else{
            throw_css_dir_error();
        }
    }
    function get_css_direcrory_url(){
        if(file_exists(get_css_direcrory())){
            return get_assets_directory_url() . '/css';
        }
        else{
            throw_css_dir_error();
        }
    }
    function get_js_directory(){
        if(file_exists(get_assets_directory() . '/js')){
            return get_assets_directory() . '/js';
        }
        else {
            throw_js_dir_error();
        }
    }
    function get_js_directory_url(){
        if(file_exists(get_js_directory())){
            return get_assets_directory_url() . '/js';
        }
        else{
            throw_js_dir_error();
        }
    }
    function get_images_directory(){
        if(file_exists(get_assets_directory() . '/images')){
            return get_assets_directory() . '/images';
        }
        else{
            throw_images_dir_error();
        }
    }
    function get_images_directory_url(){
        if(file_exists(get_images_directory())){
            return get_assets_directory_url() . '/images';
        }
        else{
            throw_images_dir_error();
        }
    }
    
    function get_header_path(){
        if(file_exists(get_templates_directory() . '/header.php')){
            return get_templates_directory() . '/header.php';
        }
        else{
            throw_header_file_error();
        }
    }
    function get_header_path_url(){
        if(file_exists(get_header_path())){
            return get_templates_directory_url() . '/header.php';
        }
        else{
            throw_header_file_error();
        }
    }
    $head_html_item;
    function get_header($headHtmlItem = ''){
        global $head_html_item;
        $head_html_item = $headHtmlItem;
        if(file_exists(get_header_path())){
            include get_header_path();
            return get_header_path();
        }
        else{
            throw_header_file_error();
        }
    }
    function get_footer_path(){
        if(file_exists(get_templates_directory() . '/footer.php')){
            return get_templates_directory() . '/footer.php';
        }  
        else{
            throw_footer_file_error();
        }
    }
    function get_footer_path_url(){
        if(file_exists(get_footer_path())){
            return get_templates_directory_url() . '/footer.php';
        }
        else{
            throw_footer_file_error();
        }
    }
    function get_footer(){
        if(file_exists(get_footer_path())){
            include get_footer_path();
            return get_footer_path();
        }
        else{
            throw_footer_file_error();
        }
    }

    function get_current_url(){
        $currentPagURL = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
        $currentPagURL .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        return $currentPagURL;
    }

    #endregion

    #region custom exceptions
    
    function throw_template_dir_error(){
        throw new Exception('template directory does not exist:');
    }
    function throw_header_file_error(){
        throw new Exception('header.php does not exist in the template directory or the template directory does not exist:');
    }
    function throw_footer_file_error(){
        throw new Exception('footer.php does not exist in the template directory or the template directory does not exist:');
    }
    function throw_assets_dir_error(){
        throw new Exception('assets directory does not exist:');
    }
    function throw_css_dir_error(){
        throw new Exception('css directory does not exist:');
    }
    function throw_js_dir_error(){
        throw new Exception('js directory does not exist:');   
    }
    function throw_images_dir_error(){
        throw new Exception('js directory does not exist:');  
    }
    #endregion

    function get_default_head_items(){
        $styleCss = get_css_direcrory_url() . '/style.css';
        $jqueryJs = get_js_directory_url() . '/jquery.min.js';
        $mainJs = get_js_directory_url() . '/main.js';

        $html = <<<EOD
            <link rel="stylesheet" href="$styleCss">
            <link rel="stylesheet" href="/scheduler/includes/packages/node_modules/@flaticon/flaticon-uicons/css/all/all.css">
    
            <script src="$jqueryJs"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/showdown/1.4.0/showdown.min.js"></script>
            <script src="$mainJs"></script>
        EOD;

        return $html;
    }
    function get_head_html_item(){
        global $head_html_item;
        return $head_html_item;
    }
?>