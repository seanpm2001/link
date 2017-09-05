<?php

$url = 'https://api.crowdin.com/api/project/phalcon-documentation/status?key=' . getenv('CROWDIN_KEY');

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, ['json' => 1]);

try {
  $result = curl_exec($ch);
  if (curl_errno($ch)) {
    throw new Exception(curl_error($ch));
  }
  curl_close($ch);

  print_r('Successfully Contacted Crowdin' . PHP_EOL);

  $json = json_decode($result);

  if (isset($json->success) && !$json->success) {
    throw new Exception($json->error->message);
  }

  print_r('Recieved Successful Response from Crowdin' . PHP_EOL);

  $langs = [];
  foreach ($json as $lang) {
    $langs[] = $lang->code;
  }

  $langs_code = 'return ' . var_export($langs, true) . ';';

  unlink('langs.php');
  $fp = fopen('langs.php', 'w');
  fwrite($fp, '<?php');
  fwrite($fp, PHP_EOL . PHP_EOL);
  fwrite($fp, '# FILE GENERATED AUTOMATICALLY CHANGES WILL NOT BE MAINTAINED' . PHP_EOL);
  fwrite($fp, '# RUN $ ./deploy TO REGENERATE' . PHP_EOL);
  fwrite($fp, $langs_code . PHP_EOL);
  fclose($fp);
} catch (Exception $e) {
  echo $e->getMessage() . PHP_EOL;
  echo $e->getTraceAsString(). PHP_EOL;
}
