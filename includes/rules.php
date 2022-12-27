<?php

	/*  
		In this moment is possible to inject CTA only after the nth element,
		but maybe in the future we will add more rules, just like:
		- before the nth element
		- between the nth and the mth element
		...

		Each of this functions will have the same structure:
		name_of_the_callback($content, $cta) {
			...
			return $content;
		}

		The name_of_the_callback will be saved in the $cta['rule']['callback'] variable,
		so remember that can this must be inserted in the select in the <!-- Callback selector -->

	*/
	function qzr_rule_after_nth_element($content, $cta) {

		// Just for readability...
		$options = $cta['rule']['options'];
		
		// Generating the new HTML
		$doc = new DOMDocument();
		@$doc->loadHTML('<?xml encoding="UTF-8">'.$content); // This prevent the error "DOMDocument::loadHTML(): Empty string supplied as input"

		// If the element is a class, we need to use a different method
		if (strpos($options['element'], '.') !== false) {
			$classname = $options['element'];
			$classname = str_replace('.', '', $classname);
			$finder = new DomXPath($doc);
			$elements = $finder->query("//*[contains(concat(' ', @class, ' '), '$classname')]");
		}
		else {
			// Otherwise we can use the getElementsByTagName method
			$elements = $doc->getElementsByTagName($options['element']);
		}
		
		// If the element isn't found, return the original content
		// but it could be better to return an alert message
		// or insert the CTA at the end of the content (if requested), but keep it simple for now		
		if ($elements->length > 0) {

			$options['nth'] = $options['nth'] - 1;
			$newHtml = $doc->createElement('div', '');
			$newHtml->setAttribute('class', 'qzr-cta');

			// Need to convert the HTML to a DOMNode and then append it to the $newHtml
			qzr_append_HTML($newHtml, $cta['html']);

			// Now add the $newHtml after the $option['nth'] element
			$elements->item($options['nth'])->parentNode->insertBefore($newHtml, $elements->item($options['nth'])->nextSibling);
		}

		$new_content = $doc->saveXML($doc->documentElement, LIBXML_NOEMPTYTAG);

		return $new_content;
	}

	function qzr_append_HTML(DOMNode $parent, $source) {

		$tmpDoc = new DOMDocument();
		@$tmpDoc->loadHTML('<?xml encoding="UTF-8">' . stripslashes($source));

		foreach ($tmpDoc->getElementsByTagName('body')->item(0)->childNodes as $node) {
			$node = $parent->ownerDocument->importNode($node, true);
			$parent->appendChild($node);
		}
	}

	// Other rules functions