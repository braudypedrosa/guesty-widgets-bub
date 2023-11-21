<?php 
add_action('init', 'guesty_add_rewrite_rule');
function guesty_add_rewrite_rule(){
    add_rewrite_rule('^properties?','index.php?guesty-listings=1&post_type=guesty_listings','top');
}

add_action('query_vars','guesty_set_query_var');
function guesty_set_query_var($vars) {
    array_push($vars, 'guesty-listings');
    return $vars;
}

add_filter('template_include', 'guesty_load_templates', 1000, 1);
function guesty_load_templates($template){
    if(get_query_var('guesty-listings')){
        $new_template = GUESTY_DIR.'/views/archive-property.php';
        if(file_exists($new_template))
            $template = $new_template;
    }
    return $template;
}