<?php

	//UPLOAD IMAGE TO SERVER
	$target_path = "./";
	//$target_path = $target_path . basename($_FILES['image']['name']);
	$target_path = $target_path . 'image.jpg';
	/*if (filesize($_FILES['image']['tmp_name']) > 1000 && move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
	    //echo "The file ".  basename( $_FILES['image']['name'])." has been uploaded\n";
	} else {
		die("There was an error uploading the file\n");
	}*/

	// Key for Google Vision API
	$VISION_API_KEY = "AIzaSyDtLbMLpOwoE2Y3LnYqtoLeFCj2EJI0oN8";

	// Keys for Google Custom Search URLs
	$GCSE_API_KEY = "AIzaSyDtLbMLpOwoE2Y3LnYqtoLeFCj2EJI0oN8";
	$GCSE_SEARCH_ENGINE_ID = "007324269943774541495:g_p2gafcwy8";

	// URL of hosted script
	$HOST_URL = "www.url.com/screenshot.png";


	//DETECT TEXT WITH JSON REQUEST TO GOOGLE
	$JSON_TEST = '{
	      "requests": [
	        {
	          "image": {
	            "source": {
	              "imageUri": "'.$HOST_URL.'"
	            }
	          },
	          "features": [
	            {
	              "type": "TEXT_DETECTION",
	              "maxResults": 1
	            }
	          ]
	        }
	      ]
	    }';

	/*
		NOTE: IF YOU ARE HOSTING ON LOCALHOST, 
		YOU WILL NEED TO SEND THE IMAGE TO GOOGLE THROUGH THE FOLLOWING
		COMMENTED OUT WAY. IT IS SLIGHTLY SLOWER.
	*/

	
	$base64 = base64_encode(file_get_contents($target_path));
	    $JSON_TEST = '{
	      "requests": [
	        {
	          "image": {
	              "content": "'.$base64.'"
	          },
	          "features": [
	            {
	              "type": "TEXT_DETECTION",
	              "maxResults": 1
	            }
	          ]
	        }
	      ]
	    }';
	

	// REQUEST TEXT FROM GOOGLE VISION
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "https://vision.googleapis.com/v1/images:annotate?key=".$VISION_API_KEY);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $JSON_TEST);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$server_output = curl_exec($ch);



	// GET TEXT OUTPUT
	$resp = json_decode($server_output, true);
	$text = $resp["responses"][0]["textAnnotations"][0]["description"];
	$text = strtolower($text);
	
	// Error check
	if (!$text || $text == "")
		die("There was an unexpected error");

	list($question, $listofanswers) = explode('?', $text, 2);

	// Does question contain the word "not" or "never"
	$isNotQuestion = (stripos($question, " not ") !== FALSE || stripos($question, " never ") !== FALSE);
	if ($isNotQuestion){
		echo "NOT QUESTION\n\n";
	}

	// Initialize array for overall answer
	$overallAnswer = array();

	// Parse question for better results
	$question = str_replace("which of these", "what", $question);
	$question = str_replace(" not ", " ", $question);
	$question = str_replace(" never ", " ", $question);
	$question = str_replace("\n", " ", $question);
	//echo $question . "?" . "\n\n";

	// Parse answers

    	// Replace " / " with " and "
    	// Common occurance in questions
    	$listofanswers = str_replace(" / ", " and ", trim($listofanswers));
	$answers = explode("\n", $listofanswers);

	// UNCOMMENT BELOW IF THERE ARE UNEXPECTED ISSUES
	if (count($answers) >= 4) {
	    $answers = array_values(array_filter(explode("\n", preg_replace('/\d/', '', trim($listofanswers)))));
	}
	if (count($answers) <= 1) {
	    $answers = explode(" ", trim($listofanswers));
	}

// ------------------- ATTEMPT 1: GOOGLE QUESTION -------------------
	$query1 = $question . "? " . $answers[0];
	$url1 = "https://www.googleapis.com/customsearch/v1?key=" . $GCSE_API_KEY . "&cx=" . $GCSE_SEARCH_ENGINE_ID . "&q=" . urlencode($query1);

	// Send request googling question
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
	$result1 = curl_exec($ch);
	$result1 = json_decode($result1, true);
	echo $result1['queries']['request'][0]['totalResults'] . "\n\n";


	$query1 = $question . "? " . $answers[1];
	$url1 = "https://www.googleapis.com/customsearch/v1?key=" . $GCSE_API_KEY . "&cx=" . $GCSE_SEARCH_ENGINE_ID . "&q=" . urlencode($query1);

	// Send request googling question
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
	$result1 = curl_exec($ch);
	$result1 = json_decode($result1, true);
	echo $result1['queries']['request'][0]['totalResults'] . "\n\n";

	
		$query1 = $question . "? " . $answers[2];
	$url1 = "https://www.googleapis.com/customsearch/v1?key=" . $GCSE_API_KEY . "&cx=" . $GCSE_SEARCH_ENGINE_ID . "&q=" . urlencode($query1);

	// Send request googling question
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    	curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
    	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
	$result1 = curl_exec($ch);
	$result1 = json_decode($result1, true);
	echo $result1['queries']['request'][0]['totalResults'] . "\n\n";
	
