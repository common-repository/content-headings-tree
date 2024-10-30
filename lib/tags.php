<?php

function extract_tags($html, $tag, $selfclosing = null, $return_the_entire_tag = false, $charset = 'UTF-8') {

	if (is_array($tag)) {
		$tag = implode('|', $tag);
	}

	$selfclosing_tags = array('area', 'base', 'basefont', 'br', 'hr', 'input', 'img', 'link', 'meta', 'col', 'param');
	if (is_null($selfclosing)) {
		$selfclosing = in_array($tag, $selfclosing_tags);
	}

	if ($selfclosing) {
		$tag_pattern =
			'@<(?P<tag>' . $tag . ')           # <tag
            (?P<attributes>\s[^>]+)?       # attributes, if any
            \s*/?>                   # /> or just >, being lenient here 
            @xsi';
	} else {
		$tag_pattern =
			'@<(?P<tag>' . $tag . ')           # <tag
            (?P<attributes>\s[^>]+)?       # attributes, if any
            \s*>                 # >
            (?P<contents>.*?)         # tag contents
            </(?P=tag)>               # the closing </tag>
            @xsi';
	}

	$attribute_pattern =
		'@
        (?P<name>\w+)                         # attribute name
        \s*=\s*
        (
            (?P<quote>[\"\'])(?P<value_quoted>.*?)(?P=quote)    # a quoted value
            |                           # or
            (?P<value_unquoted>[^\s"\']+?)(?:\s+|$)           # an unquoted value (terminated by whitespace or EOF) 
        )
        @xsi';

	if (!preg_match_all($tag_pattern, $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE)) {
		return array();
	}

	$tags = array();
	foreach ($matches as $match) {

		$attributes = array();
		if (!empty($match['attributes'][0])) {

			if (preg_match_all($attribute_pattern, $match['attributes'][0], $attribute_data, PREG_SET_ORDER)) {
				//Turn the attribute data into a name->value array
				foreach ($attribute_data as $attr) {
					if (!empty($attr['value_quoted'])) {
						$value = $attr['value_quoted'];
					} else if (!empty($attr['value_unquoted'])) {
						$value = $attr['value_unquoted'];
					} else {
						$value = '';
					}

					$value = html_entity_decode($value, ENT_QUOTES, $charset);

					$attributes[$attr['name']] = $value;
				}
			}
		}

		$tag = array(
		    'tag_name' => $match['tag'][0],
		    'offset' => $match[0][1],
		    'contents' => !empty($match['contents']) ? $match['contents'][0] : '', //empty for self-closing tags
		    'attributes' => $attributes,
		);
		if ($return_the_entire_tag) {
			$tag['full_tag'] = $match[0][0];
		}

		$tags[] = $tag;
	}

	return $tags;
}
?>