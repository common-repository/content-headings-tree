<?php

$content = get_the_content();

function replace_headings($content) {
	$nodes = extract_tags($content, 'h\d+', false);
	foreach ($nodes as $node) {
		$content = str_replace('<' . $node['tag_name'] . '>' . strip_tags($node['contents']) . '</' . $node['tag_name'] . '>', '<a name="' . str_replace(" ", "_", strip_tags($node['contents'])) . '"></a><' . $node['tag_name'] . '>' . strip_tags($node['contents']) . '</' . $node['tag_name'] . '>', $content);
	}
	return $content;
}

add_filter('the_content', 'replace_headings');
?>
