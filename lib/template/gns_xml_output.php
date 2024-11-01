<?php
header("HTTP/1.1 200 OK");
header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
<?php
$output = '';
$settings = $this->getSettings();
if(!empty($settings['gns_xml_cats'])){
	$cats = $settings['gns_xml_cats'];
	$args = array(
		'post_type' => $settings['gns_xml_custom_post_types'],
		'posts_per_page' => 1000,
		'order' => 'ASC',
		'date_query' => array(
			'after' => date('Y-m-d', strtotime('-2 days'))
		),
		'category__in' => $cats
	);
} else {
	$args = array(
		'post_type' => $settings['gns_xml_custom_post_types'],
		'posts_per_page' => 1000,
		'order' => 'ASC',
		'date_query' => array(
			'after' => date('Y-m-d', strtotime('-2 days'))
		),
		'meta_key'  => 'gns_xml_include',
		'meta_value' => 'include'
	);
}

$query = new WP_Query( $args );

function convert_smart_quotes($string) {

	$quotes = array(
		"\xC2\xAB"     => '"', // « (U+00AB) in UTF-8
		"\xC2\xBB"     => '"', // » (U+00BB) in UTF-8
		"\xE2\x80\x98" => "'", // ‘ (U+2018) in UTF-8
		"\xE2\x80\x99" => "'", // ’ (U+2019) in UTF-8
		"\xE2\x80\x9A" => "'", // ‚ (U+201A) in UTF-8
		"\xE2\x80\x9B" => "'", // ‛ (U+201B) in UTF-8
		"\xE2\x80\x9C" => '"', // “ (U+201C) in UTF-8
		"\xE2\x80\x9D" => '"', // ” (U+201D) in UTF-8
		"\xE2\x80\x9E" => '"', // „ (U+201E) in UTF-8
		"\xE2\x80\x9F" => '"', // ‟ (U+201F) in UTF-8
		"\xE2\x80\xB9" => "'", // ‹ (U+2039) in UTF-8
		"\xE2\x80\xBA" => "'", // › (U+203A) in UTF-8
	);
	$str = strtr($string, $quotes);
	return $string;
}

if ( $query->have_posts() ) {

	while ( $query->have_posts() ) {
		$query->the_post();
		$post_id = get_the_ID();
		$gns_xml_excluded = get_post_meta( $post_id, 'gns_xml_include', true);
		if($gns_xml_excluded == 'exclude'){
			continue;
		}

		$tags = get_the_tags();
		$keywords = array();
		$title = get_the_title();
		//$title = iconv('UTF-8', 'ASCII//TRANSLIT', $title);

		if($tags){
			foreach ($tags as $tag) {
				$keywords[] = $tag->name;
			}
		}

		$gns_xml_include = get_post_meta( $post_id, 'gns_xml_include', true);
		if($gns_xml_include != "" && $gns_xml_include == "exclude"){
			continue;
		}

		$gns_xml_pubaccess = get_post_meta( $post_id, 'gns_xml_pubaccess', true);
		if($gns_xml_pubaccess == ""){
			$gns_xml_pubaccess = $settings['gns_xml_pubaccess'];
		}

		$gns_xml_genres = unserialize(get_post_meta( $post_id, 'gns_xml_genres', true));
		if(empty($gns_xml_genres)){
			$gns_xml_genres = unserialize($settings['gns_xml_genres']);
		}

		$gns_xml_publanguage = get_post_meta( $post_id, 'gns_xml_publanguage', true);
		if($gns_xml_publanguage == ""){
			$gns_xml_publanguage = $settings['gns_xml_publanguage'];
		}

		$output .= '<url>';
		$output .= '<loc>'.get_permalink().'</loc>';
		$output .= '<news:news><news:publication><news:name>'.ent2ncr($settings['gns_xml_pubname']).'</news:name>';
		$output .= '<news:language>'.ent2ncr($gns_xml_publanguage).'</news:language></news:publication>';
		if($gns_xml_pubaccess != 'na'){
			$output .= '<news:access>'.ent2ncr($gns_xml_pubaccess).'</news:access>';
		}
		if(!empty($gns_xml_genres)){
			$output .= '<news:genres>'.ent2ncr($gns_xml_genres).'</news:genres>';
		}
		$output .= '<news:publication_date>'.get_the_date('Y-m-d\TH:i:s+00:00').'</news:publication_date>';
		$output .= '<news:title>'.ent2ncr($title).'</news:title>';
		$output .= '<news:keywords>'.ent2ncr(implode(",", $keywords)).'</news:keywords>';
		$output .= '</news:news>';
		$output .= '</url>';
	} // end while
} // end if

if (isset($output)) {
	echo $output;
}
?>
</urlset>