<?php
function location_label(string $slug): string {
    if(strtolower($slug)==='abuja')return 'Federal Capital Territory (Abuja)';
    $label=ucwords(str_replace(['-','_'],' ',$slug));
    return str_ireplace(['Fct Abuja','Fct'],['Federal Capital Territory','Federal Capital Territory'],$label);
}
function nigeria_locations(): array {
    static $locations=null;if($locations!==null)return $locations;
    $raw=@file_get_contents(dirname(__DIR__,2).'/locations.json');if($raw===false)return $locations=[];
    $start=null;if(preg_match('/\[\s*\{\s*"state"\s*:/s',$raw,$match,PREG_OFFSET_CAPTURE))$start=$match[0][1];
    if($start===null)return $locations=[];
    $decoded=json_decode(substr($raw,$start),true);if(!is_array($decoded))return $locations=[];
    $locations=[];foreach($decoded as $state){if(empty($state['state'])||!is_array($state['lgas']??null))continue;$stateName=location_label((string)$state['state']);$lgas=[];foreach($state['lgas'] as $lga){if(!empty($lga['lga']))$lgas[]=location_label((string)$lga['lga']);}$locations[$stateName]=array_values(array_unique($lgas));}
    ksort($locations);return $locations;
}
function nigeria_states(): array { return array_keys(nigeria_locations()); }
function acquisition_sources(): array { return ['Radio','Instagram','Facebook','Flyers','School Fair','Agent']; }
function profile_select_options(array $options,string $selected=''): string {$html='';foreach($options as $option){$html.='<option value="'.esc($option).'"'.($selected===$option?' selected':'').'>'.esc($option).'</option>';}return $html;}
