<?php
/**
 * TUFF BEATZ Site Editor — Phase 1.6 Module Registry
 * A presentation-only registry so future editable modules can declare their editor capabilities.
 */
if (!defined('ABSPATH')) exit;

function tuff_beatz_editor_registry_defaults(){
    return array(
        'global_branding'=>array('label'=>'Global Branding','phase'=>'1.0','scope'=>'public','controls'=>array('brand','footer')),
        'homepage_hero'=>array('label'=>'Homepage Hero','phase'=>'1.3','scope'=>'public','controls'=>array('content','media','layout','responsive-preview')),
        'homepage_sections'=>array('label'=>'Homepage Sections','phase'=>'1.4','scope'=>'public','controls'=>array('visibility','order','headlines')),
        'header_footer'=>array('label'=>'Header & Footer','phase'=>'1.5','scope'=>'public','controls'=>array('logo','subtitle','cta','footer-copy')),
    );
}
function tuff_beatz_editor_registry(){
    $registry=tuff_beatz_editor_registry_defaults();
    /** Future modules register here without modifying the Site Editor core. */
    return apply_filters('tuff_beatz_editor_registry',$registry);
}
function tuff_beatz_editor_register_module($key,$config){
    $key=sanitize_key($key);
    if(!$key || !is_array($config)) return;
    add_filter('tuff_beatz_editor_registry',function($registry) use($key,$config){
        $registry[$key]=wp_parse_args($config,array('label'=>$key,'phase'=>'future','scope'=>'public','controls'=>array()));
        return $registry;
    });
}
function tuff_beatz_editor_registry_panel(){
    if(empty($_GET['page']) || $_GET['page']!=='tuff-beatz-site-editor' || !current_user_can('manage_options')) return;
    $registry=tuff_beatz_editor_registry();
    ?>
    <style>
      .tbse-registry{background:#111;color:#f5f0e6;border:1px solid #332b1e;border-radius:14px;padding:22px;grid-column:1/-1}.tbse-registry-head{display:flex;align-items:flex-start;justify-content:space-between;gap:18px}.tbse-registry h2{color:#fff;margin:4px 0 7px}.tbse-registry p{color:#aaa;margin:0}.tbse-registry-badge{border:1px solid #8a7040;color:#d8b66c;border-radius:999px;padding:6px 10px;font-size:11px;font-weight:800;white-space:nowrap}.tbse-registry-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:10px;margin-top:17px}.tbse-registry-item{border:1px solid #292929;border-radius:11px;background:#171717;padding:14px}.tbse-registry-item strong{display:flex;justify-content:space-between;gap:10px;color:#fff}.tbse-registry-item em{font-style:normal;color:#d8b66c;font-size:10px;letter-spacing:.08em}.tbse-registry-controls{display:flex;flex-wrap:wrap;gap:5px;margin-top:9px}.tbse-registry-controls span{font-size:10px;border:1px solid #383838;border-radius:999px;padding:4px 7px;color:#bbb}@media(max-width:900px){.tbse-registry-grid{grid-template-columns:1fr}}
    </style>
    <script>
    document.addEventListener('DOMContentLoaded',function(){
      var grid=document.querySelector('.tbse-grid');if(!grid)return;
      var registry=<?php echo wp_json_encode($registry);?>;
      var card=document.createElement('section');card.className='tbse-registry';
      var html='<div class="tbse-registry-head"><div><p class="tbse-kicker">PHASE 1.6 · EDITOR ARCHITECTURE</p><h2>Module Registry</h2><p>Every future presentation module can register its editable controls here while protected business and security logic stays outside the editor.</p></div><span class="tbse-registry-badge">'+Object.keys(registry).length+' MODULES REGISTERED</span></div><div class="tbse-registry-grid">';
      Object.keys(registry).forEach(function(key){var m=registry[key],controls=Array.isArray(m.controls)?m.controls:[];html+='<div class="tbse-registry-item"><strong>'+m.label+' <em>PHASE '+m.phase+'</em></strong><div class="tbse-registry-controls">'+controls.map(function(c){return '<span>'+String(c).replace(/-/g,' ')+'</span>'}).join('')+'</div></div>'});
      html+='</div>';card.innerHTML=html;grid.appendChild(card);
    });
    </script>
    <?php
}
add_action('admin_footer','tuff_beatz_editor_registry_panel',90);
