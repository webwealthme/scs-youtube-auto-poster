<?php
/**
 * Some helper functions to fetch "Closed Captions" for a YouTube video
 * 
 * Normally these are only available to the video creator... 
 * But there's always a way! 
 * 
 * @author Jordan Skoblenick <parkinglotlust@gmail.com> 2014-08-16
 */
	
/**
 * "Main function" - simply call this with a YouTube video ID 
 * and you will get back the text of the first Closed Captions we could load
 * 
 * @param string $videoId
 * @return mixed Closed Caption text or NULL if no tracks or text are found
 */
function getClosedCaptionsForVideo($videoId) {
	$baseUrl = getBaseClosedCaptionsUrl($videoId);
	
	// find available languages. options vary widely from
	// video to video, and sometimes garbage is returned.
	// tracks are returned in order we think is best - 
	// try first, if its garbage, try 2nd, etc.
	$availableTracks = getAvailableTracks($baseUrl);
	$text = null;
	foreach ($availableTracks as $track) {
		$text = getClosedCaptionText($baseUrl, $track);
		
		// check for garbage
		if (stripos($text, '[Content_Types]') !== false) {
			// garbage found - some legible text and a lot
			// that is not legible. dunno what this is, but 
			// it actually appears on the YT video if you view 
			// the page... skip 
			continue;
		}
		
		if ($text) {
			// didnt skip, and we have text.. win!
			break;
		}
	}
	
	if ($text) {
		// maybe process found text here?
		// for now just wrap in paragraph tags
		$text = "<p>{$text}</p>";
	}
	
	return $text;
}

/**
 * Returns base URL for TimedText. 
 * "List languages"/"retrive text" commands will be appended to this URL
 * 
 * Fetching this from the page saves us having to calculate a signature :)
 * 
 * @param string $videoId
 * @return string The base URL for TimedText requests
 */
function getBaseClosedCaptionsUrl($videoId) {
	$youtubeUrl = 'http://www.youtube.com/watch?v=';
	$pageUrl = $youtubeUrl.$videoId;
	if (!$responseText = file_get_contents($pageUrl)) {
		die('Failed to load youtube url '.$pageUrl);
	}
	
	$matches = [];
	if (!preg_match('/TTS_URL\': "(.+?)"/is', $responseText, $matches)) {
		die('Failed to find TTS_URL in page source for '.$pageUrl);
	}
	
	return str_replace(['\\u0026', '\\/'], ['&', '/'], $matches[1]);
}
/**
 * Given a base URL, queries for available tracks and 
 * returns them in a sorted array ("scored" from highest
 * to lowest based on things like `default_language` etc)
 * 
 * @param string $baseUrl Base URL found by calling getBaseClosedCaptionsUrl()
 * @return array An array of Closed Captions tracks available for this video
 */
function getAvailableTracks($baseUrl) {
	$tracks = [];
	
	// "request list" command
	$listUrl = $baseUrl.'&type=list&tlangs=1&fmts=1&vssids=1&asrs=1';
	if (!$responseText = file_get_contents($listUrl)) {
		die('Failed to load youtube TTS list url '.$listUrl);
	}
	if (!$responseXml = simplexml_load_string($responseText)) {
		die(' Failed to decode Xml for '.$responseText);
	}
	if (!$responseXml->track) {
		// no tracks found for this video (happens sometimes even though
		// YT search API says they do have captions)
		return $tracks;
	}
	
	foreach ($responseXml->track as $track) {
		$score = 0;
		if ((string)$track['lang_default'] === 'true') {
			// we like defaults
			$score += 50;
		}
		
		$tracks[] = [
		    'score' => $score,
		    'id' => (string)$track['id'],
		    'lang' => (string)$track['lang_code'],
		    'kind' => (string)$track['kind'],
		    'name' => (string)$track['name']
		];
	}
	
	// sort tracks by descending score
	usort($tracks, function($a, $b) {
		if ($a['score'] == $b['score']) {
			return 0;
		}
		return ($a['score'] > $b['score']) ? -1 : 1;
	});
	return $tracks;
}
/**
 * Given a base URL and a track, attempt to request Closed Captions
 * 
 * If found, decode and strip tags from response, and join each line
 * with a "<br />" and a "\n"
 * 
 * @param string $baseUrl Base URL found by calling getBaseClosedCaptionsUrl()
 * @param array $track Specific track to request
 * @return string Closed captions text for video & track combo
 */
function getClosedCaptionText($baseUrl, array $track) {
	$captionsUrl = $baseUrl."&type=track&lang={$track['lang']}&name=".urlencode($track['name'])."&kind={$track['kind']}&fmt=1";
	if (!$responseText = file_get_contents($captionsUrl)) {
		die('Failed to load youtube TTS captions track url '.$captionsUrl);
	}
	if (!$responseXml = simplexml_load_string($responseText)) {
		die(' Failed to decode Xml for '.$responseText);
	}
	if (!$responseXml->text) {
		die(' Bad XML structure for '.$captionsUrl.' : '.$responseText);
	}
	
	$videoText = [];
	foreach ($responseXml->text as $textNode) {
		if ($text = trim((string)$textNode)) {
			$videoText[] = htmlspecialchars_decode(strip_tags((string)$textNode), ENT_QUOTES);
		}
	}
	
	return implode("<br />\n", $videoText);
}

//echo "SOMETHING!";
//echo "<br>";
//echo "<pre>";
//var_dump(getClosedCaptionsForVideo("NSwlHnSs0Yk"));
//echo "</pre>";