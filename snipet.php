<?php
//
//autor Alexandr
//url https://pechenki.site
// Get script options
$tpl = $modx->getOption('tpl', $scriptProperties, 'FileItemTpl');




// you can take it here https://freelancehunt.com/my/api
$api_token = 'xxxxxxxx';
$api_secret = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
function signsa($api_secret, $url, $method, $post_params = '') {
    return base64_encode(hash_hmac("sha256", $url.$method.$post_params, $api_secret, true));
}
$url = "https://api.freelancehunt.com/profiles/Pechenki_PSD?include=reviews";
$method = "GET";
$signature = signsa($api_secret, $url, $method); // реализацию функции смотрите выше
$curl = curl_init();
curl_setopt_array($curl, [
    //CURLOPT_HEADER       => 1, // позволяет просмотреть заголовки ответа сервера при необходимости отладки
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_USERPWD        => $api_token . ":" . $signature,
    CURLOPT_URL            => $url
]);
$reviers = curl_exec($curl);
//print_r($return);

 $rev = json_decode($reviers,true);
curl_close($curl);

$list = array();
// начало рабооты 
for ($i = 0; $i < count($rev['reviews']); $i++)
    {
        $itemArr['url'] = $rev['reviews'][$i]['project']['url'];
        $itemArr['name'] = $rev['reviews'][$i]['project']['name'];
        $itemArr['budget_amount'] =  $rev['reviews'][$i]['project']['budget_amount'];
        $itemArr['budget_currency_code'] = $rev['reviews'][$i]['project']['budget_currency_code'];
        $itemArr['review_comment'] = $rev['reviews'][$i]['review_comment'];
        $itemArr['from_avatar'] = $rev['reviews'][$i]['from']['avatar'];
        $itemArr['from_fname'] = $rev['reviews'][$i]['from']['fname'];
        $itemArr['from_url']  =  $rev['reviews'][$i]['from']['url'];
        

        $list[] = $modx->getChunk($tpl, $itemArr);
    }
  unset($i);
  
 $output = implode($outputSeparator, $list);
if (!empty($toPlaceholder)) {
	// If using a placeholder, output nothing and set output to specified placeholder
	$modx->setPlaceholder($toPlaceholder, $output);

	return '';
}

return $output;
