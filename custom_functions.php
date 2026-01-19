<?php
/**
 * Returns array of people ids - who are friends
 * 
 * @param int - person id
 * 
 * @return int[]
 * 
 */
function getFriends($id){
    global $db;
    $ids = [];

    $read = $db->query("SELECT * FROM friends WHERE friend1 = $id OR friend2 = $id");
    while ($row = $read->fetch_assoc()) {
        if($row['friend1'] == $id){
            array_push($ids, $row['friend2']);
        }
        else{
            array_push($ids, $row['friend1']);
        }
    }

    return array_unique($ids);
}

function person_name($id){
    return getData("people", ['id' => $id])['name'] ?? "Unknown";
}

function getGroups($id){
    global $db;
    $ids = [];

    $read = $db->query("SELECT * FROM group_members WHERE member = $id");
    while ($row = $read->fetch_assoc()) {
        array_push($ids, $row['group']);
    }

    return $ids;
}
/*
function update_last_message($user1, $user2, $message_id){
    $check = getData("last_message", ['user1' => $user1, 'user2' => $user2]);
	if($check == null){
		db_insert("last_message", [
			'user1' => $user1,
			'user2' => $user2,
			'message' => $message_id,
		]);

		db_insert("last_message", [
			'user1' => $user2,
			'user2' => $user1,
			'message' => $message_id,
		]);
	}
	else{
		db_update("last_message", [
			'message' => $message_id,
		], ['id' => $check['id']]);

		db_update("last_message", [
			'message' => $message_id,
		], ['user1' => $user2, 'user2' => $user1]);
	}
}*/

function extractBetweenDelimiters($string, $startDelimiter, $endDelimiter) {
    // Escape special characters in delimiters for use in regex
    $startDelimiter = preg_quote($startDelimiter, '/');
    $endDelimiter = preg_quote($endDelimiter, '/');

    // Build the regex pattern
    $pattern = "/{$startDelimiter}(.*?){$endDelimiter}/";

    // Perform the regex match
    if (preg_match($pattern, $string, $matches)) {
        return $matches[1]; // Return the content between delimiters
    } else {
        return null; // Return null if no match is found
    }
}

function permission_allowed($code,$user_id,$type){
    global $db;
    $permission_data = getData("permissions", ['code' => $code]);

    if ($permission_data == null) return false;

    $check = getData("permission_progress", [
        'key' => $permission_data['id'],
        'value' => $user_id,
        'type' => $type
    ]);

    return $check != null;
}

function is_notification_allowed($code, $student_id){
    return (getData("student_notifications",[
        'student' => $student_id,
        'notification' => $code
    ]) != null);
}

function getFirstNumber($str){
    if (preg_match('/(\d+)/', $str, $matches)) {
        $number4 = $matches[1];
        return $number4;
    }
    return null;
}

function convert_math($input){
    if(Strings::contains($input, "(")){
        return another_function($input);
    }
    $chars = explode(" ", $input);

    $dom = new DOMDocument();
    $root = $dom->createElement("math");
    $dom->appendChild($root);

    $output = "";
    foreach ($chars as $char) {
        $number = getFirstNumber($char);
        $remaining = substr($char, strlen($number));

        $node = $dom->createElement("mrow");
        $node->appendChild($dom->createElement("mn", $number));

        //check for superscripts
        if(Strings::contains($remaining, "^")){
            $remaining = str_replace("^", " ", $remaining);
            $letters = explode(" ", $remaining);
            $remaining = $letters[0];
            $superscript = $letters[1];

            $superscript_node = $dom->createElement("msup");
            $superscript_node->appendChild($dom->createElement("mi", $remaining));
            $superscript_node->appendChild($dom->createElement("mn", $superscript));
            $node->appendChild($superscript_node);
        }
        else{
            $node->appendChild($dom->createElement("mi", $remaining));
        }

        $node->appendChild($dom->createElement("ms", "&nbsp;"));
        $root->appendChild($node);
    }
    return $dom->saveXML();
}

function another_function($input){
    if(Strings::contains($input, "(")){
        $content = extractBetweenDelimiters($input, "(", ")");
        $remaining = str_replace("(".$content.")", "", $input);
        return $content;
    }
    else{
        //convert to math
        return convert_math($input);
    }
}

function getRating($id){
    global $db;
    $rating = (float)$db->query("SELECT AVG(rating) FROM subject_rating WHERE subject = '$id'")->fetch_assoc()['AVG(rating)'];
    return $rating;
}

function is_subscribed($user){
    $data = getData("subscriptions", [
        'user' => $user,
        'status' => 'active',
        'end_date >=' => time()
    ]);
    return $data != null;
}

function getLessonData($lesson_id){
    global $db;
    $lesson = getData("lessons", ['id' => $lesson_id]);
    if($lesson == null){
        return null;
    }
    $lesson['subject_data'] = getData("subjects", ['id' => $lesson['subject']]) ?: db_default("subjects");
    $lesson['teacher_data'] = getData("staff", ['id' => $lesson['teacher']]) ?: db_default("staff");
    $lesson['date'] = date('d-M-Y', (int)$lesson['date_added']);
    $lesson['ago'] = time_ago($lesson['date_added']);
    $lesson['comments'] = (int)$db->query("SELECT COUNT(id) FROM comments WHERE ref = '{$lesson['id']}' ")->fetch_array()[0];
    $lesson['opened'] = (int)$db->query("SELECT COUNT(id) FROM progress WHERE ref = '{$lesson['id']}' AND type = 'lesson' ")->fetch_array()[0];
    $lesson['attended'] = $db->query("SELECT DISTINCT student FROM progress WHERE ref = '{$lesson['id']}' AND type = 'lesson' ")->num_rows;
    $lesson['attachments'] = getAll("attachments", ['ref' => $lesson['id'], 'type' => 'lesson']);
    $lesson['progress'] = $lesson_progress_value[$lesson['id']] ?? 0;
    return $lesson;
}

function getLessonOrderingData($ordering_id){
    global $db;
    $data = getData("lesson_ordering", ['id' => $ordering_id]);
    return $data;
}

function get_chat_friends($id){
    global $db;

    $rows = db_query("SELECT * FROM last_message WHERE user1 = '$id' ORDER BY message DESC");

    $people_store = getAll("users", "id", [
        'id' => array_column($rows, "user2")
    ]);

    $messages = getAll("messages", "id", [
        'id' => array_column($rows, "message")
    ]);

    $default_person = db_default("users");
    $default_message = db_default("messages");
    //JSON::dump($people_store);
    //exit;

    
    foreach ($rows as &$row) {
        $row['user_data'] = $people_store[$row['user2']] ?? $default_person;
        $row['message_data'] = $messages[$row['message']] ?? $default_message;
        $row['user'] = $row['user_data']['id'];
    }

    return $rows;
}

function update_last_message($user1,$user2, $message_id){
    $check = getData("last_message", [
        'user1' => $user1, 
        'user2' => $user2,
    ]);

    if($check == null){
        db_insert("last_message", [
            'user1' => $user1,
            'user2' => $user2,
            'message' => $message_id,
        ]);

        db_insert("last_message", [
            'user1' => $user2,
            'user2' => $user1,
            'message' => $message_id,
        ]);
    }
    else{
        db_update("last_message", [
            'message' => $message_id,
        ], ['id' => $check['id']]);

        db_update("last_message", [
            'message' => $message_id,
        ], [
            'user1' => $user2, 
            'user2' => $user1, 
        ]);
    }
}

function get_people($ids){
    //return associative array
    $people = getAll("people", "id", [
        'id' => $ids
    ]);

    $students = [];
    $staff = [];

    foreach ($people as $person => $row) {
        if ($row['type'] == "student") {
            $students[] = $row['user'];
        }
        else{
            $staff[] = $row['user'];
        }
    }

    $students_store = getAll("students", "id", [
        'id' => $students
    ]);

    $staff_store = getAll("staff", "id", [
        'id' => $staff
    ]);

    $default_person = default_person();

    $store = [];
    foreach ($ids as $id) {
        $store[$id] = $people[$id] ?? $default_person;
        if($store[$id]['type'] == "student"){
            if(!isset($students_store[$store[$id]['user']])) continue;
            $store[$id]['user_data'] = $students_store[$store[$id]['user']];
            $store[$id]['user_data']['picture'] = $store[$id]['user_data']['photo'];
            unset($store[$id]['user_data']['password']);
        }
        else{
            if(!isset($staff_store[$store[$id]['user']])) continue;
            $store[$id]['user_data'] = $staff_store[$store[$id]['user']];
            unset($store[$id]['user_data']['password']);
        }
    }

    return $store;
}

function register_people(){
    global $db;

    //students 
    $students = db_query("SELECT id,name FROM students WHERE id NOT IN (SELECT user FROM people WHERE type = 'student')");

    if(count($students) > 0){
        $chunks = array_chunk($students, 100);
        foreach ($chunks as $chunk) {
            $values = [];
            foreach ($chunk as $student_row) {
                $student_name = $db->real_escape_string($student_row['name']);
                $values[] = "('{$student_row['id']}', 'student', '$student_name')";
            }
            if (count($values)>0) {
                $db->query("INSERT INTO people (user, type, name) VALUES " . implode(", ", $values));
            }
        }
    }

    //staff
    $staff = db_query("SELECT id,name FROM staff WHERE id NOT IN (SELECT user FROM people WHERE type = 'staff')");

    if(count($students) > 0){
        $chunks = array_chunk($staff, 100);
        foreach ($chunks as $chunk) {
            $values = [];
            foreach ($chunk as $staff_row) {
                $staff_name = $db->real_escape_string($staff_row['name']);
                $values[] = "('{$staff_row['id']}', 'staff', '$staff_name')";
            }
            $db->query("INSERT INTO people (user, type, name) VALUES " . implode(", ", $values));
        }
    }
}

function default_person(){
    $data = db_default("people");
    $data['user_data'] = db_default("staff");

    return $data;
}

function update_student_profile($data, $student){
    $previous = getData("student_profiles", [
        'student' => $student
    ]);

    if($data == null){
        db_insert("student_profiles", [
            ...$data,
            'student' => $student
        ]);
    }
    else{
        db_update("student_profiles", $data, [
            'student' => $student
        ]);
    }
}

function verify_payment_paychangu($tx_ref, $secret_key){
    global $config;
    // --- API Endpoint ---
    //$api_url = "https://api.paychangu.com/verify-payment/" . $tx_ref;
    //local for testing
    $api_url = $config['environment'] == "development" ? "https://wikimalawi.com/paychangu/handler.php?verify=" . $tx_ref : "https://api.paychangu.com/verify-payment/" . $tx_ref;

    // --- Initialize cURL ---
    $ch = curl_init();

    // --- Set cURL Options ---

    // Set the URL for the request
    curl_setopt($ch, CURLOPT_URL, $api_url);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

    // Set custom headers
    $headers = [
        "Accept: application/json",
        "Authorization: Bearer " . $secret_key
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    // Return the response as a string instead of outputting it directly
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    // --- Execute cURL Request ---
    $response = curl_exec($ch);

    // --- Check for cURL Errors ---
    if (curl_errno($ch)) {
        $error_msg = curl_error($ch);
        return  "cURL Error: " . $error_msg . "\n";
    } else {
        return $response;
    }

    // --- Close cURL Session ---
    curl_close($ch);
}

function get_youtube_video_data($video_id, $api_key) {
    // Define the required parts and construct the API URL
    $parts = 'snippet,contentDetails,statistics';
    $api_url = "https://www.googleapis.com/youtube/v3/videos?id={$video_id}&key={$api_key}&part={$parts}";
    
    // Attempt to retrieve the data
    $response = @file_get_contents($api_url);

    if ($response === FALSE) {
        return ['error' => 'Failed to retrieve data from API.'];
    }

    // Decode the JSON response into a PHP array
    $data = json_decode($response, true);

    // Check if video details were returned (the 'items' array is not empty)
    if (empty($data['items'])) {
        return ['error' => 'Video not found or API limits exceeded.'];
    }

    // Return the first video item (since we requested only one ID)
    return $data['items'][0];
}

function HtmlError($message){
    ?>
    <div class="ui message red"><?=$message;?></div>
    <?php
}

function update_lesson_data($lesson_id, $data){
    $lesson_data = getData("lessons", ['id' => $lesson_id]);

    if($lesson_data == null){
        return;
    }

    if (str_starts_with($lesson_data['ref_data'], '{')) {
        $json = json_decode($lesson_data['ref_data'], true);
    }
    else{
        $json = [
            'comments' => 0,
            'opened' => 0,
            'attended' => 0,
            'attachments' => 0
        ];
    }

    foreach ($data as $key => $value) {
        $json[$key] = $value;
    }
    
    db_update("lessons", [
        'ref_data' => json_encode($json)
    ], ['id' => $lesson_id]);

    return;
}

function replace_pattern($template, $context){
    //$template = "Total: {{ price * quantity }} USD, Tax: {{ (price * quantity) * taxRate }}";

    // Variables for computation
    // $context = [
    //     'price' => 100,
    //     'quantity' => 2,
    //     'taxRate' => 0.2
    // ];

    // Regex to find all {{ ... }} blocks
    $pattern = '/{{\s*(.*?)\s*}}/';

    $result = preg_replace_callback($pattern, function ($matches) use ($context) {
        $expression = $matches[1];

        // Extract variables into local scope
        extract($context);

        // Evaluate the expression safely
        try {
            // Note: eval is dangerous if user input is not trusted.
            // Wrap it safely and return the result.
            $result = eval("return $expression;");
            return $result;
        } catch (Throwable $e) {
            return "[Error]";
        }

    }, $template);

    return $result;
}

function get_fortnight($timeStamp){
    if (date('d', $timeStamp) < 15) {
        return [
            strtotime(date('Y-m-', $timeStamp)."01"),
            strtotime(date('Y-m-')."14 23:59:59")
        ];
    }
    else{
        return [
            strtotime(date('Y-m-', $timeStamp)."15"),
            strtotime(date('Y-m-t', $timeStamp)." 23:59:59")
        ];
    }
}

function league_score($student, $start_time, $end_time){
    $academic = Academic::get($student, $start_time, $end_time); 

    $engagement = Engagement::get($student, $start_time, $end_time); 

    $discipline = Discipline::get($student, $start_time, $end_time); 

    $progress_score = (0.5 * $academic) + (0.3 * $engagement) + (0.2 * $discipline);

    return $progress_score;
}

//echo json_encode(get_fortnight(time()));

function get_email_alt_body($html) {
    error_reporting(E_ERROR | E_PARSE);

    $dom = new DOMDocument();
    @$dom->loadHTML($html, LIBXML_NOERROR | LIBXML_NOWARNING);
    $dom->preserveWhiteSpace = false;
    $dom->formatOutput = true;
    $dom->normalize();

    // Tags we care about
    $tags = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p'];
    $output = [];

    foreach ($tags as $tag) {
        $elements = $dom->getElementsByTagName($tag);
        foreach ($elements as $el) {
            $text = trim($el->textContent);
            if (!empty($text)) {
                $output[] = $text;
                // stop after capturing 2 items
                if (count($output) >= 2) {
                    break 2;
                }
            }
        }
    }

    // Fallback if nothing found
    if (empty($output)) {
        $alt = strip_tags($html);
        $alt = preg_replace('/\s+/', ' ', $alt);
        return trim(substr($alt, 0, 200)); // limit for safety
    }

    return implode("\n\n", $output);
}
?>